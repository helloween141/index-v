<?php

namespace Modules\Vpanel\Entities;


class CachePermission extends base\CachePermission
{
    public static function clearForRoleAndEntity(
        array $entities = [],
        array $roleNames = [],
        array $users = [],
        array $recordsIds = []
    ) {
        if (count($users) === 0) {
            if (count($roleNames) > 0) {
                $users = User::query()
                    ->where(function ($query) use ($roleNames) {
                        foreach ($roleNames as $roleName) {
                            $query->orWhereJsonContains('role', $roleName);
                        }
                    })
                    ->whereJsonDoesntContain('role', Role::ROOT)
                    ->pluck('id')
                    ->toArray();
            } else {
                $users = User::query()
                    ->whereJsonDoesntContain('role', Role::ROOT)
                    ->pluck('id')
                    ->toArray();
            }
        }

        if (count($entities) > 0) {
            $additionalIdsQuery = '';
            if (count($recordsIds) > 0) {
                $additionalIdsQuery = "record_id in (" . implode(", ", $recordsIds) . ")";
            }

            if ($users) {
                CachePermission::query()
                    ->whereIn('entity', $entities)
                    ->whereIn('user', $users)
                    ->whereRaw($additionalIdsQuery)
                    ->delete();
            }
        } elseif ($users) {
            CachePermission::query()
                ->whereIn('user', $users)
                ->delete();
        }
    }

    public static function getMax(int $entityId, int $userId, int $recordId): int
    {
        return (int)self::query()
            ->select('permission')
            ->where('entity', '=', $entityId)
            ->where('user', '=', $userId)
            ->where('record_id', '=', $recordId)
            ->value('permission');
    }
}
