<?php

namespace Modules\Vpanel\Core\Fields;

class TextField extends Field
{
    public function __construct(protected string $type = 'text', protected mixed $defaultValue = ''){}
}
