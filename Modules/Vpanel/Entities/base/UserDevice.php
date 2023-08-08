<?php

namespace Modules\Vpanel\Entities\base;

use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Vpanel\Core\BaseModel;
use Modules\Vpanel\Core\Fields\BoolField;
use Modules\Vpanel\Core\Fields\IntField;
use Modules\Vpanel\Core\Fields\StringField;
use Modules\Vpanel\Core\MasterModel;
use Modules\Vpanel\Core\ModelStructure;

class UserDevice extends BaseModel
{
    use SoftDeletes;

    protected $table = 'vpanel_user_devices';

    public static function defineStructure(): ModelStructure
    {
        return ModelStructure::create()
            ->addField(
                StringField::create()
                    ->setTitle('Идентификатор')
                    ->setName('device_id')
                    ->readonly()
            )
            ->addField(
                StringField::create()
                    ->setTitle('Модель')
                    ->setName('model')
                    ->readonly()
            )
            ->addField(
                StringField::create()
                    ->setTitle('Версия приложения')
                    ->setName('app_version')
                    ->readonly()
            )
            ->addField(
                StringField::create()
                    ->setTitle('Пин-код')
                    ->setName('pin')
                    ->readonly()
            )
            ->addField(
                BoolField::create()
                    ->setTitle('Активность')
                    ->setName('active')
            )
            ->addField(
                IntField::create()
                    ->setName('need_to_set_pin')
                    ->hideFromEditor()
                    ->hideFromForm()
            )
            ->addField(
                StringField::create()
                    ->setName('auth_token')
                    ->hideFromEditor()
                    ->hideFromForm()
            )
            ->addField(
                StringField::create()
                    ->setName('session_token')
                    ->hideFromEditor()
                    ->hideFromForm()
            )
            ->addField(
                IntField::create()
                    ->setName('user')
                    ->hideFromEditor()
                    ->hideFromForm()
            )
            ->setMasterModel(
                MasterModel::create()
                    ->setModel(User::class)
                    ->setRelationKey('user')
                    ->showAsTab()
            )
            ->setSoftDelete()
            ->setModelTitle('Устройство')
            ->setRecordTitle('устройство')
            ->setAccusativeRecordTitle('устройство');
    }
}
