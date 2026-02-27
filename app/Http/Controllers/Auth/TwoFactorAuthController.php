<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\TwoFactorOtpNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FALaravel\Support\Authenticator;

class TwoFactorAuthController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (! $user || ! $user->hasTwoFactorEnabled() || $request->session()->has('two_factor_verified')) {
            return redirect()->intended(route('dashboard'));
        }

        return view('auth.2fa-verify', [
            'type' => $user->two_factor_type,
            'email' => $user->email,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $request->validate(['code' => 'required']);

        $throttleKey = '2fa-verify:' . $user->id . '|' . $request->ip();

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'code' => 'Too many verification attempts. Please try again in ' . ceil($seconds / 60) . ' minutes.'
            ]);
        }

        // 1. Check TOTP
        if ($user->two_factor_type === 'totp') {
            $authenticator = app(Authenticator::class)->boot($request);
            if ($authenticator->verifyGoogle2FA($user->two_factor_secret, $request->code)) {
                \Illuminate\Support\Facades\RateLimiter::clear($throttleKey);
                $request->session()->put('two_factor_verified', true);
                return redirect()->intended(route('dashboard'));
            }
        }

        // 2. Check Email OTP
        if ($user->two_factor_type === 'email') {
            if ($user->two_factor_otp === $request->code && now()->lt($user->two_factor_otp_expires_at)) {
                $user->forceFill(['two_factor_otp' => null, 'two_factor_otp_expires_at' => null])->save();
                \Illuminate\Support\Facades\RateLimiter::clear($throttleKey);
                $request->session()->put('two_factor_verified', true);
                return redirect()->intended(route('dashboard'));
            }
        }

        // 3. Check Recovery Codes
        $recoveryCodes = (array) $user->two_factor_recovery_codes;
        if (in_array($request->code, $recoveryCodes)) {
            $user->two_factor_recovery_codes = array_values(array_diff($recoveryCodes, [$request->code]));
            $user->save();
            \Illuminate\Support\Facades\RateLimiter::clear($throttleKey);
            $request->session()->put('two_factor_verified', true);
            return redirect()->intended(route('dashboard'));
        }

        \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 300); // 5 minutes decay

        return back()->withErrors(['code' => 'The provided code is invalid.']);
    }

    public function select()
    {
        $user = Auth::user();
        return view('auth.2fa-select', ['user' => $user]);
    }

    public function changeMethod(Request $request)
    {
        $user = Auth::user();
        $request->validate(['method' => 'required|in:totp,email']);
        
        $user->two_factor_type = $request->method;
        $user->save();

        if ($request->method === 'email') {
            $otp = $user->generateTwoFactorOtp();
            $user->notify(new TwoFactorOtpNotification($otp));
        }

        return redirect()->route('two-factor.verify')->with('status', 'Verification method changed.');
    }

    public function resend()
    {
        $user = Auth::user();
        if ($user->two_factor_type === 'email') {
            $otp = $user->generateTwoFactorOtp();
            $user->notify(new TwoFactorOtpNotification($otp));
            return back()->with('status', 'OTP resent to your email.');
        }
        return back();
    }
}
