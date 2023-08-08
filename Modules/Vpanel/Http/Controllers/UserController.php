<?php

namespace Modules\Vpanel\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\Vpanel\Core\ApiError;
use Modules\Vpanel\Entities\User;
use Modules\Vpanel\Services\RecordService;
use Modules\Vpanel\Services\WidgetService;

class UserController extends Controller
{
    public const ADMIN_SESSION_KEY = 'admin_id';

    public function index()
    {
        return view('vpanel::login');
    }

    /**
     * Получить информацию о текущем пользователе
     * @throws \Exception
     */
    public function getInfo(RecordService $service): JsonResponse
    {
        try {
            $user = Auth::user();

            $userInfo = ($service->getRecord(User::class, $user->id))->toArray();

            $userInfo = array_merge($userInfo, [
                'blocked' => $user->blocked
            ]);

            if (session()->has(self::ADMIN_SESSION_KEY)) {
                $userInfo = array_merge($userInfo, [
                    'admin_id' => session(self::ADMIN_SESSION_KEY)
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(
                ApiError::getError($e->getCode(), $e->getMessage()),
                Response::HTTP_BAD_REQUEST
            );
        }
        return response()->json($userInfo);
    }

    /**
     * Получить виджеты для пользователя
     */
    public function getWidgets(WidgetService $service): JsonResponse
    {
        return response()->json($service->getForUser());
    }

    public function create(Request $request): JsonResponse
    {
        try {
            $user = new User();
            $user->name = $request->name;
            $user->login = $request->login;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();

            $success = true;
            $message = 'Пользователь успешно создан';
        } catch (QueryException $ex) {
            $success = false;
            $message = $ex->getMessage();
        }

        $response = [
            'success' => $success,
            'message' => $message,
        ];
        return response()->json($response);
    }

    public function update(Request $request, RecordService $service): JsonResponse
    {
        try {
            $saveResult = $service->saveRecord(
                modelClass: User::class,
                recordId: Auth::id(),
                formData: [...$request->post(), 'deleted_at' => null],
                uploads: $request->file()
            );
        } catch (\Exception $e) {
            return response()->json(
                ApiError::getError($e->getCode(), $e->getMessage()),
                Response::HTTP_BAD_REQUEST
            );
        }

        return response()->json($saveResult, Response::HTTP_OK);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('login', 'password');

        if (Auth::attempt($credentials)) {
            session()->forget(self::ADMIN_SESSION_KEY);

            if (!Auth::user()->has_access) {
                return back()->withErrors([
                    'error' => 'У пользователя нет доступа к системе!'
                ])->onlyInput('login');
            }

            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'error' => 'Пользователь не найден!'
        ])->onlyInput('login');
    }

    public function logout(Request $request): Redirector|Application|RedirectResponse
    {
        Auth::logout();

        session()->invalidate();

        session()->regenerateToken();

        session()->forget(self::ADMIN_SESSION_KEY);

        return redirect()->route('login.show');
    }

    public function authAsUser(Request $request): JsonResponse
    {
        $userId = (int)$request->post('user_id', 0);

        if ($userId && (Auth::user()->isRoot())) {
            session([self::ADMIN_SESSION_KEY => Auth::id()]);
            Auth::loginUsingId($userId);

            return response()->json(true, Response::HTTP_OK);
        }

        return response()->json(false, Response::HTTP_BAD_REQUEST);
    }

    public function authAsAdmin(): JsonResponse
    {
        if (session()->has(self::ADMIN_SESSION_KEY)) {
            $adminId = (int)session(self::ADMIN_SESSION_KEY);
            Auth::loginUsingId($adminId);
            session()->forget(self::ADMIN_SESSION_KEY);

            return response()->json(true, Response::HTTP_OK);
        }

        return response()->json(false, Response::HTTP_BAD_REQUEST);
    }
}
