<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Inertia\Inertia;


class AuthController extends Controller
{

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->regenerateToken(); // Only regenerates CSRF token for security
        return redirect()->route('admin.login');
    }

    public function dashboard()
    {
        return Inertia::render('admin/Dashboard');
    }

    public function sip()
    {
        return Inertia::render('admin/Sip', [
            'sipUser' => '1001',
            'sipPassword' => '46b9de07cd3025ab5fcc6bcbe43a4c66',
            'sipServer' => 'wss://pbx1.asteriskbd.com:8089/ws',
            'sipDomain' => 'pbx1.asteriskbd.com',
        ]);
    }

}
