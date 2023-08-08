<?php

namespace Modules\Vpanel\Entities\base;

use Modules\Vpanel\Core\BaseModel;
use Modules\Vpanel\Core\Fields\IntField;
use Modules\Vpanel\Core\MasterModel;
use Modules\Vpanel\Core\ModelStructure;

class RoleRule extends BaseModel
{
    protected $table = 'vpanel_role_rules';

    public static function defineStructure(): ModelStructure
    {
        return ModelStructure::create()
            ->addField(
                IntField::create()
                    ->setName('permission')
            )
            ->addField(
                IntField::create()
                    ->setName('role')
            )
            ->setMasterModel(
                MasterModel::create()
                    ->setModel(Role::class)
                    ->setRelationKey('role')
                    ->showAsTab()
            )
            ->setEditorComponent('RoleRuleEditor')
            ->setModelTitle('Правила')
            ->setRecordTitle('правило')
            ->setAccusativeRecordTitle('правило');
    }
}
