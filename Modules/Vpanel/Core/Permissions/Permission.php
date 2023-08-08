<?php

namespace Modules\Vpanel\Core\Permissions;

class Permission
{
    public const NONE = 0;
    public const READ = 1;
    public const EDIT = 2;
    public const DELETE = 4;

    public const LABELS = [
        self::NONE => 'Запрещено',
        self::READ => 'Чтение',
        self::EDIT => 'Редактирование',
        self::DELETE => 'Полный доступ',
    ];

    public static function has($permission): bool
    {
        return array_key_exists($permission, self::LABELS);
    }
}
