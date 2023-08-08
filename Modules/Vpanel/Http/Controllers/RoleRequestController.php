<?php

namespace Modules\Vpanel\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Vpanel\Services\PermissionService;
use Modules\Vpanel\Services\RbacService;
use Modules\Vpanel\Services\WidgetService;

class RoleRequestController extends Controller
{
    public function getPermissionList(PermissionService $service, int $roleId): JsonResponse
    {
        return response()->json($service->getList($roleId), Response::HTTP_OK);
    }

    /**
     * @throws \Throwable
     */
    public function savePermissionList(
        Request $request,
        PermissionService $permissionService,
        RbacService $rbacService,
        int $roleId
    ): JsonResponse {
        $formData = $request->post('data', []);

        $result = $permissionService->saveList($formData, $roleId);

        $rbacService->totalRebuild();

        return response()->json($result, $result ? Response::HTTP_OK : Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function getWidgetList(WidgetService $service, int $roleId): JsonResponse
    {
        return response()->json($service->getList($roleId), Response::HTTP_OK);
    }

    /**
     * @throws \Exception
     */
    public function saveWidgetList(Request $request, WidgetService $service, int $roleId): JsonResponse
    {
        $formData = $request->post('widget_ids', []);

        $result = $service->saveList($formData, $roleId);

        return response()->json($result, $result ? Response::HTTP_OK : Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
