<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class OtpController extends Controller
{
    public function show()
    {
        return view('auth.verify-otp');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric',
        ]);

        $email = Session::get('email_to_verify');

        if (!$email) {
            return redirect('/login')->with('error', 'Session expired. Please register again.');
        }

        $cachedOtp = Cache::get('otp_' . $email);

        if ($request->otp == $cachedOtp) {
            $user = User::where('email', $email)->first();

            if (!$user->email_verified_at) {
                $user->email_verified_at = now();
                $user->save();
            }

            Auth::login($user, true);

            Cache::forget('otp_' . $email);
            Session::forget('email_to_verify');

            return redirect('/dashboard');
        }

        return back()->withErrors(['otp' => 'The provided OTP is incorrect.']);
    }


    public function showLoginOtp()
    {
        return view('auth.verify-otp');
    }

    public function verifyLoginOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric',
        ]);

        $userId = Session::get('login_user_id');
        $remember = Session::get('login_remember', true);

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        $cachedOtp = Cache::get('otp_' . $userId);

        if ($request->otp == $cachedOtp) {
            Auth::loginUsingId($userId, $remember);

            $user = Auth::user();
            $user->update([
                'last_login_at' => now(),
            ]);

            Cache::forget('otp_' . $userId);
            Session::forget('login_user_id');
            Session::forget('login_remember');

            $request->session()->regenerate();

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['otp' => 'Invalid OTP code.']);
    }
}
