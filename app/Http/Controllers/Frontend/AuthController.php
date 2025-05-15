<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\EmailVerification;
use App\Models\OtpVerify;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function login(Request $request)
    {

        if (Auth::check()){
            return  $this->redirect_dashboard();
        }

        if ($request->isMethod('post')){
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (Auth::attempt(['email' => $request->email, 'password' =>$request->password], $request->remember)){
                $request->session()->regenerate();

                $user = Auth::user();
                if ($user->user_type == 'admin'){
                    return $this->redirect_dashboard();
                }else{


                    if($user->is_email_verify == 0 && get_settings('email_verification') == 1){
                        $otp = generateUniqueCode();
                        OtpVerify::updateOrCreate(
                            ['identifier' => $user->email],
                            ['otp' => $otp, 'created_at' => now()]
                        );
                        Mail::to($user->email)->send(new EmailVerification($user, $otp));
                        Session::put('verify_email', $user->email);

                        return redirect()->route('email_verify')->with('error', 'Your email not verified. Please Verify your email');

                    }

                    if ($user->is_phone_verify == 0 && get_settings('phone_verification') == 1){
                        $otp = generateUniqueCode();

                        OtpVerify::updateOrCreate(
                            ['identifier' => $user->phone],
                            ['otp' => $otp, 'created_at' => now()]
                        );

                        Session::put('verify_phone', $user->phone);

                        return redirect()->route('phone_verify')->with('error', 'Your phone not verified. Please verify your phone');

                    }
                    return $this->redirect_dashboard();
                }



            }else{
                throw ValidationException::withMessages(['email' => 'These credentials do not match our records.']);
            }

        }
        return Inertia::render('frontend/auth/Login');
    }

    public function redirect_dashboard()
    {
        if (Auth::user()->user_type == 'admin'){
            return redirect()->intended(route('admin.dashboard'));
        }elseif (Auth::user()->user_type == 'super_admin'){
            dd('Super Admin');
        }else{
            return redirect()->intended(route('user.dashboard'))->with('success', 'Login Success');
        }
    }

    public function register(Request $request)
    {
        if (Auth::check()){
            return  $this->redirect_dashboard();
        }

        if ($request->isMethod('post')){
            $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'phone' => 'required|min:6|unique:users,phone',
                'company_name' => 'required',
                'address' => 'required',
                'city' => 'required',
                'state' => 'required',
                'zip_code' => 'required',
                'country' => 'required',
                'password' => 'required|confirmed|min:8',
            ]);
            $user = new User();
            $user->user_type = 'user';
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->company_name = $request->company_name;
            $user->street_address = $request->address;
            $user->password = Hash::make($request->password);
            $user->save();


            if(get_settings('email_verification') == 1){
                $otp = generateUniqueCode();
                OtpVerify::updateOrCreate(
                    ['identifier' => $user->email],
                    ['otp' => $otp, 'created_at' => now()]
                );
                Mail::to($user->email)->send(new EmailVerification($user, $otp));
                Session::put('verify_email', $user->email);

                return redirect()->route('email_verify')->with('success', 'Registration Successful. Please Verify your email');

            }else{

                $user->is_email_verify = 1;
                $user->update();

                if (get_settings('phone_verification') == 1){
                    //send message via phone
                    $otp = generateUniqueCode();

                    OtpVerify::updateOrCreate(
                        ['identifier' => $user->phone],
                        ['otp' => $otp, 'created_at' => now()]
                    );

                    Session::put('verify_phone', $user->phone);

                    return redirect()->route('phone_verify')->with('success', 'Registration Successful. Please verify your phone');
                }
            }

            //login user
            Auth::login($user);
            $request->session()->regenerate();
            return $this->redirect_dashboard();
        }

        $json = file_get_contents(public_path('/country_list.json'));
        $countries = json_decode($json, true)['countries'];

        return Inertia::render('frontend/auth/Register', [
            'countries' => $countries
        ]);
    }

    public function email_verify(Request $request)
    {
        $email = session('verify_email');
        if (!$email) return redirect()->route('register');
        if ($request->isMethod('post')){

            $request->validate([
               'otp' => 'required|min:5',
               'email' => 'required',
            ]);

            if ($request->email != $email){
                return redirect()->back()->with('error', 'Something went to wrong');
            }

            $user = User::where('email', $email)->first();
            if (empty($user)){
                return redirect('/')->with('error', 'Something went to wrong');
            }

            $otp_data = OtpVerify::where('identifier', $email)->first();
            if (empty($otp_data)){
                return redirect('/')->with('error', 'Something went to wrong');
            }

            if ($otp_data->otp == $request->otp){

                $user->is_email_verify = 1;
                $user->update();

                OtpVerify::where('identifier', $user->email)->delete();

                Session::forget('verify_email');

                if (get_settings('phone_verification') == 1){
                    Session::put('verify_phone', $user->phone);

                    //send message via phone
                    $otp = generateUniqueCode();

                    OtpVerify::updateOrCreate(
                        ['identifier' => $user->phone],
                        ['otp' => $otp, 'created_at' => now()]
                    );

                    return redirect()->route('phone_verify')->with('success', 'Email verified successfully. Please verify your phone');
                }else{
                    Auth::login($user);
                    $request->session()->regenerate();
                    return $this->redirect_dashboard();
                    //return redirect()->route('login')->with('success', 'Email verified successfully');
                }

            }else{
                throw ValidationException::withMessages(['otp' => 'OTP is not matched!']);
            }

        }
        return Inertia::render('frontend/auth/VerifyEmail', ['email' => $email]);
    }

    public function resend_otp(Request $request)
    {
        if ($request->identifier == '' && $request->type == ''){
            return redirect()->back()->with('error', 'Something went to wrong');
        }

        if ($request->type == 'email'){
            $user = User::where('email', $request->identifier)->first();
        }else{
            $user = User::where('phone', $request->identifier)->first();
        }


        if (empty($user)){
            return redirect('/')->with('error', 'Something went to wrong');
        }

        $otp = generateUniqueCode();

        OtpVerify::updateOrCreate(
            ['identifier' => $request->identifier],
            ['otp' => $otp]
        );

        if ($request->type == 'email'){
            Mail::to($request->identifier)->send(new EmailVerification($user, $otp));
            Session::put('verify_email', $user->email);
        }else{
            //send otp via phone
            Session::put('verify_phone', $user->phone);
        }

        return back()->with('message', 'OTP resend successfully');

    }

    public function phone_verify(Request $request)
    {
        $phone = session('verify_phone');
        if (!$phone) return redirect()->route('register');
        if ($request->isMethod('post')){

            $request->validate([
                'otp' => 'required|min:5',
                'phone' => 'required|numeric',
            ]);

            if ($request->phone != $phone){
                return redirect()->back()->with('error', 'Something went to wrong');
            }

            $user = User::where('phone', $phone)->first();
            if (empty($user)){
                return redirect('/')->with('error', 'Something went to wrong');
            }

            $otp_data = OtpVerify::where('identifier', $phone)->first();
            if (empty($otp_data)){
                return redirect('/')->with('error', 'Something went to wrong');
            }

            if ($otp_data->otp == $request->otp){

                $user->is_phone_verify = 1;
                $user->update();

                OtpVerify::where('identifier', $user->phone)->delete();

                Session::forget('verify_phone');

                Auth::login($user);
                $request->session()->regenerate();
                return $this->redirect_dashboard();

                //return redirect()->route('login')->with('success', 'Phone verified successfully');

            }else{
                throw ValidationException::withMessages(['otp' => 'OTP is not matched!']);
            }

        }
        return Inertia::render('frontend/auth/VerifyPhone', ['phone' => $phone]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->regenerateToken(); // Only regenerates CSRF token for security
        return redirect()->route('login');
    }

}
