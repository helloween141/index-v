<?php

namespace Modules\Vpanel\Core\Fields;

use Modules\Vpanel\Core\BaseModel;

class SelectField extends Field
{
    protected array $options = [];

    public function __construct(protected string $type = 'select', protected mixed $defaultValue = '')
    {
    }

    public function setOptions(array $options = []): Field
    {
        $this->options = $options;
        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getWhere(BaseModel|string $mainModel, array $filter): array
    {
        $tableName = with(new $mainModel)->getTable();
        if (key_exists($this->name, $filter)) {
            return ["{$tableName}.{$this->name}", '=', $filter[$this->name]];
        }
        return [];
    }
}
