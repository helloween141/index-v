<?php

namespace Modules\Vpanel\Behaviors;

use Modules\Vpanel\Core\Utils;

trait MetaBehavior
{
    // TODO: save to meta table (id, entity_name, entity_id, meta_title, meta_description)
    public static function boot()
    {
        static::saving(function ($model) {
            if (isset($model->meta_title)) {

            }
            if (isset($model->meta_description)) {

            }
        });

        parent::boot();
    }

}