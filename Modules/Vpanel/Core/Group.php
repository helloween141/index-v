<?php

namespace Modules\Vpanel\Core;

class Group
{
    protected string $name;

    protected string $title;

    protected string $template;

    public static function create(): static
    {
        return new static();
    }

    public function setName(string $value): static
    {
        $this->name = $value;
        return $this;
    }

    public function setTitle(string $value): static
    {
        $this->title = $value;
        return $this;
    }

    public function setTemplate(string $value): static
    {
        $this->template = $value;
        return $this;
    }
}