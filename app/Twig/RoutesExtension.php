<?php

namespace Hexlet\Code\Twig;

use Hexlet\Code\Config\Routes;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class RoutesExtension extends AbstractExtension implements GlobalsInterface
{
    public function getGlobals(): array
    {
        return [
            'routes' => [
                'home' => Routes::HOME,
                'urls_store' => Routes::URLS_STORE,
                'urls_show' => Routes::URLS_SHOW,
                'urls_index' => Routes::URLS_INDEX,
                'urls_checks_store' => Routes::URLS_CHECKS_STORE,
            ]
        ];
    }
}
