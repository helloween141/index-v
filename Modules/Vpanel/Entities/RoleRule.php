<?php

namespace Modules\Vpanel\Entities;


class RoleRule extends base\RoleRule
{
    public static function getValue(int $entityId, int $roleId)
    {
        return static::query()->where([
            'entity' => $entityId,
            'role' => $roleId
        ])->first();
    }
}
