<x-admin-auth-layout>
    <div class="min-h-screen flex items-center justify-center p-6 bg-[#024959]" style="background: radial-gradient(circle at top right, #0a192f 0%, #024959 100%);">
        <div class="w-full max-w-md">
            <!-- Logo Section -->
            <div class="text-center mb-8">
                <a href="{{ route('home') }}" class="inline-block group">
                    @if(settings('site_logo'))
                        <img src="{{ asset(settings('site_logo')) }}" alt="Logo" class="h-16 mx-auto group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-16 h-16 bg-[#2dae9a] rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-900/20 mx-auto group-hover:scale-110 transition-transform">
                            <i class="fas fa-bolt text-white text-2xl"></i>
                        </div>
                        <div class="mt-4">
                            <span class="text-2xl font-extrabold tracking-tight text-white block leading-none uppercase">TECH<span class="text-[#2dae9a]">HUB</span></span>
                            <span class="text-[0.65rem] font-bold uppercase tracking-[0.2em] text-emerald-400 mt-2 block">Enterprise Portal</span>
                        </div>
                    @endif
                </a>
            </div>

            <!-- Auth Card -->
            <div class="bg-white/95 backdrop-blur-md rounded-3xl shadow-2xl overflow-hidden p-8 sm:p-10 border border-white/10">
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-[#2dae9a]/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shield-halved text-[#2dae9a] text-2xl"></i>
                    </div>
                    <h2 class="text-2xl font-black text-gray-900 tracking-tight">{{ __('Secure Login') }}</h2>
                    <p class="text-sm text-gray-500 mt-2">
                        {{ __('Two-factor authentication is required to access the admin portal.') }}
                    </p>
                </div>

                @if ($type === 'email')
                    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-start gap-3">
                        <i class="fas fa-paper-plane text-[#2dae9a] mt-0.5"></i>
                        <div>
                            <p class="text-[0.8rem] font-bold text-emerald-800 leading-tight">{{ __('Code Sent!') }}</p>
                            <p class="text-xs text-emerald-600 mt-1">{{ __('A verification code has been sent to your email: ') . $email }}</p>
                        </div>
                    </div>
                @endif

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('two-factor.verify.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="code" class="text-[0.65rem] font-bold text-gray-400 uppercase tracking-widest block mb-2">{{ __('Verification Code') }}</label>
                        <input id="code" class="block w-full px-4 py-4 rounded-2xl bg-gray-50 border-gray-100 text-gray-900 font-bold tracking-[0.5em] text-center text-xl focus:border-[#2dae9a] focus:ring focus:ring-[#2dae9a]/20 transition-all" type="text" name="code" required autofocus autocomplete="one-time-code" placeholder="000000" />
                        <x-input-error :messages="$errors->get('code')" class="mt-2" />
                    </div>

                    <button type="submit" class="w-full py-4 bg-[#2dae9a] hover:bg-[#248e7e] text-white rounded-2xl font-bold shadow-lg shadow-emerald-900/20 transform hover:-translate-y-0.5 transition-all text-sm uppercase tracking-widest flex items-center justify-center gap-2">
                        <i class="fas fa-check-circle"></i> {{ __('Verify & Authenticate') }}
                    </button>

                    <div class="flex flex-col gap-4 text-center mt-6 pt-6 border-t border-gray-100">
                        <a class="text-xs font-bold text-gray-400 hover:text-[#2dae9a] uppercase tracking-widest transition-colors flex items-center justify-center gap-2" href="{{ route('two-factor.select') }}">
                            <i class="fas fa-redo"></i> {{ __('Try another verification method') }}
                        </a>

                        @if ($type === 'email')
                            <button type="submit" form="resend-form" class="text-xs font-bold text-[#2dae9a] hover:text-[#248e7e] uppercase tracking-widest transition-colors flex items-center justify-center gap-2">
                                <i class="fas fa-envelope"></i> {{ __('Didn\'t get the code? Resend OTP') }}
                            </button>
                        @endif
                    </div>
                </form>

                <form id="resend-form" method="POST" action="{{ route('two-factor.resend') }}">
                    @csrf
                </form>
            </div>

            <!-- Footer -->
            <div class="text-center mt-8">
                <p class="text-white/40 text-xs font-medium uppercase tracking-widest">
                    &copy; {{ date('Y') }} {{ settings('site_name', 'Tech Hub') }}. Secure Admin Portal.
                </p>
            </div>
        </div>
    </div>
</x-admin-auth-layout>
