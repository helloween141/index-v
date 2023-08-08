<?php

namespace Modules\Vpanel\Core\Fields;


use Modules\Vpanel\Core\BaseModel;

class IntField extends Field
{
    public function __construct(protected string $type = 'int', protected mixed $defaultValue = 0)
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
            } else {
                return ["{$tableName}.{$this->name}", '=', $filter[$this->name]];
            }
        }
        return [];
    }
}
