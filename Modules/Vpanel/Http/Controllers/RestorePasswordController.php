<?php

namespace Modules\Vpanel\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Modules\Vpanel\Entities\User;

class RestorePasswordController extends Controller
{
    public function index()
    {
        return view('vpanel::restore_password');
    }

    public function restore(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
        ]);

        $token = Str::random(64);

        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        Mail::send('vpanel::email.restore_password', ['token' => $token], function($message) use($request){
            $message->to($request->email);
            $message->subject('Восстановление пароля');
        });

        return back()->with('message', 'Мы отправили ссылку для восстановления пароля на ваш e-mail!');
    }
}
