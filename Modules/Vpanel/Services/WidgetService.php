<?php

namespace Modules\Vpanel\Services;

use Exception;
use Modules\Vpanel\Core\ApiError;
use Modules\Vpanel\Entities\RoleWidget;
use Modules\Vpanel\Entities\Widget;
use Nwidart\Modules\Facades\Module;

class WidgetService
{
    public function getList(int $roleId): array
    {
        $modules = Module::allEnabled();
        $list = [];
        $key = 0;
        foreach ($modules as $module) {
            $widgetsList = $module->get('widgets') ?? [];

            if (count($widgetsList) > 0) {
                $menu = $module->get('menu') ?? null;
                $list[$key] = [
                    'title' => $menu['title'],
                ];

                foreach ($widgetsList as $item) {
                    $title = $item['title'] ?? '';
                    $component = $item['component'] ?? '';
                    if ($component) {
                        $widget = Widget::findOrCreate($module->get('name'), $component);
                        $roleWidget = RoleWidget::getValue($widget->id, $roleId);

                        $list[$key]['list'][] = [
                            'id' => $widget->id,
                            'title' => $title,
                            'component' => $component,
                            'active' => $roleWidget ?? 0
                        ];
                    }
                }
                $key++;
            }
        }
        return $list;
    }

    /**
     * @throws Exception
     */
    public function saveList(array $widgetIds, int $roleId): bool
    {
        try {
            RoleWidget::query()->where(['role' => $roleId])->delete();
            $insertRoleWidget = [];
            foreach ($widgetIds as $widgetId) {
                $insertRoleWidget[] = [
                    'widget' => $widgetId,
                    'role' => $roleId
                ];
            }
            RoleWidget::query()->insert($insertRoleWidget);
            return true;
        } catch (\Exception $exception) {
            throw new Exception(
                message: $exception->getMessage(),
                code: ApiError::INTERNAL_ERROR
            );
        }
    }

    public function getForUser(): array
    {
        $user = \Auth::user();
        if (!$user) {
            return [];
        }
        $rolesIds = $user->getRoleIds();

        return Widget::getByRoleIds($rolesIds);
    }

}