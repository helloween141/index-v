<?php

namespace Modules\Vpanel\Entities\base;

use Modules\Vpanel\Core\BaseModel;

class Widget extends BaseModel
{
    protected $table = 'vpanel_widgets';

    public $timestamps = false;

    protected $guarded = [];
}
