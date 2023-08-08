<?php

namespace Modules\Vpanel\Entities;


class RuleEntity extends base\RuleEntity
{
    public static function findOrCreate(string $name)
    {
        return static::firstOrCreate(['name' => $name]);
    }
}
