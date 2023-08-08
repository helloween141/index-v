<?php

namespace Modules\Vpanel\Core;

class MasterModel
{
    protected string $model;

    protected string $relationKey;

    protected bool $tab = false;

    public static function create(): self
    {
        return new self();
    }

    public function setModel(string|BaseModel $baseModel): self
    {
        $this->model = $baseModel;
        return $this;
    }

    public function setRelationKey(string $relationKey): self
    {
        $this->relationKey = $relationKey;
        return $this;
    }

    public function showAsTab(): self
    {
        $this->tab = true;
        return $this;
    }

    public function isTab(): bool
    {
        return $this->tab;
    }

    public function getRelationKey(): string
    {
        return $this->relationKey;
    }

    public function getClass(): string|BaseModel
    {
        return str_replace('base\\', '', $this->model);
    }
}
