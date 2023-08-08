<?php

namespace Modules\Vpanel\Services;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Modules\Vpanel\Entities\CachePermission;
use Modules\Vpanel\Entities\ControlRoleField;
use Modules\Vpanel\Entities\FieldRule;
use Modules\Vpanel\Entities\Role;
use Modules\Vpanel\Entities\RoleRule;
use Modules\Vpanel\Entities\RuleEntity;
use Modules\Vpanel\Entities\User;

class RbacService
{
    protected array $rules = [];

    /**
     * @throws \Throwable
     */
    public function totalRebuild(): void
    {
        $this->clearCache();

        $this->initRules();

        // Получить ids сущностей, у которых включен контроль по полям
        $entitiesIds = ControlRoleField::query()
            ->groupBy(['entity'])
            ->pluck('entity')
            ->toArray();

        // Получить сущности, у которых включен контроль по полям
        $entities = RuleEntity::query()
            ->whereIn('id', $entitiesIds)
            ->get()
            ->toArray();

        $this->rebuildWithFieldControl($entities);

        $this->rebuildWithoutFieldControl();
    }

    /**
     * @throws \Throwable
     */
    private function initRules(string $whereQuery = ''): void
    {
        /*
         *  Получить информацию по всем сущностям, у которых включен контроль по полям
         *  [
                'entity' => 5
                'entity_name' => 'Modules\Blog\Entities\News'
                'role' => 2
                'role_name' => 'Тестовая'
                'field_name' => 'second_user_id'
                'field_permission' => 1
                'common_permission' => 4
            ]
        */
        $query = ControlRoleField::query()
            ->from(ControlRoleField::getTableName() . ' as crf')
            ->select([
                'crf.entity',
                're.name as entity_name',
                'crf.role',
                'r.name as role_name',
                'crf.field as field_name',
                'fr.permission as field_permission',
                'rl.permission as common_permission'
            ])
            ->leftJoin(RuleEntity::getTableName() . ' as re', 'crf.entity', '=', 're.id')
            ->leftJoin(Role::getTableName() . ' as r', 'crf.role', '=', 'r.id')
            ->leftJoin(FieldRule::getTableName() . ' as fr', function ($join) {
                $join->on('crf.entity', '=', 'fr.entity')
                    ->on('crf.role', '=', 'fr.role')
                    ->on('crf.field', '=', 'fr.field');
            })
            ->leftJoin(RoleRule::getTableName() . ' as rl', function ($join) {
                $join->on('rl.entity', '=', 'crf.entity')
                    ->on('rl.role', '=', 'crf.role');
            });

        if ($whereQuery) {
            $query->whereRaw($whereQuery);
        }

        $this->rules = $query
            ->orderBy('crf.entity')
            ->orderBy('crf.field')
            ->orderBy('crf.role')
            ->get()
            ->toArray();

        $this->rebuildForSubordinates();
    }

    /**
     * Пересчитать права модели для пользователя (с контролем по полям)
     * @throws \Throwable
     */
    private function rebuildWithFieldControl(array $entities, array $ids = [], array $users = []): void
    {
        if (count($users) === 0) {
            $roleIds = Arr::pluck($this->rules, 'role');

            $rolesNames = Role::query()
                ->whereIn('id', $roleIds)
                ->pluck('name')
                ->toArray();

            /** @var User[] $users */
            // Выбрать пользователей с ролями кроме ROOT
            $users = User::query()
                ->where(function ($query) use ($rolesNames) {
                    foreach ($rolesNames as $roleName) {
                        $query->orWhereJsonContains('role', $roleName);
                    }
                })
                ->whereJsonDoesntContain('role', Role::ROOT)
                ->get();
        }

        foreach ($users as $user) {
            $userRoles = $user->getRoleNames();

            foreach ($entities as $entity) {
                if (!class_exists($entity['name'])) {
                    continue;
                }

                $permissions = $this->getUserPermissions($entity['id'], $userRoles);

                if ($permissions) {
                    $this->rebuildToEntityForUser($entity, $permissions, $user->id, $ids);
                }
            }
        }
    }

