<?php

namespace Modules\Vpanel\Entities;

class SubordinateStructure extends base\SubordinateStructure
{
    public static function getById(int $id)
    {
        return static::query()
            ->where('id', '=', $id)
            ->first();
    }

    public function getChildren(): array
    {
        $positions = static::query()
            ->where('parent', '=', $this->id)
            ->get()
            ->keyBy('id');

        $childrenIds = array_keys($positions->toArray());

        foreach ($positions as $position) {
            $childrenIds = [...$childrenIds, ...$position->getChildren()];
        }

        return $childrenIds;
    }
}
