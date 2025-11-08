<?php

namespace Hexlet\Code\Url\Validators;

use Hexlet\Code\Config\Messages;
use Hexlet\Code\Url\Services\UrlNormalizer;

class UrlValidator
{
    private const int MAX_LENGTH = 255;

    private UrlNormalizer $normalizer;

    public function __construct(UrlNormalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function validate(string $url): array
    {
        $errors = [];

        $url = trim($url);

        if (empty($url)) {
            $errors['name'] = Messages::URL_REQUIRED;
        } elseif (strlen($url) > self::MAX_LENGTH) {
            $errors['name'] = sprintf(Messages::URL_TOO_LONG, self::MAX_LENGTH);
        } else {
            $normalizedUrl = $this->normalizer->normalize($url);

            if ($normalizedUrl === null) {
                $errors['name'] = Messages::URL_INVALID;
            } elseif (strlen($normalizedUrl) > self::MAX_LENGTH) {
                $errors['name'] = sprintf(Messages::URL_TOO_LONG, self::MAX_LENGTH);
            }
        }

        return $errors;
    }
}
