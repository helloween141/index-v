<?php

namespace Modules\Vpanel\Core\Fields;


use Modules\Vpanel\Core\BaseModel;

abstract class Field
{
    protected string $name;

    protected string $title;

    protected bool $required = false;

    protected array $requiredDependencies = [];

    protected bool $identify = false;

    protected bool $readonly = false;

    protected bool $parent = false;

    protected bool $inEditor = true;

    protected bool $inForm = true;

    protected bool $inFilter = false;

    protected bool $inSearch = false;

    protected mixed $defaultValue;

    protected string $tooltip;

    protected string $showCondition = '';

    protected string $calc = '';

    protected string $group = '';

    protected string $mask = '';

    protected array $filterConfig = [];

    public function getSelect(BaseModel|string $mainModel): array
    {
        $tableName = with(new $mainModel)->getTable();

        return [
            "{$tableName}.{$this->name} AS {$this->name}"
        ];
    }

    public function getWhere(BaseModel|string $mainModel, array $filter): array
    {
        return [];
    }

    public function getJoin(BaseModel|string $mainModel): array
    {
        return [];
    }

    public static function create(): static
    {
        return new static();
    }

    public function setName(string $value): Field
    {
        $this->name = $value;
        return $this;
    }

    public function setTitle(string $value): Field
    {
        $this->title = $value;
        return $this;
    }

    public function setDefaultValue(mixed $value): Field
    {
        $this->defaultValue = $value;
        return $this;
    }

    public function setTooltip(string $value): Field
    {
        $this->tooltip = $value;
        return $this;
    }

    public function setGroup(string $value): Field
    {
        $this->group = $value;
        return $this;
    }

    public function setMask(string $value): Field
    {
        $this->mask = $value;
        return $this;
    }

    public function required(array $dependencies = []): Field
    {
        $this->requiredDependencies = $dependencies;
        $this->required = true;
        return $this;
    }

    public function identify(): Field
    {
        $this->identify = true;
        return $this;
    }

    public function parent(): Field
    {
        $this->parent = true;
        return $this;
    }

    public function readonly(): Field
    {
        $this->readonly = true;
        return $this;
    }

    public function calc($expression): Field
    {
        $this->calc = $expression;
        return $this;
    }

    public function hideFromEditor(): Field
    {
        $this->inEditor = false;
        return $this;
    }

    public function hideFromForm(): Field
    {
        $this->inForm = false;
        return $this;
    }

    public function showInFilter(array $filterConfig = []): Field
    {
        $this->inFilter = true;
        $this->filterConfig = $filterConfig;
        return $this;
    }

    public function showInSearch(): Field
    {
        $this->inSearch = true;
        return $this;
    }

    public function showCondition($condition): Field
    {
        $this->showCondition = $condition;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDefaultValue(): mixed
    {
        return $this->defaultValue;
    }

    public function getCalc(): string
    {
        return $this->calc;
    }

    public function getGroup(): string
    {
        return $this->group;
    }

    public function getRequiredDependencies(): array
    {
        return $this->requiredDependencies;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function isIdentify(): bool
    {
        return $this->identify;
    }

    public function isParent(): bool
    {
        return $this->parent;
    }

    public function isReadonly(): bool
    {
        return $this->readonly;
    }

    public function isInEditor(): bool
    {
        return $this->inEditor;
    }

    public function isInForm(): bool
    {
        return $this->inForm;
    }

    public function isInSearch(): bool
    {
        return $this->inSearch;
    }

    public function isInFilter(): bool
    {
        return $this->inFilter;
    }

    public function isCalc(): bool
    {
        return !empty($this->calc);
    }
}
