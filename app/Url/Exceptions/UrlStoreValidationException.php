<?php

namespace Hexlet\Code\Url\Exceptions;

use Exception;
use Hexlet\Code\Config\ExceptionsTexts;

class UrlStoreValidationException extends Exception
{
    public function __construct(string $message = ExceptionsTexts::EMPTY_DATABASE_URL)
    {
        parent::__construct($message);
    }
}