    /**
     * Пересчитать права модели для пользователя
     * @throws \Throwable
     */
    private function rebuildToEntityForUser(array $entity, array $permissions, int $userId, array $ids = []): void
    {
        $cachePermissionTable = CachePermission::getTableName();

        /*
         * Пользователь указан в поле
         */
        $entityName = $entity['name'];

        $all = (int)$permissions['all'];

        $where = array_map(function ($permissionField) use ($userId) {
            return "{$permissionField['f']} = {$userId}";
        }, $permissions['fields']);

        $where = "(" . implode(' or ', $where) . ")";

        if (count($ids) > 0) {
            $where .= " AND __table.id in (" . implode(", ", $ids) . ")";
        }

        $exp = array_map(function ($permissionField) use ($userId, $all) {
            return "(case when {$permissionField['f']} = {$userId} then {$permissionField['p']} else {$all} end)";
        }, $permissions['fields']);

        $exp = implode(", ", $exp);

        $exp = "GREATEST({$exp}, 0)";

        $sql = "SELECT {$entity['id']} as entity, 
                        __table.id as record_id, 
                        {$userId} as " . '"user"' . ",
                        {$exp} as permission 
                FROM " . $entityName::getTableName() . " __table ";

        $sql .= " where " . $where . " and GREATEST({$exp}, 0) > 0 ";

        // В ON CONFLICT используется индекс cache_row_index
        $sql = "INSERT INTO ${cachePermissionTable} (entity, record_id," . '"user"' . ", permission) ({$sql})
                ON CONFLICT (entity, record_id," . '"user"' . ") DO UPDATE SET permission = excluded.permission";

        DB::transaction(function () use ($sql) {
            try {
                return DB::statement($sql);
            } catch (Exception $e) {
                return $e;
            }
        });

        // Пользователь не указан в поле
        if ($all > 0) {
            $where = array_map(function ($permissionField) use ($userId) {
                return "{$permissionField['f']} is distinct from {$userId}";
            }, $permissions['fields']);

            $where = "(" . implode(' and ', $where) . ")";

            if ($ids) {
                $where .= " and __table.id in (" . implode(", ", $ids) . ")";
            }

            $sql = "SELECT {$entity['id']} as entity, 
                        __table.id as record_id, 
                        {$userId} as " . '"user"' . ",
                        {$exp} as permission 
                FROM " . $entityName::getTableName() . " __table ";

            $sql .= " where " . $where . " ";

            $sql = "INSERT INTO ${cachePermissionTable} (entity, record_id," . '"user"' . ", permission) ({$sql})
                    ON CONFLICT (entity, record_id," . '"user"' . ") DO UPDATE 
                    SET permission = excluded.permission;
            ";

            DB::transaction(function () use ($sql) {
                try {
                    return DB::statement($sql);
                } catch (Exception $e) {
                    return $e;
                }
            });
        }
    }

    /**
     * Пересчитать права модели для пользователя (без контроля по полям)
     * @throws \Throwable
     */
    private function rebuildWithoutFieldControl(): void
    {
        $cachePermissionTable = CachePermission::getTableName();
        /** @var Role[] $roles */
        $roles = Role::query()->get();

        foreach ($roles as $role) {
            /** @var RuleEntity[] $entities */
            $entities = RuleEntity::query()->get();

            foreach ($entities as $entity) {
                if (!class_exists($entity->name)) {
                    continue;
                }

                $permission = (int)RoleRule::query()
                    ->select('permission')
                    ->where([
                        'entity' => $entity->id,
                        'role' => $role->id
                    ])
                    ->value('permission');

                /** @var User[] $users */
                $users = User::query()
                    ->whereJsonContains('role', $role->name)
                    ->get();

                foreach ($users as $user) {
                    $permissionSql = "
                    GREATEST(
                      COALESCE(
                        (SELECT max(vcp.permission) FROM ${cachePermissionTable} vcp WHERE vcp.entity = {$entity->id} AND vcp.user = {$user->id} AND vcp.entity = 0), 0),
                        {$permission}
                      )
                    ";

                    // В ON CONFLICT используется индекс cache_row_index
                    $sql = "INSERT INTO ${cachePermissionTable} (entity, record_id," . '"user"' . ", permission) 
                            VALUES ($entity->id, 0, $user->id, $permissionSql)
                            ON CONFLICT (entity, record_id," . '"user"' . ") DO UPDATE 
                            SET permission = excluded.permission;";

                    DB::transaction(function () use ($sql) {
                        try {
                            return DB::statement($sql);
                        } catch (Exception $e) {
                            return $e;
                        }
                    });
                }
            }
        }
    }

