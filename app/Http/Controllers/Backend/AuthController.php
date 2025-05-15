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
            'sipPassword' => 'yourpassword',
            'sipServer' => 'wss://yourdomain.com:8089/ws',
            'sipDomain' => 'yourdomain.com',
        ]);
    }

}
