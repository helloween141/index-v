<?php

namespace Modules\Vpanel\Core\Fields;

use Modules\Vpanel\Core\BaseModel;

class PointerField extends Field
{
    /** @var string|BaseModel */
    protected mixed $model;

    protected bool $isModal = false;

    protected string $filterCondition = '';

    public function __construct(protected string $type = 'pointer', protected mixed $defaultValue = null)
    {
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;
        return $this;
    }

    public function modal(): PointerField
    {
        $this->isModal = true;
        return $this;
    }

    public function getSelect(BaseModel|string $mainModel = ''): array
    {
        /** @var string|BaseModel */
        $model = $this->getModel();

        $tableName = with(new $model)->getTable();
        $identifyFieldName = $model::getStructure()->getIdentifyField();

        $this->identify = $identifyFieldName;

        return [
            "{$tableName}_{$this->name}.id AS {$this->name}.id",
            "{$tableName}_{$this->name}.{$identifyFieldName} AS {$this->name}.{$identifyFieldName}"
        ];
    }

    public function getWhere(BaseModel|string $mainModel, array $filter): array
    {
        /** @var string|BaseModel */
        $model = $this->getModel();

        $tableName = with(new $model)->getTable();
        if (key_exists($this->name, $filter)) {
            $ids = $filter[$this->name];
            if (count($ids) > 0) {
                return ["{$tableName}_{$this->name}.id", $filter[$this->name]];
            }
        }

        return [];
    }

    public function getJoin(BaseModel|string $mainModel): array
    {
        /** @var string|BaseModel */
        $model = $this->getModel();

        $tableName = with(new $model)->getTable();
        $mainTableMain = with(new $mainModel)->getTable();

        return [
            "{$tableName} AS {$tableName}_{$this->name}",
            "{$tableName}_{$this->name}.id",
            '=',
            "{$mainTableMain}.{$this->name}"
        ];
    }

    public function filterCondition(string $condition): PointerField
    {
        $this->filterCondition = $condition;
        return $this;
    }
}