    /**
     * Получить максимальные права пользователя на сущность
     */
    private function getUserPermissions(int $entityId, array $userRoles): array
    {
        $permissions = [];
        foreach ($this->rules as $rule) {
            if ($rule['entity'] == $entityId && in_array($rule['role_name'], $userRoles)) {
                if (!key_exists('fields', $permissions)) {
                    $permissions['fields'][$rule['field_name']] = [
                        'f' => $rule['field_name'],
                        'p' => (int)$rule['field_permission'],
                    ];
                    $permissions['all'] = $rule['common_permission'];
                } else {
                    if (!key_exists($rule['field_name'], $permissions['fields'])) {
                        $permissions['fields'][$rule['field_name']] = [
                            'f' => $rule['field_name'],
                            'p' => 0,
                        ];
                    }
                    $permissions['fields'][$rule['field_name']] = [
                        'f' => $rule['field_name'],
                        'p' => (int)max($permissions['fields'][$rule['field_name']]['p'], $rule['field_permission']),
                    ];
                    $permissions['all'] = (string)max($permissions['all'], $rule['common_permission']);
                }
            }
        }
        return $permissions;
    }

    /**
     * Пересчитать права подчиненных пользователей
     * @throws \Throwable
     */
    private function rebuildForSubordinates(): void
    {
        $cachePermissionTable = CachePermission::getTableName();

        $users = User::query()
            ->select(['id', 'subordinates_ids'])
            ->where('subordinates_ids', '<>', '')
            ->whereJsonDoesntContain('role', Role::ROOT)
            ->get()
            ->toArray();

        foreach ($users as $user) {
            $where = ["${cachePermissionTable}." . '"user"' . " = {$user['id']}"];
            if ($user['subordinates_ids']) {
                $where[] = "${cachePermissionTable}." . '"user"' . " in ({$user['subordinates_ids']})";
            }

            $where = implode(' OR ', $where);

            $selectSql = "SELECT entity, record_id, {$user['id']}, max(permission)
                    FROM ${cachePermissionTable}
                    WHERE {$where}
                    GROUP BY entity, record_id";

            $insertSql = "INSERT INTO ${cachePermissionTable} (entity, record_id," . '"user"' . ", permission) ({$selectSql})
                ON CONFLICT (entity, record_id," . '"user"' . ") DO UPDATE 
                SET permission = excluded.permission
                WHERE ${where}";

            DB::transaction(function () use ($insertSql) {
                try {
                    return DB::statement($insertSql);
                } catch (Exception $e) {
                    return $e;
                }
            });
        }
    }

    /**
     * @throws \Throwable
     */
    public function cacheOneRecord($item): void
    {
        $entity = RuleEntity::findOrCreate($item::class);
        if (!$entity) {
            return;
        }

        $this->initRules('crf.entity = ' . $entity->id);

        CachePermission::clearForRoleAndEntity([$entity->id], [], [], [$item->id]);

        $this->rebuildWithFieldControl([
            [
                'id' => $entity->id,
                'name' => $entity->name
            ]
        ], [$item->id]);

        $this->rebuildForSubordinates();
    }

    private function clearCache(): void
    {
        CachePermission::query()->delete();
    }
}
