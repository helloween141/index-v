<?php

namespace Modules\Vpanel\Entities;


class Widget extends base\Widget
{
    public static function findOrCreate(string $module, string $component)
    {
        return static::firstOrCreate(['module' => $module, 'component' => $component]);
    }

    public static function getByRoleIds(array $ids): array
    {
        return static::query()
            ->from(static::getTableName(), 'w')
            ->select(['w.module', 'w.component'])
            ->leftJoin(RoleWidget::getTableName() . ' as rw', 'rw.widget', '=', 'w.id')
            ->whereIn('rw.role', $ids)
            ->get()
            ->toArray();
    }
}
