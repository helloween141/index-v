<?php

namespace Modules\Vpanel\Core\Fields;

use Modules\Vpanel\Core\BaseModel;

class DateField extends Field
{
    protected bool $withTime = false;

    public function __construct(protected string $type = 'date', protected mixed $defaultValue = null)
    {
    }

    public function getWhere(BaseModel|string $mainModel, array $filter): array
    {
        $tableName = with(new $mainModel)->getTable();
        if (key_exists($this->name, $filter)) {
            if (is_array($filter[$this->name])) {
                $resultQuery = [];
                foreach ($filter[$this->name] as $item) {
                    $resultQuery[] = ["{$tableName}.{$this->name}", $item['comparsion'], $item['value']];
                }
                return $resultQuery;
            }
        }
        return [];
    }

    public function withTime(): DateField
    {
        $this->withTime = true;
        return $this;
    }

    public function getDefaultValue(): mixed
    {
        return date('Y-m-d H:i:s');
    }
}
