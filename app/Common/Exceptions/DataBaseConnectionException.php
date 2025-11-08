<?php

namespace Hexlet\Code\Common\Exceptions;

use Exception;
use Hexlet\Code\Config\ExceptionsTexts;

class DataBaseConnectionException extends Exception
{
    public function __construct(string $message = ExceptionsTexts::EMPTY_DATABASE_URL)
    {
        parent::__construct($message);
    }
}
