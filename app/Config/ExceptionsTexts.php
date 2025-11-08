<?php

namespace Hexlet\Code\Config;

class ExceptionsTexts
{
    public const string EMPTY_DATABASE_URL = 'DATABASE_URL is not set';

    public const string INVALID_DATABASE_URL = 'Invalid DATABASE_URL format';

    public const string URL_ID_IS_NULL_AT_CREATION = 'URL ID cannot be null';

    public const string URL_ID_IS_NULL_AFTER_SAVE = 'URL ID cannot be null after save';
}
