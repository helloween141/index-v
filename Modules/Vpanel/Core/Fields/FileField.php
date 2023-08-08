<?php
namespace Modules\Vpanel\Core\Fields;

use Modules\Archive\Entities\File;
use Modules\Vpanel\Core\BaseModel;

class FileField extends Field
{
    public function __construct(protected string $type = 'file', protected mixed $defaultValue = null) {}

    public function getSelect(BaseModel|string $mainModel = ''): array
    {
        $tableName = with(new File)->getTable();

        return [
            "{$tableName}_{$this->name}.id AS {$this->name}.id",
            "{$tableName}_{$this->name}.name AS {$this->name}.name",
            "{$tableName}_{$this->name}.path AS {$this->name}.value"
        ];
    }

    public function getJoin(BaseModel|string $mainModel): array
    {
        $tableName = with(new File)->getTable();
        $mainTableMain = with(new $mainModel)->getTable();

        return [
            "{$tableName} AS {$tableName}_{$this->name}",
            "{$tableName}_{$this->name}.id",
            '=',
            "{$mainTableMain}.{$this->name}"
        ];
    }
}
