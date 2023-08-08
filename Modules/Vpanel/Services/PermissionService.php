<?php

namespace Modules\Vpanel\Services;

use Exception;
use Modules\Vpanel\Core\BaseModel;
use Modules\Vpanel\Core\Utils;
use Modules\Vpanel\Entities\ControlRoleField;
use Modules\Vpanel\Entities\FieldRule;
use Modules\Vpanel\Entities\MenuAccess;
use Modules\Vpanel\Entities\RoleRule;
use Modules\Vpanel\Entities\RuleEntity;
use Modules\Vpanel\Entities\User;
use Nwidart\Modules\Facades\Module;

class PermissionService
{
    /**
     * @throws Exception
     */
    public function getList(int $roleId): array
    {
        $modules = Module::allEnabled();
        $list = [];
        $key = 0;
        foreach ($modules as $module) {
            $menu = $module->get('menu') ?? null;
            if ($menu) {
                $moduleName = $module->get('name') ?? '';

                $list[$key] = [
                    'name' => $moduleName,
                    'title' => $menu['title'],
                ];

                if (isset($menu['list'])) {
                    foreach ($menu['list'] as $item) {
                        $modelClass = Utils::getModelClass($moduleName, $item['model']);
                        $entity = RuleEntity::findOrCreate($modelClass);

                        $roleRule = RoleRule::getValue($entity->id, $roleId);

                        $list[$key]['list'][] = [
                            'name' => $item['model'],
                            'title' => $item['title'],
                            'permission' => $roleRule->permission ?? 0,
                            'menu' => MenuAccess::hasAccess($entity->id, [$roleId]),
                            'access_fields' => $this->getAccessFields($modelClass, $entity->id, $roleId)
                        ];
                    }
                } else {
                    $modelClass = Utils::getModelClass($moduleName, $moduleName);
                    $entity = RuleEntity::findOrCreate($modelClass);

                    $roleRule = RoleRule::getValue($entity->id, $roleId);
                    $list[$key]['list'][] = [
                        'name' => $moduleName,
                        'title' => $menu['title'],
                        'permission' => $roleRule->permission ?? 0,
                        'menu' => MenuAccess::hasAccess($entity->id, [$roleId]),
                        'access_fields' => $this->getAccessFields($modelClass, $entity->id, $roleId)
                    ];
                }
            }
            $key++;
        }
        return $list;
    }

    /**
     * @throws Exception
     */
    public function saveList(array $data, int $roleId): bool
    {
        try {
            RoleRule::query()->where(['role' => $roleId])->delete();
            MenuAccess::query()->where(['role' => $roleId])->delete();
            FieldRule::query()->where(['role' => $roleId])->delete();
            ControlRoleField::query()->where(['role' => $roleId])->delete();

            $insertRoleRule = [];
            $insertMenuAccess = [];
            $insertAccessFields = [];
            $insertControlFields = [];
            foreach ($data as $module) {
                foreach ($module['list'] as $model) {
                    $modelClass = Utils::getModelClass($module['name'], $model['name']);
                    $entity = RuleEntity::findOrCreate($modelClass);

                    if ($model['menu']) {
                        $insertMenuAccess[] = [
                            'entity' => $entity->id,
                            'role' => $roleId,
                        ];
                    }

                    if ($model['permission'] > 0) {
                        $insertRoleRule[] = [
                            'entity' => $entity->id,
                            'role' => $roleId,
                            'permission' => $model['permission']
                        ];
                    }

                    if (count($model['access_fields']) > 0) {
                        foreach ($model['access_fields'] as $accessField) {
                            if ($accessField['active']) {
                                $insertControlFields[] = [
                                    'entity' => $entity->id,
                                    'role' => $roleId,
                                    'field' => $accessField['name']
                                ];
                                if ($accessField['permission'] > 0) {
                                    $insertAccessFields[] = [
                                        'entity' => $entity->id,
                                        'role' => $roleId,
                                        'field' => $accessField['name'],
                                        'permission' => $accessField['permission']
                                    ];
                                }
                            }
                        }
                    }
                }
            }

            RoleRule::query()->insert($insertRoleRule);
            MenuAccess::query()->insert($insertMenuAccess);
            FieldRule::query()->insert($insertAccessFields);
            ControlRoleField::query()->insert($insertControlFields);
            return true;
        } catch (\Exception $exception) {
            throw new Exception(
                message: $exception->getMessage()
            );
        }
    }

    private function getAccessFields(string $modelClass, int $entityId, int $roleId): array
    {
        $result = [];
        /** @var $model BaseModel */
        $model = new $modelClass();
        foreach ($model::getStructure()->getFields() as $field) {
            if ($field->getType() === 'pointer') {
                $pointerModel = new ($field->getModel())();
                if ($pointerModel instanceof User) {
                    $fieldRule = FieldRule::query()
                        ->where('field', '=', $field->getName())
                        ->where('entity', '=', $entityId)
                        ->where('role', '=', $roleId)
                        ->first();

                    $controlRoleField = ControlRoleField::query()
                        ->where('field', '=', $field->getName())
                        ->where('entity', '=', $entityId)
                        ->where('role', '=', $roleId)
                        ->first();

                    $result[] = [
                        'active' => isset($controlRoleField),
                        'name' => $field->getName(),
                        'title' => $field->getTitle(),
                        'permission' => $fieldRule ? intval($fieldRule->permission) : 0
                    ];
                }
            }
        }
        return $result;
    }
}