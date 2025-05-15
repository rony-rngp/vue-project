<?php

function get_settings($key){
    $config = null;
    $data = collect(app('app_settings'))->where('key', $key)->first();
    if (isset($data) && $data != null){
        $config = json_decode($data['value'], true);
        if (is_null($config)) {
            $config = $data['value'];
        }
    }
    return $config;
}

function generateUniqueCode()
{
    do {
        $code = rand(000000, 999999);
    } while (\App\Models\OtpVerify::where('otp', $code)->exists());

    return $code;
}

function upload_file($dir, $image = null){
    if ($image != null) {
        $ext = $image->getClientOriginalExtension();
        $imageName = \Carbon\Carbon::now()->toDateString() . "-" . uniqid() . "." . $ext;
        if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($dir)) {
            \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory($dir);
        }
        \Illuminate\Support\Facades\Storage::disk('public')->put($dir . $imageName, file_get_contents($image));
    } else {
        $imageName = '';
    }
    return $dir.$imageName;
}

function update_file( $dir, $old_image, $image = null){
    if ($old_image != '' && \Illuminate\Support\Facades\Storage::disk('public')->exists( $old_image)) {
        \Illuminate\Support\Facades\Storage::disk('public')->delete($old_image);
    }
    $imageName = upload_file($dir, $image);
    return $imageName;
}

function delete_file($full_path){
    if ($full_path != '' && \Illuminate\Support\Facades\Storage::disk('public')->exists($full_path)) {
        \Illuminate\Support\Facades\Storage::disk('public')->delete($full_path);
    }
}
