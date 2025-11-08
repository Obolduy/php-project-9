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

        $parsedUrl = parse_url($url);

        if ($parsedUrl === false) {
            return null;
        }

        if (!isset($parsedUrl['scheme']) || !isset($parsedUrl['host'])) {
            return null;
        }

        $scheme = strtolower($parsedUrl['scheme']);
        $host = strtolower($parsedUrl['host']);

        if (!in_array($scheme, ['http', 'https'])) {
            return null;
        }

        return $scheme . '://' . $host;
    }
}
