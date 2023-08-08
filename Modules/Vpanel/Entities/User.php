<?php

namespace Modules\Vpanel\Entities;

use Modules\Vpanel\Services\SubordinateService;
use Nette\Utils\Json;
use Nette\Utils\JsonException;

class User extends base\User
{
    private array $cacheSubordinatesIds = [];
    private array $cacheSubordinates = [];

    public static function booted()
    {
        static::updated(function (User $user) {
            (new SubordinateService())->totalRebuild();
        });
    }

    public static function getById(int $id)
    {
        return static::query()
            ->where('id', '=', $id)
            ->first();
    }

    public static function getRoot()
    {
        return static::query()
            ->whereJsonContains('role', Role::ROOT)
            ->get()
            ->first();
    }

    public function isRoot(): bool
    {
        $roles = $this->getRoleNames();
        return in_array(Role::ROOT, $roles, true);
    }

    public function hasRole(string $role): bool
    {
        $roles = $this->getRoleNames();
        return in_array($role, $roles, true);
    }

    public function getRoleIds(): array
    {
        $ids = [];
        $roleNames = $this->getRoleNames();
        foreach ($roleNames as $roleName) {
            $role = Role::getByName($roleName);
            if ($role) {
                $ids[] = $role->id;
            }
        }
        return $ids;
    }

    /**
     * @throws JsonException
     */
    public function getRoleNames(): array
    {
        return $this->role ? Json::decode($this->role) : [];
    }

    /**
     * @param int $entityId
     * @return bool
     */
    public function hasRulesByFieldsForEntity(int $entityId): bool
    {
        $roles = $this->getRoleNames();
        if (!$roles) {
            return false;
        }

        if (in_array(Role::ROOT, $roles)) {
            return false;
        }

        $rolesQuery = Role::query()->select('id');
        foreach ($roles as $role) {
            $rolesQuery->orWhere('name', '=', $role);
        }

        $rolesIds = $rolesQuery
            ->pluck('id')
            ->toArray();

        $isControlRoleFieldsExists = ControlRoleField::has($entityId, $rolesIds);

        if (!$isControlRoleFieldsExists) {
            return false;
        }

        return FieldRule::query()
            ->where('entity', '=', $entityId)
            ->whereIn('role', $rolesIds)
            ->exists();
    }

    public function getSubordinates(bool $fromCache = true, bool $onlyIds = false): array
    {
        if ($fromCache) {
            if ($onlyIds) {
                if (count($this->cacheSubordinatesIds) === 0) {
                    $this->cacheSubordinatesIds = $this->subordinates_ids ? explode(',', $this->subordinates_ids) : [];
                }
                return $this->cacheSubordinatesIds;
            } else {
                if (count($this->cacheSubordinates) === 0) {
                    $this->cacheSubordinates =
                        $this->subordinates_ids ?
                            User::query()->whereIn('id', explode(',', $this->subordinates_ids))->get()->all()
                            :
                            [];
                }
                return $this->cacheSubordinates;
            }
        }

        if ($onlyIds && count($this->cacheSubordinatesIds) > 0) {
            return $this->cacheSubordinatesIds;
        } elseif (!$onlyIds && count($this->cacheSubordinates) > 0) {
            return $this->cacheSubordinates;
        }

        /** @var SubordinateStructure $userPosition */
        $userPosition = SubordinateStructure::getById((int)$this->position);

        if (!$userPosition) {
            return [];
        }

        $subPositions = $userPosition->getChildren();

        $query = static::query()
            ->select('id')
            ->whereIn('position', $subPositions);

        if ($onlyIds) {
            $this->cacheSubordinatesIds = $query->pluck('id')->toArray();
            return $this->cacheSubordinatesIds;
        }

        $this->cacheSubordinates = $query->get();
        return $this->cacheSubordinates;
    }
}
