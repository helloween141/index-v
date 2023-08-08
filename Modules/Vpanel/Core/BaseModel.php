<?php

namespace Modules\Vpanel\Core;

use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    protected static array $structures = [];
    
    public static function getStructure(): ?ModelStructure
    {
        if (!array_key_exists(static::class, self::$structures)) {
            self::$structures[static::class] = static::defineStructure();
        }
        return self::$structures[static::class];
    }

    public static function defineStructure(): ?ModelStructure
    {
        return null;
    }

    public static function getTableName(): string
    {
        return with(new static())->getTable();
    }

    public static function getRootMasterAndId(int $recordId = 0): ?array
    {
        $masterModel = static::getStructure()->getMasterModel();

        if ($masterModel) {
            $masterId = $recordId ? static::query()->select($masterModel->getRelationKey())->where(
                'id',
                '=',
                $recordId
            )->value($masterModel->getRelationKey()) : 0;

            if (!$result = ($masterModel->getClass())::getRootMasterAndId($masterId)) {
                return [
                    'id' => $masterId,
                    'class' => $masterModel->getClass(),
                ];
            } else {
                return $result;
            }
        }
        return null;
    }
}
