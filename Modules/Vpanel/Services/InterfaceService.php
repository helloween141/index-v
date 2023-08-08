<?php

namespace Modules\Vpanel\Services;

use Exception;
use Modules\Vpanel\Core\BaseModel;
use Modules\Vpanel\Core\Utils;

class InterfaceService
{
    /**
     * @throws Exception
     */
    public function getInterface(string|BaseModel $modelClass, int $recordId = 0): array
    {
        $structure = $modelClass::getStructure();

        if ($structure) {
            $accessLevel = Utils::getAccessLevel(
                modelClass: $structure->getMasterModel() ? $structure->getMasterModel()->getClass() : $modelClass,
                recordId: $recordId
            );

            if ($accessLevel < 4) {
                $structure->canDelete(false);
            }

            if ($accessLevel < 2) {
                $structure->canEdit(false);
            }
        }

        return Utils::toArray($structure);
    }
}