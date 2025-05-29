<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $settings = collect(app('app_settings'))->keyBy('key');
        //$settings = app('app_settings');

        if (Auth::id() == 2){
            $user = '1001';
            $pass = '46b9de07cd3025ab5fcc6bcbe43a4c66';
        }else{
            $user = '1002';
            $pass = 'd79da2cb92adaade8532d630d5662763';
        }

       return array_merge(parent::share($request), [
            'auth' => [
                'user' => fn () => Auth::guard('web')->user(),
                'admin' => fn () => Auth::guard('admin')->user(),
            ],
           'flash' => [
               'success' => fn () => $request->session()->get('success'),
               'error' => fn () => $request->session()->get('error'),
           ],
           'settings' => $settings,

           //for sip
           'sipUser' => $user,
           'sipPassword' => $pass,
           'sipServer' => 'wss://pbx1.asteriskbd.com:8089/ws',
           'sipDomain' => 'pbx1.asteriskbd.com',

       ]);
    }
}
