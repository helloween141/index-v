<?php
namespace Modules\Vpanel\Core\Fields;


class HtmlField extends Field
{
    public function __construct(protected string $type = 'html', protected mixed $defaultValue = '') {}

}
