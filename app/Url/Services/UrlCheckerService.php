<?php

namespace Hexlet\Code\Url\Services;

use DiDom\Document;
use DiDom\Element;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class UrlCheckerService
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 10,
            'connect_timeout' => 10,
            'http_errors' => false,
            'allow_redirects' => true,
        ]);
    }

    public function check(string $url): ?array
    {
        try {
            $response = $this->client->get($url);

            $statusCode = $response->getStatusCode();
            $body = (string) $response->getBody();

            $h1 = $this->extractH1($body);
            $title = $this->extractTitle($body);
            $description = $this->extractDescription($body);

            $result = [
                'response_code' => $statusCode,
                'h1' => $h1,
                'title' => $title,
                'description' => $description,
            ];
        } catch (ConnectException | GuzzleException $e) {
            $result = null;
        }

        return $result;
    }

    private function extractH1(string $html): ?string
    {
        if (empty($html)) {
            return null;
        }

        return $this->suppressDeprecations(function () use ($html) {
            $result = null;

            try {
                $document = new Document($html);
                $h1Element = $document->first('h1');

                if ($h1Element instanceof Element) {
                    $text = $h1Element->text();

                    if ($text) {
                        $result = mb_substr(trim($text), 0, 255);
                    }
                }
            } catch (Exception $e) {
                $result = null;
            }

            return $result;
        });
    }

    private function extractTitle(string $html): ?string
    {
        if (empty($html)) {
            return null;
        }

        return $this->suppressDeprecations(function () use ($html) {
            $result = null;

            try {
                $document = new Document($html);
                $titleElement = $document->first('title');

                if ($titleElement instanceof Element) {
                    $text = $titleElement->text();

                    if ($text) {
                        $result = mb_substr(trim($text), 0, 255);
                    }
                }
            } catch (Exception $e) {
                $result = null;
            }

            return $result;
        });
    }

    private function extractDescription(string $html): ?string
    {
        if (empty($html)) {
            return null;
        }

        return $this->suppressDeprecations(function () use ($html) {
            $result = null;

            try {
                $document = new Document($html);
                $metaElement = $document->first('meta[name="description"]');

                if ($metaElement instanceof Element) {
                    $content = $metaElement->getAttribute('content');

                    if ($content) {
                        $result = trim($content);
                    }
                }
            } catch (Exception $e) {
                $result = null;
            }

            return $result;
        });
    }

    /**
     * Подавляет deprecation warnings от DiDOM при выполнении callback из-за несовместимости DiDOM 2.0.1 и PHP 8.4
     */
    private function suppressDeprecations(callable $callback): mixed
    {
        $previousLevel = error_reporting();

        error_reporting($previousLevel & ~E_DEPRECATED);

        try {
            return $callback();
        } finally {
            error_reporting($previousLevel);
        }
    }
}
