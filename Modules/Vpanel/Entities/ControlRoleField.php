<?php

namespace Modules\Vpanel\Entities;


class ControlRoleField extends base\ControlRoleField
{
    public static function has(int $entityId, array $roleIds = [], $field = ''): bool
    {
        $query = self::query()
            ->where('entity', '=', $entityId)
            ->whereIn('role', $roleIds);

        if ($field) {
            $query->where('field', '=', $field);
        }

        return $query->exists();
    }
}
