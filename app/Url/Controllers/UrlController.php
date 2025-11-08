<?php

namespace Hexlet\Code\Url\Controllers;

use Exception;
use Hexlet\Code\Common\Services\FlashService;
use Hexlet\Code\Config\ExceptionsTexts;
use Hexlet\Code\Config\Messages;
use Hexlet\Code\Config\Routes;
use Hexlet\Code\Url\Exceptions\UrlStoreValidationException;
use Hexlet\Code\Url\Models\Url;
use Hexlet\Code\Url\Repositories\UrlRepository;
use Hexlet\Code\Url\Services\UrlCheckerService;
use Hexlet\Code\Url\Services\UrlNormalizer;
use Hexlet\Code\Url\Validators\UrlValidator;
use Hexlet\Code\UrlAnalysis\Models\UrlAnalysis;
use Hexlet\Code\UrlAnalysis\Repositories\UrlAnalysisRepository;
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

        /** @var array{url?: array{name?: string}} $data */
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

        if (is_string($normalizedUrl)) {
            try {
                $existingUrl = $this->urlRepository->findByName($normalizedUrl);

                if ($existingUrl !== null) {
                    $this->flash->set('success', Messages::URL_ALREADY_EXISTS);
                    $existingUrlId = $existingUrl->getId();

                    if ($existingUrlId === null) {
                        throw new UrlStoreValidationException(ExceptionsTexts::URL_ID_IS_NULL_AT_CREATION);
                    }

                    $result = $this->redirectToShow($request, $response, $existingUrlId);
                } else {
                    $url = new Url(['name' => $normalizedUrl]);

                    $this->urlRepository->save($url);
                    $this->flash->set('success', Messages::URL_ADDED);

                    $newUrlId = $url->getId();

                    if ($newUrlId === null) {
                        throw new UrlStoreValidationException(ExceptionsTexts::URL_ID_IS_NULL_AFTER_SAVE);
                    }

                    $result = $this->redirectToShow($request, $response, $newUrlId);
                }
            } catch (Exception $e) {
                $this->flash->set('error', Messages::ERROR_SAVING_URL);
                $result = $this->redirectToIndex($request, $response);
            }
        } else {
            $this->flash->set('error', Messages::ERROR_SAVING_URL);
            $result = $this->redirectToIndex($request, $response);
        }

        return $result;
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
            $response->getBody()->write(Messages::ERROR_LOADING_DATA);

            return $response->withStatus(500);
        }
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];

        try {
            $url = $this->urlRepository->find($id);

            if ($url === null) {
                $response->getBody()->write(Messages::URL_NOT_FOUND);

                return $response->withStatus(404);
            }

            $checks = $this->urlAnalysisRepository->findByUrlId($id);

            $params = [
                'url' => [
                    'id' => $url->getId(),
                    'name' => $url->getName(),
                    'createdAt' => $url->getCreatedAt()
                ],
                'checks' => array_map(fn($check) => [
                    'id' => $check->getId(),
                    'responseCode' => $check->getResponseCode(),
                    'h1' => $check->getH1(),
                    'title' => $check->getTitle(),
                    'description' => $check->getDescription(),
                    'createdAt' => $check->getCreatedAt()
                ], $checks),
                'flash' => $this->flash->getAll(),
                'currentRoute' => Routes::URLS_SHOW
            ];

            return Twig::fromRequest($request)->render($response, 'show.twig', $params);
        } catch (Exception $e) {
            $response->getBody()->write(Messages::ERROR_LOADING_DATA);

            return $response->withStatus(500);
        }
    }

    public function createCheck(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];

        try {
            $url = $this->urlRepository->find($id);

            if ($url === null) {
                $response->getBody()->write(Messages::URL_NOT_FOUND);

                $result = $response->withStatus(404);
            } else {
                $checkResult = $this->checker->check($url->getName());

                if ($checkResult === null) {
                    $this->flash->set('error', Messages::CHECK_NETWORK_ERROR);
                } else {
                    $analysis = new UrlAnalysis([
                        'url_id' => $id,
                        'response_code' => $checkResult['response_code'],
                        'h1' => $checkResult['h1'],
                        'title' => $checkResult['title'],
                        'description' => $checkResult['description'],
                    ]);

                    $this->urlAnalysisRepository->save($analysis);
                    $this->flash->set('success', Messages::CHECK_CREATED);
                }

                $result = $this->redirectToShow($request, $response, $id);
            }
        } catch (Exception $e) {
            $this->flash->set('error', Messages::ERROR_CREATING_CHECK);

            $result = $this->redirectToShow($request, $response, $id);
        }

        return $result;
    }

    private function redirectToShow(Request $request, Response $response, int $id): Response
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        $url = $routeParser->urlFor(Routes::URLS_SHOW, ['id' => (string) $id]);

        return $response->withHeader('Location', $url)->withStatus(302);
    }

    private function redirectToIndex(Request $request, Response $response): Response
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();

        $url = $routeParser->urlFor(Routes::HOME);

        return $response->withHeader('Location', $url)->withStatus(302);
    }
}
