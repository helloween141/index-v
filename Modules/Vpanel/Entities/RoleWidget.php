<?php

namespace Modules\Vpanel\Entities;


class RoleWidget extends base\RoleWidget
{
    public static function getValue(int $widgetId, int $roleId)
    {
        return static::query()->where([
            'widget' => $widgetId,
            'role' => $roleId
        ])->first();
    }
}
