<?php

namespace Hexlet\Code\Config;

class Messages
{
    public const string URL_REQUIRED = 'URL не должен быть пустым';
    public const string URL_TOO_LONG = 'URL не должен превышать %d символов';
    public const string URL_INVALID = 'Некорректный URL';
    public const string URL_ADDED = 'Страница успешно добавлена';
    public const string URL_ALREADY_EXISTS = 'Страница уже существует';
    public const string ERROR_SAVING_URL = 'Произошла ошибка при сохранении URL';
    public const string ERROR_LOADING_DATA = 'Произошла ошибка при загрузке данных';
    public const string URL_NOT_FOUND = 'URL не найден';
    public const string CHECK_CREATED = 'Страница успешно проверена';
    public const string ERROR_CREATING_CHECK = 'Произошла ошибка при создании проверки';
    public const string CHECK_NETWORK_ERROR = 'Проверка не была создана. Произошла ошибка при подключении';
}
