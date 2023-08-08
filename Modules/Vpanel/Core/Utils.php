<?php

namespace Modules\Vpanel\Core;

use Exception;
use Illuminate\Support\Facades\Auth;
use Modules\Vpanel\Core\Permissions\Permission;
use Modules\Vpanel\Entities\User;

class Utils
{
    public static function toArray($obj, $nestLevel = 0)
    {
        if ($nestLevel > 15) {
            return [];
        }

        if (is_object($obj)) {
            $obj = (array)$obj;
        }

        if (is_array($obj)) {
            $result = [];
            foreach ($obj as $key => $val) {
                $aux = explode("\0", $key);
                $newKey = $aux[count($aux) - 1];
                $result[$newKey] = self::toArray($val, $nestLevel + 1);
            }
        } else {
            $result = $obj;
        }
        return $result;
    }

    public static function translitUrl($value): string
    {
        $converter = array(
            'а' => 'a',
            'б' => 'b',
            'в' => 'v',
            'г' => 'g',
            'д' => 'd',
            'е' => 'e',
            'ё' => 'e',
            'ж' => 'zh',
            'з' => 'z',
            'и' => 'i',
            'й' => 'y',
            'к' => 'k',
            'л' => 'l',
            'м' => 'm',
            'н' => 'n',
            'о' => 'o',
            'п' => 'p',
            'р' => 'r',
            'с' => 's',
            'т' => 't',
            'у' => 'u',
            'ф' => 'f',
            'х' => 'h',
            'ц' => 'c',
            'ч' => 'ch',
            'ш' => 'sh',
            'щ' => 'sch',
            'ь' => '',
            'ы' => 'y',
            'ъ' => '',
            'э' => 'e',
            'ю' => 'yu',
            'я' => 'ya',
        );

        $value = mb_strtolower($value);
        $value = strtr($value, $converter);
        $value = mb_ereg_replace('[^-0-9a-z]', '-', $value);
        $value = mb_ereg_replace('[-]+', '-', $value);
        $value = trim($value, '-');

        return $value;
    }

    /**
     * @throws Exception
     */
    public static function getModelClass(string $moduleName, string $modelName): BaseModel|string
    {
        $className = 'Modules\\' . ucfirst($moduleName) . '\\Entities\\' . ucfirst($modelName);
        if (!class_exists($className)) {
            throw new Exception(
                message: ApiError::getMessage(ApiError::MODEL_NOT_FOUND) . ' ' . $className,
                code: ApiError::MODEL_NOT_FOUND
            );
        }
        return $className;
    }

    public static function prepareModelData($collection)
    {
        $collection->transform(function ($item) {
            foreach ($item->getAttributes() as $key => $value) {
                if (str_contains($key, '.')) {
                    $prefix = explode('.', $key);
                    $item[$prefix[0]] = array_merge($item[$prefix[0]] ?? [], [$prefix[1] => $value]);
                    unset($item[$key]);
                }
            }
            return $item;
        });

        return $collection;
    }

    public static function getAccessLevel(string $modelClass, int $recordId = 0): int
    {
        $user = Auth::user();

        // Исключение для профиля пользователя
        if ($modelClass === User::class && $user->id === $recordId) {
            return Permission::DELETE;
        }

        $read = $user->can('read', [$modelClass, $recordId]) ? Permission::READ : Permission::NONE;
        $edit = $user->can('edit', [$modelClass, $recordId]) ? Permission::EDIT : Permission::NONE;
        $delete = $user->can('delete', [$modelClass, $recordId]) ? Permission::DELETE : Permission::NONE;

        return $read | $edit | $delete;
    }

    public static function generateToken(): string
    {
        return str_shuffle(md5(microtime(false))) . md5(microtime(false));
    }
}
