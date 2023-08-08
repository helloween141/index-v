<?php

namespace Modules\Vpanel\Core\Permissions;

use Illuminate\Database\Eloquent\Collection;
use Modules\Vpanel\Entities\CachePermission;
use Modules\Vpanel\Entities\ControlRoleField;
use Modules\Vpanel\Entities\Role;
use Modules\Vpanel\Entities\RuleEntity;
use Modules\Vpanel\Entities\User;

class AccessChecker
{
    public function getAccess(int $userId, $params = []): int
    {
        /** @var User $user */
        $user = User::getById($userId);
        if (!$user) {
            return Permission::NONE;
        }

        // Права для текущего пользователя
        $userAccess = $this->getAccessForUser($user, $params);

        if ($userAccess == Permission::DELETE) {
            return $userAccess;
        }

        // Права для подчиненных
        $subordinatesAccess = $this->getAccessForUsers($user->getSubordinates(), $params);

        return max($userAccess, $subordinatesAccess);
    }

    /**
     * Права для пользователя
     */
    private function getAccessForUser(object $user, array $params): int
    {
        if ($user->isRoot()) {
            return Permission::DELETE;
        }

        $roles = Role::getByNames($user->getRoleNames());
        if (count($roles) === 0) {
            return Permission::NONE;
        }

        $modelClass = $params['entity'] ?? '';
        if (!$modelClass) {
            return Permission::NONE;
        }

        $recordId = intval($params['record_id']);

        if ($toCheck = $modelClass::getRootMasterAndId($recordId)) {
            $modelClass = trim($toCheck['class'], '\\');
            $entity = RuleEntity::findOrCreate($modelClass);
            $recordId = $toCheck['id'];
        } else {
            $entity = RuleEntity::findOrCreate($modelClass);
        }

        if (ControlRoleField::has($entity->id, $roles->pluck('id')->toArray())) {
            return CachePermission::getMax($entity->id, $user->id, $recordId);
        }

        return $this->getAccessToEntity($roles, $entity->id, $params['strict'] ?? false);
    }

    /**
     * Права для группы пользователей
     */
    private function getAccessForUsers($users, $params)
    {
        $permission = Permission::NONE;

        foreach ($users as $user) {
            $permission = max($permission, $this->getAccessForUser($user, $params));
        }

        return $permission;
    }

    private function getAccessToEntity(Collection $roles, int $entityId, $strict = false): int
    {
        $permission = Permission::NONE;

        /** @var Role $role */
        foreach ($roles as $role) {
            $permission = max($permission, $role->getPermissionForEntity($entityId));

            if (!$strict) {
                $permission = max(
                    $permission,
                    $role->getPermissionForFields($entityId)
                );
            }
        }

        return $permission;
    }
}
