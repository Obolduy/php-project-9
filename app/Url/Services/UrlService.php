<?php

namespace Hexlet\Code\Url\Services;

use Exception;
use Hexlet\Code\Config\ExceptionsTexts;
use Hexlet\Code\Url\Exceptions\UrlStoreValidationException;
use Hexlet\Code\Url\Models\Url;
use Hexlet\Code\Url\Repositories\UrlRepository;
use Hexlet\Code\Url\Validators\UrlValidator;
use Hexlet\Code\UrlAnalysis\Models\UrlAnalysis;
use Hexlet\Code\UrlAnalysis\Repositories\UrlAnalysisRepository;

class UrlService
{
    private UrlRepository $urlRepository;
    private UrlAnalysisRepository $urlAnalysisRepository;
    private UrlValidator $validator;
    private UrlNormalizer $normalizer;
    private UrlCheckerService $checker;

    public function __construct(
        UrlRepository $urlRepository,
        UrlAnalysisRepository $urlAnalysisRepository,
        UrlValidator $validator,
        UrlNormalizer $normalizer,
        UrlCheckerService $checker
    ) {
        $this->urlRepository = $urlRepository;
        $this->urlAnalysisRepository = $urlAnalysisRepository;
        $this->validator = $validator;
        $this->normalizer = $normalizer;
        $this->checker = $checker;
    }

    public function validateUrl(string $urlName): array
    {
        return $this->validator->validate($urlName);
    }

    public function normalizeUrl(string $urlName): string|false
    {
        return $this->normalizer->normalize($urlName);
    }

    public function findUrlByName(string $name): ?Url
    {
        return $this->urlRepository->findByName($name);
    }

    public function findUrl(int $id): ?Url
    {
        return $this->urlRepository->find($id);
    }

    public function findAllUrlsWithLatestAnalysis(): array
    {
        return $this->urlRepository->findAllWithLatestAnalysis();
    }

    public function createUrl(string $normalizedUrl): Url
    {
        $url = new Url();
        $url->setName($normalizedUrl);
        $this->urlRepository->save($url);

        if ($url->getId() === null) {
            throw new UrlStoreValidationException(ExceptionsTexts::URL_ID_IS_NULL_AFTER_SAVE);
        }

        return $url;
    }

    public function findAnalysesByUrlId(int $urlId): array
    {
        return $this->urlAnalysisRepository->findByUrlId($urlId);
    }

    public function checkUrl(int $urlId, string $urlName): ?UrlAnalysis
    {
        $checkResult = $this->checker->check($urlName);

        if ($checkResult === null) {
            return null;
        }

        $analysis = new UrlAnalysis();
        $analysis->setUrlId($urlId);
        $analysis->setResponseCode($checkResult['response_code']);
        $analysis->setH1($checkResult['h1']);
        $analysis->setTitle($checkResult['title']);
        $analysis->setDescription($checkResult['description']);

        $this->urlAnalysisRepository->save($analysis);

        return $analysis;
    }
}
