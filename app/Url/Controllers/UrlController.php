<?php

namespace Hexlet\Code\Url\Controllers;

use Exception;
use Hexlet\Code\Config\ExceptionsTexts;
use Hexlet\Code\Config\Messages;
use Hexlet\Code\Config\Routes;
use Hexlet\Code\Url\Exceptions\UrlStoreValidationException;
use Hexlet\Code\Url\Services\UrlService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Flash\Messages as FlashMessages;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class UrlController
{
    private UrlService $urlService;
    private FlashMessages $flash;

    public function __construct(UrlService $urlService, FlashMessages $flash)
    {
        $this->urlService = $urlService;
        $this->flash = $flash;
    }

    public function index(Request $request, Response $response): Response
    {
        try {
            $urls = $this->urlService->findAllUrlsWithLatestAnalysis();

            $params = [
                'urls' => $urls,
                'flash' => $this->flash->getMessages(),
                'currentRoute' => Routes::URLS_INDEX
            ];

            return Twig::fromRequest($request)->render($response, 'urls/urls.twig', $params);
        } catch (Exception $e) {
            $response->getBody()->write(Messages::ERROR_LOADING_DATA);

            return $response->withStatus(500);
        }
    }

    public function store(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        /** @var array{url?: array{name?: string}} $data */
        $urlData = $data['url'] ?? [];
        $urlName = trim($urlData['name'] ?? '');

        $errors = $this->urlService->validateUrl($urlName);

        if (!empty($errors)) {
            return Twig::fromRequest($request)->render(
                $response->withStatus(422),
                'urls/index.twig',
                [
                    'errors' => $errors,
                    'url' => $urlData,
                    'flash' => [],
                    'currentRoute' => Routes::HOME
                ]
            );
        }

        $normalizedUrl = $this->urlService->normalizeUrl($urlName);

        if (is_string($normalizedUrl)) {
            try {
                $existingUrl = $this->urlService->findUrlByName($normalizedUrl);

                if ($existingUrl !== null) {
                    $this->flash->addMessage('success', Messages::URL_ALREADY_EXISTS);
                    $existingUrlId = $existingUrl->getId();

                    if ($existingUrlId === null) {
                        throw new UrlStoreValidationException(ExceptionsTexts::URL_ID_IS_NULL_AT_CREATION);
                    }

                    $result = $this->redirectToShow($request, $response, $existingUrlId);
                } else {
                    $url = $this->urlService->createUrl($normalizedUrl);
                    $this->flash->addMessage('success', Messages::URL_ADDED);

                    $urlId = $url->getId();

                    if ($urlId === null) {
                        throw new UrlStoreValidationException(ExceptionsTexts::URL_ID_IS_NULL_AT_CREATION);
                    }

                    $result = $this->redirectToShow($request, $response, $urlId);
                }
            } catch (Exception $e) {
                $this->flash->addMessage('error', Messages::ERROR_SAVING_URL);
                $result = $this->redirectToIndex($request, $response);
            }
        } else {
            $this->flash->addMessage('error', Messages::ERROR_SAVING_URL);
            $result = $this->redirectToIndex($request, $response);
        }

        return $result;
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];

        try {
            $url = $this->urlService->findUrl($id);

            if ($url === null) {
                $response->getBody()->write(Messages::URL_NOT_FOUND);

                return $response->withStatus(404);
            }

            $checks = $this->urlService->findAnalysesByUrlId($id);

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
                'flash' => $this->flash->getMessages(),
                'currentRoute' => Routes::URLS_SHOW
            ];

            return Twig::fromRequest($request)->render($response, 'urls/show.twig', $params);
        } catch (Exception $e) {
            $response->getBody()->write(Messages::ERROR_LOADING_DATA);

            return $response->withStatus(500);
        }
    }

    public function createCheck(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];

        try {
            $url = $this->urlService->findUrl($id);

            if ($url === null) {
                $response->getBody()->write(Messages::URL_NOT_FOUND);

                $result = $response->withStatus(404);
            } else {
                $analysis = $this->urlService->checkUrl($id, $url->getName());

                if ($analysis === null) {
                    $this->flash->addMessage('error', Messages::CHECK_NETWORK_ERROR);
                } else {
                    $this->flash->addMessage('success', Messages::CHECK_CREATED);
                }

                $result = $this->redirectToShow($request, $response, $id);
            }
        } catch (Exception $e) {
            $this->flash->addMessage('error', Messages::ERROR_CREATING_CHECK);

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
