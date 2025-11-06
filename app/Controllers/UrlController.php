<?php

namespace Hexlet\Code\Controllers;

use Exception;
use Hexlet\Code\Config\Messages;
use Hexlet\Code\Config\Routes;
use Hexlet\Code\Models\UrlAnalysis;
use Hexlet\Code\Repositories\UrlRepository;
use Hexlet\Code\Repositories\UrlAnalysisRepository;
use Hexlet\Code\Services\FlashService;
use Hexlet\Code\Services\UrlNormalizer;
use Hexlet\Code\Services\UrlCheckerService;
use Hexlet\Code\Validators\UrlValidator;
use Hexlet\Code\Models\Url;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class UrlController
{
    private UrlRepository $urlRepository;
    private UrlAnalysisRepository $urlAnalysisRepository;
    private UrlValidator $validator;
    private UrlNormalizer $normalizer;
    private FlashService $flash;
    private UrlCheckerService $checker;

    public function __construct(
        UrlRepository $urlRepository,
        UrlAnalysisRepository $urlAnalysisRepository,
        UrlValidator $validator,
        UrlNormalizer $normalizer,
        FlashService $flash,
        UrlCheckerService $checker
    ) {
        $this->urlRepository = $urlRepository;
        $this->urlAnalysisRepository = $urlAnalysisRepository;
        $this->validator = $validator;
        $this->normalizer = $normalizer;
        $this->flash = $flash;
        $this->checker = $checker;
    }

    public function store(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $urlData = $data['url'] ?? [];
        $urlName = trim($urlData['name'] ?? '');

        $errors = $this->validator->validate($urlName);

        if (!empty($errors)) {
            return Twig::fromRequest($request)->render(
                $response->withStatus(422),
                'index.twig',
                [
                    'errors' => $errors,
                    'url' => $urlData,
                    'flash' => [],
                    'currentRoute' => Routes::HOME
                ]
            );
        }

        $normalizedUrl = $this->normalizer->normalize($urlName);

        try {
            $existingUrl = $this->urlRepository->findByName($normalizedUrl);

            if ($existingUrl !== null) {
                $this->flash->set('success', Messages::URL_ALREADY_EXISTS);

                return $this->redirectToShow($request, $response, $existingUrl->getId());
            }

            $url = new Url(['name' => $normalizedUrl]);

            $this->urlRepository->save($url);

            $this->flash->set('success', Messages::URL_ADDED);

            return $this->redirectToShow($request, $response, $url->getId());
        } catch (Exception $e) {
            $this->flash->set('error', Messages::ERROR_SAVING_URL);

            return $this->redirectToIndex($request, $response);
        }
    }

    public function index(Request $request, Response $response): Response
    {
        try {
            $urls = $this->urlRepository->findAllWithLatestAnalysis();

            $params = [
                'urls' => $urls,
                'flash' => $this->flash->getAll(),
                'currentRoute' => Routes::URLS_INDEX
            ];

            return Twig::fromRequest($request)->render($response, 'urls.twig', $params);
        } catch (Exception $e) {
            return $response->withStatus(500)->write(Messages::ERROR_LOADING_DATA);
        }
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];

        try {
            $url = $this->urlRepository->find($id);

            if ($url === null) {
                return $response->withStatus(404)->write(Messages::URL_NOT_FOUND);
            }

            $checks = $this->urlAnalysisRepository->findByUrlId($id);

            $params = [
                'url' => [
                    'id' => $url->getId(),
                    'name' => $url->name,
                    'createdAt' => $url->getCreatedAt()
                ],
                'checks' => array_map(fn($check) => [
                    'id' => $check->getId(),
                    'responseCode' => $check->responseCode,
                    'h1' => $check->h1,
                    'title' => $check->title,
                    'description' => $check->description,
                    'createdAt' => $check->getCreatedAt()
                ], $checks),
                'flash' => $this->flash->getAll(),
                'currentRoute' => Routes::URLS_SHOW
            ];

            return Twig::fromRequest($request)->render($response, 'show.twig', $params);
        } catch (Exception $e) {
            return $response->withStatus(500)->write(Messages::ERROR_LOADING_DATA);
        }
    }

    public function createCheck(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];

        try {
            $url = $this->urlRepository->find($id);

            if ($url === null) {
                return $response->withStatus(404)->write(Messages::URL_NOT_FOUND);
            }

            $checkResult = $this->checker->check($url->name);

            if ($checkResult === null) {
                $this->flash->set('error', Messages::CHECK_NETWORK_ERROR);

                return $this->redirectToShow($request, $response, $id);
            }

            $analysis = new UrlAnalysis([
                'url_id' => $id,
                'response_code' => $checkResult['response_code'],
                'h1' => $checkResult['h1'],
                'title' => $checkResult['title'],
                'description' => $checkResult['description'],
            ]);

            $this->urlAnalysisRepository->save($analysis);

            $this->flash->set('success', Messages::CHECK_CREATED);

            return $this->redirectToShow($request, $response, $id);
        } catch (Exception $e) {
            $this->flash->set('error', Messages::ERROR_CREATING_CHECK);

            return $this->redirectToShow($request, $response, $id);
        }
    }

    private function redirectToShow(Request $request, Response $response, int $id): Response
    {
        $url = RouteContext::fromRequest($request)->getRouteParser()->urlFor(Routes::URLS_SHOW, ['id' => $id]);

        return $response->withHeader('Location', $url)->withStatus(302);
    }

    private function redirectToIndex(Request $request, Response $response): Response
    {
        $url = RouteContext::fromRequest($request)->getRouteParser()->urlFor(Routes::HOME);

        return $response->withHeader('Location', $url)->withStatus(302);
    }
}
