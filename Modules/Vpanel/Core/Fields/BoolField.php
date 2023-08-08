<?php
namespace Modules\Vpanel\Core\Fields;

use Modules\Vpanel\Core\BaseModel;

class BoolField extends Field
{
    public function __construct(protected string $type = 'bool', protected mixed $defaultValue = false) {}

    public function getWhere(BaseModel|string $mainModel, array $filter): array {
        $tableName = with(new $mainModel)->getTable();
        if (key_exists($this->name, $filter)) {
            return ["{$tableName}.{$this->name}", '=', $filter[$this->name]];
        }
        return [];
    }
}
