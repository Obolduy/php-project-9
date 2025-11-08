<?php

namespace Hexlet\Code\Url\Services;

class UrlNormalizer
{
    public function normalize(string $url): ?string
    {
        $url = trim($url);

        if (empty($url)) {
            return null;
        }

        $result = null;

        $parsedUrl = parse_url($url);

        if ($parsedUrl !== false && isset($parsedUrl['scheme']) && isset($parsedUrl['host'])) {
            $scheme = strtolower($parsedUrl['scheme']);

            if (in_array($scheme, ['http', 'https'])) {
                $result = $scheme . '://' . strtolower($parsedUrl['host']);
            }
        }

        return $result;
    }
}
