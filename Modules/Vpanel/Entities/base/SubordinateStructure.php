<?php

namespace Modules\Vpanel\Entities\base;

use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Vpanel\Core\BaseModel;
use Modules\Vpanel\Core\Fields\PointerField;
use Modules\Vpanel\Core\Fields\SelectField;
use Modules\Vpanel\Core\Fields\StringField;
use Modules\Vpanel\Core\ModelStructure;

class SubordinateStructure extends BaseModel
{
    use SoftDeletes;

    protected $table = 'vpanel_subordinates_structure';

    public static function defineStructure(): ModelStructure
    {
        return ModelStructure::create()
            ->addField(
                StringField::create()
                    ->setName('name')
                    ->setTitle('Название')
                    ->identify()
                    ->required()
            )
            ->addField(
                SelectField::create()
                    ->setName('type')
                    ->setTitle('Тип')
                    ->setOptions([
                        'position' => 'Должность'
                    ])
                    ->required()
            )
            ->addField(
                PointerField::create()
                    ->setName('parent')
                    ->setTitle('Родительская должность')
                    ->setModel(self::class)
                    ->parent()
                    ->modal()
                    ->hideFromEditor()
            )
            ->addField(
                SelectField::create()
                    ->setName('user_interface')
                    ->setTitle('Интерфейс')
                    ->setOptions([
                        'default' => 'По умолчанию'
                    ])
                    ->showCondition('record.type === "position"')
                    ->setDefaultValue('default')
                    ->required()
            )
            ->setRecursive(expand: true)
            ->setSoftDelete()
            ->setModelTitle('Структура подчинений')
            ->setRecordTitle('должность')
            ->setAccusativeRecordTitle('должность');
    }
}
