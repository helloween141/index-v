<?php

namespace Modules\Vpanel\Core;

class ApiError
{
    public const MODEL_NOT_FOUND = 1000;
    public const PERMISSION_ERROR = 1001;
    public const GET_RECORD_ERROR = 1002;
    public const RECORD_VALIDATION_ERROR = 1003;

    public const MOBILE_APP_TOKEN_ERROR = 2000;
    public const MOBILE_APP_CREDENTIALS_ERROR = 2001;
    public const MOBILE_APP_DEVICE_NOT_FOUND = 2002;
    public const MOBILE_APP_USER_NOT_FOUND = 2003;
    public const MOBILE_APP_PIN_ERROR = 2004;
    public const MOBILE_APP_DEVICE_NOT_ACTIVE = 2005;

    public const INTERNAL_ERROR = 4000;

    public static array $messages = [
        self::MOBILE_APP_TOKEN_ERROR => 'Неверный токен',
        self::MODEL_NOT_FOUND => 'Модель не найдена',
        self::PERMISSION_ERROR => 'Недостаточно прав для выполнения операции',
        self::GET_RECORD_ERROR => 'Запись не найдена',
        self::RECORD_VALIDATION_ERROR => 'Ошибка валидации',

        self::MOBILE_APP_CREDENTIALS_ERROR => 'Ошибка авторизации. Неверный логин или пароль',
        self::MOBILE_APP_DEVICE_NOT_FOUND => 'Устройство не найдено',
        self::MOBILE_APP_USER_NOT_FOUND => 'Пользователь не найден',
        self::MOBILE_APP_PIN_ERROR => 'Неверный пин-код',
        self::MOBILE_APP_DEVICE_NOT_ACTIVE => 'Устройство не активировано',

        self::INTERNAL_ERROR => 'Внутренняя ошибка',
    ];

    public static function getMessage(int|string $code): string
    {
        return static::$messages[$code] ?? '';
    }

    public static function getError(int|string $code, string $message = ''): array
    {
        // Обработка внутренних ошибок
        if (!static::getMessage($code)) {
            return [
                'error' => [
                    'code' => $code,
                    'message' => static::getMessage(static::INTERNAL_ERROR),
                    'full_error' => $message
                ]
            ];
        }

        return [
            'error' => [
                'code' => $code,
                'message' => $message ?: static::getMessage($code)
            ]
        ];
    }
}