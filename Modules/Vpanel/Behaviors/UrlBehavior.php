<?php

namespace Modules\Vpanel\Behaviors;

use Modules\Vpanel\Core\Utils;

trait UrlBehavior
{
    public static function boot()
    {
        static::saving(function ($model) {
            $identifyField = $model::getStructure()->getIdentifyField();
            if ($identifyField) {
                $model->url = Utils::translitUrl($model->$identifyField);
            }
        });

        parent::boot();
    }

}