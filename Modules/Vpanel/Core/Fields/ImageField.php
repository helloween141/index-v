<?php
namespace Modules\Vpanel\Core\Fields;

use Modules\Archive\Entities\Image;
use Modules\Vpanel\Core\BaseModel;

class ImageField extends Field
{
    public function __construct(protected string $type = 'image', protected mixed $defaultValue = null) {}

    public function getSelect(BaseModel|string $mainModel = ''): array
    {
        $tableName = with(new Image)->getTable();

        return [
            "{$tableName}_{$this->name}.id AS {$this->name}.id",
            "{$tableName}_{$this->name}.name AS {$this->name}.name",
            "{$tableName}_{$this->name}.path AS {$this->name}.value"
        ];
    }

    public function getJoin(BaseModel|string $mainModel): array
    {
        $tableName = with(new Image)->getTable();
        $mainTableMain = with(new $mainModel)->getTable();

        return [
            "{$tableName} AS {$tableName}_{$this->name}",
            "{$tableName}_{$this->name}.id",
            '=',
            "{$mainTableMain}.{$this->name}"
        ];
    }
}
