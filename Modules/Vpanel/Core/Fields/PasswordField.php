<?php

namespace Modules\Vpanel\Core\Fields;


class PasswordField extends Field
{
    public function __construct(protected string $type = 'password', protected mixed $defaultValue = ''){}
}
