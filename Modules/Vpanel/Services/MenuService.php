<?php

namespace Modules\Vpanel\Services;

use Modules\Vpanel\Core\Utils;
use Modules\Vpanel\Entities\MenuAccess;
use Modules\Vpanel\Entities\RuleEntity;
use Nwidart\Modules\Facades\Module;

class MenuService
{
    /**
     * @throws \Exception
     */
    public function getMenu(): array
    {
        $modules = Module::allEnabled();
        $list = [];
        $key = 0;
        $user = \Auth::getUser();
        foreach ($modules as $module) {
            $menu = $module->get('menu') ?? null;
            if ($menu) {
                $moduleName = $module->get('name');

                $list[$key] = [
                    'module' => $moduleName,
                    'title' => $menu['title'],
                    'icon' => $menu['icon']
                ];

                if (isset($menu['list'])) {
                    foreach ($menu['list'] as $item) {
                        $modelClass = Utils::getModelClass($moduleName, $item['model']);
                        $entity = RuleEntity::findOrCreate($modelClass);

                        if ($user->isRoot() || MenuAccess::hasAccess($entity->id, $user->getRoleIds())) {
                            $list[$key]['list'][] = [
                                'model' => $item['model'],
                                'title' => $item['title'],
                                'icon' => $item['icon'] ?? ''
                            ];
                        }
                    }
                    if (!isset($list[$key]['list'])) {
                        unset($list[$key]);
                    }
                } else {
                    $modelClass = Utils::getModelClass($moduleName, $moduleName);
                    $entity = RuleEntity::findOrCreate($modelClass);
                    if ($user->isRoot() || MenuAccess::hasAccess($entity->id, $user->getRoleIds())) {
                        $list[$key] = array_merge($list[$key], [
                            'model' => $moduleName,
                            'show' => $user->isRoot() || MenuAccess::hasAccess($entity->id, $user->getRoleIds()),
                        ]);
                    } else {
                        unset($list[$key]);
                    }
                }
            }
            $key++;
        }
        return $list;
    }
}