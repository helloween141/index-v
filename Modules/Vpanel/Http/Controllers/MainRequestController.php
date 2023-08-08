<?php

namespace Modules\Vpanel\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Vpanel\Core\ApiError;
use Modules\Vpanel\Core\Utils;
use Modules\Vpanel\Services\InterfaceService;
use Modules\Vpanel\Services\ListService;
use Modules\Vpanel\Services\MenuService;
use Modules\Vpanel\Services\RbacService;
use Modules\Vpanel\Services\RecordService;

class MainRequestController extends Controller
{
    /**
     * Получить меню (сайдбар)
     */
    public function getMenu(MenuService $service): JsonResponse
    {
        return response()->json($service->getMenu(), Response::HTTP_OK);
    }

    /**
     * Получить интерфейс
     */
    public function getInterface(
        InterfaceService $service,
        string $moduleName,
        string $modelName,
        int $id = 0
    ): JsonResponse {
        try {
            $modelClass = Utils::getModelClass($moduleName, $modelName);
            $interface = $service->getInterface(modelClass: $modelClass, recordId: $id);
        } catch (\Exception $e) {
            return response()->json(ApiError::getError($e->getCode(), $e->getMessage()), Response::HTTP_BAD_REQUEST);
        }

        return response()->json($interface, Response::HTTP_OK);
    }

    /**
     * Получить список записей в табличном представлении
     * @queryParam filter object Фильтр (в формате json)
     * - Числовые поля и даты.
     *   Передается массив: "date": [{"comparsion":">=", "value":"1"},{"comparsion":"<=","value":"10"}]
     *   (при этом не обязательно указывать начальное или конечное значение)
     * - Поля типа pointer: передается массив из id соответствующих записей: "author": [1, 4, 5]
     * - Поля типа select: передается объект вида: "type": {"id": "test-type"}, где id - это соответствующий ключ
     * - Для остальных типов передается обычная пара ключ-значение
     * - Общий пример: "filter": {"title":"Farrel","price":[{"comparsion":">=","value":"100"}],"date":[{"comparsion":">=","value":"2022-12-29"},{"comparsion":"<=","value":"2023-01-15"}],"author":[1,4],"type":{"id":"type2","label":"Тип 2"},"show":1}
     * @queryParam search string Текст для поиcка
     * @queryParam page int Номер страницы
     */
    public function getList(
        Request $request,
        ListService $service,
        string $moduleName,
        string $modelName,
    ): JsonResponse {
        try {
            $modelClass = Utils::getModelClass($moduleName, $modelName);
            $list = $service->getList(
                modelClass: $modelClass,
                params: [
                    'page' => $request->page,
                    'search' => $request->search,
                    'query_string' => $request->query_string ?? '',
                    'filter' => json_decode($request->filter, true),
                ]
            );
        } catch (\Exception $e) {
            return response()->json(ApiError::getError($e->getCode(), $e->getMessage()), Response::HTTP_BAD_REQUEST);
        }

        return response()->json($list, Response::HTTP_OK);
    }

    /**
     * Получить запись
     */
    public function getRecord(
        RecordService $service,
        string $moduleName,
        string $modelName,
        int $id = 0
    ): JsonResponse {
        try {
            $modelClass = Utils::getModelClass($moduleName, $modelName);
            $record = $service->getRecord(modelClass: $modelClass, recordId: $id ?? 0);
        } catch (\Exception $e) {
            return response()->json(ApiError::getError($e->getCode(), $e->getMessage()), Response::HTTP_BAD_REQUEST);
        }

        return response()->json($record, Response::HTTP_OK);
    }

    /**
     * Сохранить запись
     */
    public function saveRecord(
        Request $request,
        RecordService $recordService,
        RbacService $rbacService,
        string $moduleName,
        string $modelName,
        int $id = 0
    ): JsonResponse {
        try {
            $modelClass = Utils::getModelClass($moduleName, $modelName);
            $record = $recordService->saveRecord(
                modelClass: $modelClass,
                recordId: $id ?? 0,
                formData: $request->post(),
                uploads: $request->file()
            );
            $rbacService->cacheOneRecord($record);
        } catch (\Exception $e) {
            return response()->json(ApiError::getError($e->getCode(), $e->getMessage()), Response::HTTP_BAD_REQUEST);
        }

        return response()->json($record->id, Response::HTTP_OK);
    }

    /**
     * Удалить запись
     */
    public function deleteRecord(
        RecordService $service,
        string $moduleName,
        string $modelName,
        int $id
    ): JsonResponse {
        try {
            $modelClass = Utils::getModelClass($moduleName, $modelName);
            $deleteResult = $service->deleteRecord(modelClass: $modelClass, recordId: $id);
        } catch (\Exception $e) {
            return response()->json(ApiError::getError($e->getCode(), $e->getMessage()), Response::HTTP_BAD_REQUEST);
        }

        return response()->json($deleteResult, Response::HTTP_OK);
    }

    /**
     * Восстановить удаленную запись
     */
    public function restoreRecord(
        RecordService $service,
        string $moduleName,
        string $modelName,
        int $id
    ): JsonResponse {
        try {
            $modelClass = Utils::getModelClass($moduleName, $modelName);
            $deleteResult = $service->restoreRecord(modelClass: $modelClass, recordId: $id);
        } catch (\Exception $e) {
            return response()->json(ApiError::getError($e->getCode(), $e->getMessage()), Response::HTTP_BAD_REQUEST);
        }

        return response()->json($deleteResult, Response::HTTP_OK);
    }

    /**
     * Сортировать список записей
     */
    public function sortList(
        Request $request,
        ListService $service,
        string $moduleName,
        string $modelName
    ) {
        try {
            $modelClass = Utils::getModelClass($moduleName, $modelName);
            $sortedList = $service->sortList(modelClass: $modelClass, recordsIds: $request->records_ids);
        } catch (\Exception $e) {
            return response()->json(ApiError::getError($e->getCode(), $e->getMessage()), Response::HTTP_BAD_REQUEST);
        }

        return response()->json($sortedList, Response::HTTP_OK);
    }

    /**
     * Получить значения для PointerField
     */
    public function getPointer(
        Request $request,
        ListService $service,
        string $moduleName,
        string $modelName
    ): JsonResponse {
        try {
            $modelClass = Utils::getModelClass($moduleName, $modelName);
            $resultValues = $service->getPointerValues(
                modelClass: $modelClass,
                queryString: $request->query_string ?? ''
            );
        } catch (\Exception $e) {
            return response()->json(ApiError::getError($e->getCode(), $e->getMessage()), Response::HTTP_BAD_REQUEST);
        }

        return response()->json($resultValues, Response::HTTP_OK);
    }
}
