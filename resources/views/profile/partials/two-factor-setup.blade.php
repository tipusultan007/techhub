<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Two-Factor Authentication') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Add additional security to your account using two-factor authentication.') }}
        </p>
    </header>

    @if (! $user->hasTwoFactorEnabled())
        <div class="space-y-6">
            {{-- TOTP Setup --}}
            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <h3 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Setup Authenticator App') }}</h3>
                <div class="mb-4">
                    {!! $qrCodeSvg !!}
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    {{ __('Scan this QR code with your authenticator app (e.g., Google Authenticator) and enter the code below.') }}
                </p>

                <form method="POST" action="{{ route('two-factor.totp.enable') }}" class="flex items-center space-x-4">
                    @csrf
                    <x-text-input name="code" type="text" class="block w-32" placeholder="Code" required />
                    <x-primary-button>{{ __('Enable TOTP') }}</x-primary-button>
                </form>
            </div>

            <div class="relative">
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                </div>
                <div class="relative flex justify-center">
                    <span class="px-2 bg-white dark:bg-gray-800 text-sm text-gray-500">{{ __('OR') }}</span>
                </div>
            </div>

            {{-- Email Setup --}}
            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <h3 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Use Email Verification') }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    {{ __('Verification codes will be sent to ') }} <strong>{{ $user->email }}</strong>.
                </p>
                <form method="POST" action="{{ route('two-factor.email.enable') }}">
                    @csrf
                    <x-primary-button>{{ __('Enable Email 2FA') }}</x-primary-button>
                </form>
            </div>
        </div>
    @else
        <div class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-green-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm font-medium text-green-800 dark:text-green-200">
                    {{ __('Two-factor authentication is currently enabled via ') }} <strong>{{ strtoupper($user->two_factor_type) }}</strong>.
                </span>
            </div>

            <div class="mt-4">
                <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 mb-2">{{ __('Recovery Codes') }}</h4>
                <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">{{ __('Store these securely. You can use them to access your account if you lose your primary 2FA device.') }}</p>
                <div class="grid grid-cols-2 gap-2 font-mono text-xs p-2 bg-white dark:bg-gray-900 rounded border dark:border-gray-700">
                    @if($user->two_factor_recovery_codes)
                        @foreach ($user->two_factor_recovery_codes as $code)
                            <div>{{ $code }}</div>
                        @endforeach
                    @else
                        <div class="col-span-2 text-center text-gray-500 italic">{{ __('No recovery codes generated.') }}</div>
                    @endif
                </div>
            </div>

            <form method="POST" action="{{ route('two-factor.disable') }}" class="mt-6">
                @csrf
                <x-danger-button onclick="return confirm('Are you sure you want to disable 2FA?')">
                    {{ __('Disable 2FA') }}
                </x-danger-button>
            </form>
        </div>
    @endif
</section>
