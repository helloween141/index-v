<?php

namespace Modules\Vpanel\Entities\base;

use Modules\Vpanel\Core\BaseModel;
use Modules\Vpanel\Core\MasterModel;
use Modules\Vpanel\Core\ModelStructure;

class RoleWidget extends BaseModel
{
    protected $table = 'vpanel_role_widgets';

    public static function defineStructure(): ModelStructure
    {
        return ModelStructure::create()
            ->setMasterModel(
                MasterModel::create()
                    ->setModel(Role::class)
                    ->setRelationKey('role')
                    ->showAsTab()
            )
            ->setEditorComponent('RoleWidgetEditor')
            ->setModelTitle('Виджеты')
            ->setRecordTitle('виджет')
            ->setAccusativeRecordTitle('виджет');
    }
}
