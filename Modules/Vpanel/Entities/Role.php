<?php

namespace Modules\Vpanel\Entities;


use Modules\Vpanel\Core\Permissions\Permission;

class Role extends base\Role
{
    public const ROOT = 'ROOT';

    public static function getByName(string $name)
    {
        return static::query()
            ->where('name', '=', $name)
            ->first();
    }

    public static function getByNames(array $names)
    {
        return static::query()
            ->whereIn('name', $names)
            ->get();
    }

    public function getPermissionForFields(int $entityId): int
    {
        $rule = FieldRule::getValue($entityId, $this->id);

        return $rule ? intval($rule->permission) : Permission::NONE;
    }

    public function getPermissionForEntity(int $entityId): int
    {
        $rule = RoleRule::getValue($entityId, $this->id);

        return $rule ? intval($rule->permission) : Permission::NONE;
    }


}
