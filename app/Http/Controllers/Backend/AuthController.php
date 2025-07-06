<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\OdooService;
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
        /*$odoo = new OdooService();
        $result = $odoo->syncContact('+8809638823932', [
            'name' => 'Tests',
            'email' => 'test@gms.com',
            'company_type' => 'person'
        ]);
        dd($result);

        $odoo = new OdooService();
        $contacts = $odoo->searchContactByNumber('+8809638823932');
        dd($contacts);*/

       /* $odoo = new OdooService();
        $contacts = $odoo->searchContactByNumber('+8809638823932');
        dd($contacts);*/

        return Inertia::render('admin/Dashboard');
    }

    public function search_caller(Request $request)
    {
        $odoo = new OdooService();
        $contacts = $odoo->searchContactByNumber($request->caller);
        dd($contacts);
    }

    public function sip()
    {
        if (Auth::id() == 2){
            $user = '1001';
            $pass = '46b9de07cd3025ab5fcc6bcbe43a4c66';
        }else{
            $user = '1002';
            $pass = 'd79da2cb92adaade8532d630d5662763';
        }
        return Inertia::render('admin/Sip', [
            'sipUser' => $user,
            'sipPassword' => $pass,
            'sipServer' => 'wss://pbx1.asteriskbd.com:8089/ws',
            'sipDomain' => 'pbx1.asteriskbd.com',
        ]);
    }

}
