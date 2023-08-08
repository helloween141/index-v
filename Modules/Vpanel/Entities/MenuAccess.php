<?php

namespace Modules\Vpanel\Entities;


class MenuAccess extends base\MenuAccess
{
    public static function hasAccess(int $entityId, array $roleIds): bool
    {
        $result = static::query()
            ->where('entity', '=', $entityId)
            ->whereIn('role', $roleIds)
            ->first();
        return (bool)$result;
    }
}
