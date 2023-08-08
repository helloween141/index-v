<?php

namespace Modules\Vpanel\Services;

use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Modules\Vpanel\Core\ApiError;
use Modules\Vpanel\Core\BaseModel;
use Modules\Vpanel\Core\Utils;
use Modules\Vpanel\Entities\CachePermission;
use Modules\Vpanel\Entities\RuleEntity;

class ListService
{
    protected const PER_PAGE = 30;

    /**
     * @throws Exception
     */
    public function getList(string|BaseModel $modelClass, array $params = []): Collection|LengthAwarePaginator|array
    {
        $structure = $modelClass::getStructure();

        $accessLevel = Utils::getAccessLevel(
            $structure->getMasterModel() ? $structure->getMasterModel()->getClass() : $modelClass
        );

        $parentId = $params['parent'] ?? null;
        $page = $params['page'] ?? null;
        $filter = $params['filter'] ?? []; // для фильтров в списке
        $search = $params['search'] ?? '';
        $queryString = $params['query_string'] ?? ''; // для filterCondition

        $tableName = $modelClass::getTableName();

        if ($structure->isSoftDelete()) {
            $showOnlyFilter = $filter['show_only'] ?? '';
            if ($showOnlyFilter) {
                if ($showOnlyFilter === 'all') {
                    $query = $modelClass::withTrashed();
                } elseif ($showOnlyFilter === 'trashed') {
                    $query = $modelClass::onlyTrashed();
                }
            }
        }

        $query = $query ?? $modelClass::query();

        $query->addSelect("$tableName.id");

        if ($structure->isSoftDelete()) {
            $query->addSelect("$tableName.deleted_at");
        }

        $fields = $structure->getFields();
        foreach ($fields as $field) {
            if ($field->isParent()) {
                $query->where("$tableName.{$field->getName()}", $parentId);
                continue;
            }

            if ($field->isInEditor()) {
                if ($field->isCalc()) {
                    $query->selectRaw('(' . $field->getCalc() . ') as "' . $field->getName() . '"');
                } else {
                    $query->addSelect($field->getSelect($modelClass));
                }
            }

            $join = $field->getJoin($modelClass);
            if (count($join) > 0) {
                $query->leftJoin(...$join);
            }

            if (count($filter) > 0) {
                $where = $field->getWhere($modelClass, $filter);
                if ($where) {
                    if ($field->isCalc() && key_exists($field->getName(), $filter)) {
                        $query->whereRaw(
                            "(" . $field->getCalc() . ")" . " ilike '%" . $filter[$field->getName()] . "%'"
                        );
                    } elseif ($field->getType() === 'pointer') {
                        $query->whereIn(...$where);
                    } elseif (is_array($where[0])) {
                        foreach ($where as $w) {
                            $query->where(...$w);
                        }
                    } else {
                        $query->where(...$where);
                    }
                }
            } elseif (!empty($search) && $field->isInSearch()) {
                if ($field->isCalc()) {
                    $query->orWhereRaw("(" . $field->getCalc() . ")" . " ilike '%" . $search . "%'");
                } else {
                    if ($field->getType() === 'pointer') {
                        $pointerModel = $field->getModel();
                        $aliasTable = with(new $pointerModel())->getTable();
                        $aliasIdentifyFieldName = $pointerModel::getStructure()->getIdentifyField();
                        $searchColumn = "{$aliasTable}_{$field->getName()}.$aliasIdentifyFieldName";
                    } else {
                        $searchColumn = "$tableName.{$field->getName()}";
                    }
                    $query->orWhere($searchColumn, 'ilike', "%$search%");
                }
            }
        }

        if ($queryString) {
            $query->whereRaw($queryString);
        }

        // Учет прав по полям
        if ($structure->isRightsControl()) {
            $user = Auth::user();
            $entity = RuleEntity::findOrCreate($modelClass);

            if ($accessLevel === 0) {
                if ($user->hasRulesByFieldsForEntity($entity->id)) {
                    $query->leftJoin(
                        CachePermission::getTableName() . ' as vcp',
                        function ($join) use ($tableName, $user, $entity) {
                            $join->on('vcp.record_id', '=', $tableName . '.id');
                            $join->where('vcp.entity', '=', $entity->id);
                            $join->where('vcp.user', '=', $user->id);
                        }
                    )
                        ->where('vcp.permission', '>', 0)
                        ->whereNotNull('vcp.permission');
                } else {
                    throw new Exception(
                        code: ApiError::PERMISSION_ERROR
                    );
                }
            }
        }

        if ($structure->isSortable()) {
            $sortField = 'sort';
            $sortOrder = 'asc';
        } else {
            $sortField = $structure->getSortField() ?: 'id';
            $sortOrder = $structure->getSortDirection() ?: 'asc';
        }

        $query->orderBy("$tableName." . $sortField, $sortOrder);

        if ($page > 0) {
            $resultList = $query->paginate(self::PER_PAGE);
            Utils::prepareModelData($resultList->getCollection());
        } else {
            $resultList = $query->get();
        }

        if ($structure->isRecursive()) {
            foreach ($resultList as $item) {
                $item->children = self::getList($modelClass, ['parent' => $item->id]);
            }
        }
        return $resultList;
    }

    public function sortList(string|BaseModel $modelClass, array $recordsIds = []): Collection
    {
        $records = $modelClass::query()->whereIn('id', $recordsIds)->get();

        foreach ($recordsIds as $key => $value) {
            foreach ($records as $record) {
                if ($value === $record->id) {
                    $record->sort = $key;
                    $record->save();
                    break;
                }
            }
        }
        return $records;
    }

    public function getPointerValues(string|BaseModel $modelClass, string $queryString = ''): Collection|array
    {
        $structure = $modelClass::getStructure();

        $fields = $structure->getFields();
        foreach ($fields as $field) {
            if ($field->isIdentify()) {
                $query = $modelClass::query()->select(['id', $field->getName()]);

                if ($queryString) {
                    $query->whereRaw($queryString);
                }

                $values = $query->get();

                return [
                    'identifyLabel' => $field->getName(),
                    'values' => $values
                ];
            }
        }

        return [];
    }
}