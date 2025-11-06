<?php

namespace Hexlet\Code\Services;

class UrlNormalizer
{
    public function normalize(string $url): ?string
    {
        $url = trim($url);

        if (empty($url)) {
            return null;
        }

        $parsedUrl = parse_url(strtolower($url));

        if (!isset($parsedUrl['scheme']) || !isset($parsedUrl['host'])) {
            return null;
        }

        return $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
    }
}
