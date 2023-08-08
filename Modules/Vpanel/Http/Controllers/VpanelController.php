<?php

namespace Modules\Vpanel\Http\Controllers;

use Illuminate\Routing\Controller;

class VpanelController extends Controller
{
    public function index()
    {
        if (!\Auth::check()) {
            return redirect()->route('login.show');
        }
        return view('vpanel::index');
    }
}
