<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember', true);

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            Auth::logout();

            $otp = rand(100000, 999999);
            Cache::put('otp_' . $user->id, $otp, 600);

            Session::put('login_user_id', $user->id);
            Session::put('login_remember', $remember);

            try {
                Mail::to($user->email)->send(new OtpMail($otp));
            } catch (\Exception $e) {
                return back()->withErrors(['email' => 'Failed to send OTP. Please try again.']);
            }

            return redirect()->route('otp.verify');
        }

        return back()
        ->withErrors(['email' => 'Invalid email or password.'])
        ->onlyInput('email');

        // $request->session()->regenerate();

        // $request->user()->update([
        //     'last_login_at' => now(),
        // ]);

        // return redirect()->route('dashboard');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create($validated);

        $otp = rand(100000, 999999);

        Cache::put('otp_' . $user->email, $otp, 600);

        Mail::to($user->email)->send(new OtpMail($otp));

        Session::put('email_to_verify', $user->email);

        return redirect()->route('otp.verify');

        // Auth::login($user);
        // $request->session()->regenerate();

        // return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function showProfile(Request $request)
    {
        return view('profile', [
            'user' => $request->user(),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'target_hours' => ['required', 'numeric', 'min:0.01', 'max:10000'],
            'current_password' => ['nullable', 'required_with:new_password', 'current_password'],
            'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $data = [
            'name' => $validated['name'],
            'target_hours' => $validated['target_hours'],
        ];

        if (!empty($validated['new_password'])) {
            $data['password'] = $validated['new_password'];
        }

        $request->user()->update($data);

        return back()->with('status', 'Profile updated successfully.');
    }
}
