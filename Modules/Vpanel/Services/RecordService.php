<?php

namespace Modules\Vpanel\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Modules\Archive\Services\UploadService;
use Modules\Vpanel\Core\ApiError;
use Modules\Vpanel\Core\BaseModel;
use Modules\Vpanel\Core\Utils;

class RecordService
{
    /**
     * @throws Exception
     */
    public function getRecord(string|BaseModel $modelClass, int $recordId)
    {
        $accessLevel = Utils::getAccessLevel($modelClass, $recordId);
        if ($accessLevel === 0) {
            throw new Exception(
                code: ApiError::PERMISSION_ERROR
            );
        }

        $structure = $modelClass::getStructure();

        $tableName = $modelClass::getTableName();

        if ($structure->isSoftDelete()) {
            $query = $modelClass::withTrashed();
        }
        $query = $query ?? $modelClass::query();

        $query->addSelect(["$tableName.id"]);

        if ($structure->isSoftDelete()) {
            $query->addSelect("$tableName.deleted_at");
        }

        $fields = $structure->getFields();
        foreach ($fields as $field) {
            if ($field->isCalc()) {
                $query->addSelect(DB::raw('(' . $field->getCalc() . ') as ' . $field->getName()));
            } else {
                $query->addSelect($field->getSelect($modelClass));
            }

            $join = $field->getJoin($modelClass);
            if (count($join) > 0) {
                $query->leftJoin(...$join);
            }
        }

        if (!$structure->isSingle()) {
            $query->where("$tableName.id", '=', $recordId);
        }

        $record = $query->get();

        if ($record->count() === 0) {
            if ($recordId > 0) {
                throw new Exception(
                    code: ApiError::GET_RECORD_ERROR
                );
            }

            return null;
        }

        Utils::prepareModelData($record);

        return $record->first();
    }

    /**
     * @throws Exception
     */
    public function saveRecord(
        string|BaseModel $modelClass,
        int $recordId = 0,
        array $formData = [],
        array $uploads = []
    ) {
        $accessLevel = Utils::getAccessLevel($modelClass, $recordId);

        if ($accessLevel < 2) {
            throw new Exception(
                code: ApiError::PERMISSION_ERROR
            );
        }
        $structure = $modelClass::getStructure();

        if ($structure->isSingle()) {
            $record = $modelClass::query()->first();
        } else {
            $record = $modelClass::query()->find($recordId);
        }

        $requiredFields = [];
        foreach ($structure->getFields() as $field) {
            $fieldName = $field->getName();
            $value = $formData[$fieldName] ?? null;

            if (array_key_exists($fieldName, $formData)) {
                if ($field->getType() === 'password') {
                    if (isset($formData['new_password'])) {
                        $formData['password'] = bcrypt($formData['new_password']);
                        unset($formData['new_password']);
                    }
                } elseif (!$value || $value === 'null') {
                    $formData[$fieldName] = $field->getDefaultValue();
                }
            } else {
                if ($record) {
                    $formData[$fieldName] = $record->$fieldName;
                } elseif (!$value || $value === 'null') {
                    $formData[$fieldName] = $field->getDefaultValue();
                }
            }

            if ($field->getType() === 'file' || $field->getType() === 'image') {
                $upload = $uploads[$fieldName] ?? null;

                if ($upload) {
                    $uploadedEntity = (new UploadService())($upload, $field->getType());
                    $formData[$fieldName] = $uploadedEntity->id;
                }
            }

            if ($field->isRequired()) {
                $requiredDependencies = $field->getRequiredDependencies();
                if (count($requiredDependencies) > 0) {
                    foreach ($requiredDependencies as $key => $value) {
                        if ((isset($formData[$key]) && $formData[$key] === $value) || (isset($formData[$value]))) {
                            $requiredFields = [
                                ...$requiredFields,
                                $fieldName => 'required'
                            ];
                        }
                    }
                } else {
                    $requiredFields = [
                        ...$requiredFields,
                        $fieldName => 'required'
                    ];
                }
            }

            if ($field->isCalc()) {
                unset($formData[$fieldName]);
            }
        }

        $validator = \Validator::make($formData, $requiredFields);

        if ($validator->fails()) {
            throw new Exception(
                message: $validator->messages(),
                code: ApiError::RECORD_VALIDATION_ERROR
            );
        }

        if ($record) {
            $record->update($formData);
        } else {
            $record = $modelClass::query()->create($formData);
        }

        return $record;
    }

    /**
     * @throws Exception
     */
    public function deleteRecord(string|BaseModel $modelClass, int $recordId): bool
    {
        $accessLevel = Utils::getAccessLevel($modelClass, $recordId);
        if ($accessLevel < 4) {
            throw new Exception(
                code: ApiError::PERMISSION_ERROR
            );
        }
        $structure = $modelClass::getStructure();

        if ($structure->isSoftDelete()) {
            $query = $modelClass::withTrashed();
        }
        $query = $query ?? $query::query();

        $record = $query
            ->where('id', $recordId)
            ->first();

        $user = \Auth::user();

        if ($record->deleted_at) {
            if ($user->isRoot()) {
                return $record->forceDelete();
            } else {
                throw new Exception(
                    code: ApiError::PERMISSION_ERROR
                );
            }
        }

        return $record->delete();
    }

    /**
     * @throws Exception
     */
    public function restoreRecord(string|BaseModel $modelClass, int $recordId): bool
    {
        $accessLevel = Utils::getAccessLevel($modelClass, $recordId);
        if ($accessLevel < 4) {
            throw new Exception(
                code: ApiError::PERMISSION_ERROR
            );
        }
        return $modelClass::withTrashed()->where('id', $recordId)->restore();
    }
}
