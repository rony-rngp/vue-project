<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function settings(Request $request)
    {
        if ($request->isMethod('post')){

            $request->validate([
                'website_name' => 'required',
                'email' => 'required',
                'phone' => 'required',
                'address' => 'required',
                'copyright_text' => 'required',
            ]);

            Setting::updateOrInsert(['key' => 'website_name'], [
                'value' => $request['website_name']
            ]);
            Setting::updateOrInsert(['key' => 'email'], [
                'value' => $request['email']
            ]);
            Setting::updateOrInsert(['key' => 'phone'], [
                'value' => $request['phone']
            ]);
            Setting::updateOrInsert(['key' => 'address'], [
                'value' => $request['address']
            ]);
            Setting::updateOrInsert(['key' => 'copyright_text'], [
                'value' => $request['copyright_text']
            ]);
            Setting::updateOrInsert(['key' => 'email_verification'], [
                'value' => $request['email_verification']
            ]);
            Setting::updateOrInsert(['key' => 'phone_verification'], [
                'value' => $request['phone_verification']
            ]);

            return redirect()->back()->with('success', 'Settings updated successfully');
        }

        $settings = Setting::get()->keyBy('key');
        return Inertia::render('admin/Settings',[
            'settings' => $settings
        ]);
    }
}
