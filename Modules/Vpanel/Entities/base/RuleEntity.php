<?php

namespace Modules\Vpanel\Entities\base;

use Modules\Vpanel\Core\BaseModel;

class RuleEntity extends BaseModel
{
    protected $table = 'vpanel_rule_entities';

    public $timestamps = false;

    protected $guarded = [];
}
