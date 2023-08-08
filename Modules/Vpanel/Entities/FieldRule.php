<?php

namespace Modules\Vpanel\Entities;


class FieldRule extends base\FieldRule
{
    public static function getValue(int $entityId, int $roleId)
    {
        return static::query()->where([
            'entity' => $entityId,
            'role' => $roleId
        ])->first();
    }
}
