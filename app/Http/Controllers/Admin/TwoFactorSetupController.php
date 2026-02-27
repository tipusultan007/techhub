<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Notifications\TwoFactorOtpNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FALaravel\Support\Authenticator;

class TwoFactorSetupController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $google2fa = app('pragmarx.google2fa');
        
        if (! $user->two_factor_secret) {
            $user->two_factor_secret = $google2fa->generateSecretKey();
            $user->save();
        }

        $qrCodeSvg = $google2fa->getQRCodeInline(
            config('app.name'),
            $user->email,
            $user->two_factor_secret
        );

        return view('profile.partials.two-factor-setup', [
            'user' => $user,
            'qrCodeSvg' => $qrCodeSvg,
            'secret' => $user->two_factor_secret,
        ]);
    }

    public function enableTotp(Request $request)
    {
        $user = Auth::user();
        $request->validate(['code' => 'required']);

        $google2fa = app('pragmarx.google2fa');
        if ($google2fa->verifyKey($user->two_factor_secret, $request->code)) {
            $user->forceFill([
                'two_factor_confirmed_at' => now(),
                'two_factor_type' => 'totp',
                'two_factor_recovery_codes' => $this->generateRecoveryCodes(),
            ])->save();

            return back()->with('status', 'two-factor-enabled');
        }

        return back()->withErrors(['code' => 'Invalid verification code.']);
    }

    public function enableEmail(Request $request)
    {
        $user = Auth::user();
        $user->forceFill([
            'two_factor_confirmed_at' => now(),
            'two_factor_type' => 'email',
            'two_factor_recovery_codes' => $this->generateRecoveryCodes(),
        ])->save();

        return back()->with('status', 'two-factor-enabled');
    }

    public function disable()
    {
        Auth::user()->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
            'two_factor_type' => null,
        ])->save();

        return back()->with('status', 'two-factor-disabled');
    }

    protected function generateRecoveryCodes()
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = bin2hex(random_bytes(5));
        }
        return $codes;
    }
}
