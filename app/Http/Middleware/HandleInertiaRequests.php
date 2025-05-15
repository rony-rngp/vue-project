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
       ]);
    }
}
