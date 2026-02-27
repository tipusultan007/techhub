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
                        <i class="fas fa-user-shield text-[#2dae9a] text-2xl"></i>
                    </div>
                    <h2 class="text-2xl font-black text-gray-900 tracking-tight">{{ __('Verification Method') }}</h2>
                    <p class="text-sm text-gray-500 mt-2">
                        {{ __('Choose a verification method to confirm your identity.') }}
                    </p>
                </div>

                <form method="POST" action="{{ route('two-factor.change-method') }}" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 gap-4">
                        <label class="relative flex items-center p-4 rounded-2xl border-2 cursor-pointer transition-all {{ $user->two_factor_type === 'totp' ? 'border-[#2dae9a] bg-emerald-50/30' : 'border-gray-50 hover:bg-gray-50' }}">
                            <input type="radio" name="method" value="totp" class="hidden" {{ $user->two_factor_type === 'totp' ? 'checked' : '' }}>
                            <div class="w-10 h-10 rounded-xl bg-white border border-gray-100 flex items-center justify-center mr-4 shrink-0 shadow-sm">
                                <i class="fas fa-mobile-alt text-lg text-gray-400"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-gray-900 leading-tight">{{ __('Authenticator App') }}</p>
                                <p class="text-[0.65rem] text-gray-400 mt-1 uppercase tracking-widest">{{ __('TOTP Verification') }}</p>
                            </div>
                            <div class="check-icon {{ $user->two_factor_type === 'totp' ? '' : 'hidden' }}">
                                <i class="fas fa-check-circle text-[#2dae9a]"></i>
                            </div>
                        </label>

                        <label class="relative flex items-center p-4 rounded-2xl border-2 cursor-pointer transition-all {{ $user->two_factor_type === 'email' ? 'border-[#2dae9a] bg-emerald-50/30' : 'border-gray-50 hover:bg-gray-50' }}">
                            <input type="radio" name="method" value="email" class="hidden" {{ $user->two_factor_type === 'email' ? 'checked' : '' }}>
                            <div class="w-10 h-10 rounded-xl bg-white border border-gray-100 flex items-center justify-center mr-4 shrink-0 shadow-sm">
                                <i class="fas fa-envelope-open-text text-lg text-gray-400"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-gray-900 leading-tight">{{ __('Email Address') }}</p>
                                <p class="text-[0.65rem] text-gray-400 mt-1 uppercase tracking-widest">{{ $user->email }}</p>
                            </div>
                            <div class="check-icon {{ $user->two_factor_type === 'email' ? '' : 'hidden' }}">
                                <i class="fas fa-check-circle text-[#2dae9a]"></i>
                            </div>
                        </label>
                    </div>

                    <button type="submit" class="w-full py-4 mt-6 bg-[#2dae9a] hover:bg-[#248e7e] text-white rounded-2xl font-bold shadow-lg shadow-emerald-900/20 transform hover:-translate-y-0.5 transition-all text-sm uppercase tracking-widest flex items-center justify-center gap-2">
                        <i class="fas fa-exchange-alt"></i> {{ __('Switch Method') }}
                    </button>
                </form>

                <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">
                        {{ __('Emergency Access') }}
                    </p>
                    <a href="{{ route('two-factor.verify') }}" class="inline-flex w-full items-center justify-center gap-2 px-6 py-4 rounded-2xl bg-gray-50 text-gray-600 hover:bg-gray-100 font-bold text-xs uppercase tracking-widest transition-all">
                        <i class="fas fa-key"></i> {{ __('Use Recovery Code') }}
                    </a>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-8">
                <p class="text-white/40 text-xs font-medium uppercase tracking-widest">
                    &copy; {{ date('Y') }} {{ settings('site_name', 'Tech Hub') }}. Secure Admin Portal.
                </p>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('input[name="method"]').forEach(input => {
            input.addEventListener('change', function() {
                document.querySelectorAll('label').forEach(label => {
                    label.classList.remove('border-[#2dae9a]', 'bg-emerald-50/30');
                    label.classList.add('border-gray-50');
                    label.querySelector('.check-icon').classList.add('hidden');
                });
                
                if (this.checked) {
                    const label = this.closest('label');
                    label.classList.remove('border-gray-50');
                    label.classList.add('border-[#2dae9a]', 'bg-emerald-50/30');
                    label.querySelector('.check-icon').classList.remove('hidden');
                }
            });
        });
    </script>
</x-admin-auth-layout>
