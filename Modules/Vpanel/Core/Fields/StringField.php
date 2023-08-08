<?php

namespace Modules\Vpanel\Core\Fields;


use Modules\Vpanel\Core\BaseModel;

class StringField extends Field
{
    public function __construct(protected string $type = 'string', protected mixed $defaultValue = '')
    {
    }

    public function getWhere(BaseModel|string $mainModel, array $filter): array
    {
        $tableName = with(new $mainModel)->getTable();
        if (key_exists($this->name, $filter)) {
            return ["{$tableName}.{$this->name}", 'ilike', "%{$filter[$this->name]}%"];
        }
        return [];
    }
}
