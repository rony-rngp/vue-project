<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\OdooService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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


        /*$filePath = storage_path('app/public/voice_file/2025-05-15-6825cdf168b8f.mp3');

        if (!file_exists($filePath)) {
            return 'Audio file not found!';
        }

        $audio = fopen($filePath, 'r');

        $response = Http::withHeaders([
            'Authorization' => 'Token ' . env('DEEPGRAM_API_KEY'),
            'Content-Type' => 'audio/mpeg',
        ])->withBody($audio, 'audio/mpeg')
            ->post('https://api.deepgram.com/v1/listen?punctuate=true&smart_format=true');

        if ($response->successful()) {
            $result = $response->json();
            $transcript = $result['results']['channels'][0]['alternatives'][0]['transcript'] ?? 'No transcript found.';
            return '✅ Transcription: ' . $transcript;
        }

        return '❌ Failed: ' . $response->body();*/


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
