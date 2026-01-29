<x-admin-auth-layout>
    <style>
        :root {
            --admin-navy: #0f172a;
            --admin-primary: #3b82f6;
            --admin-primary-hover: #2563eb;
            --admin-border: #e2e8f0;
            --admin-text-main: #1e293b;
            --admin-text-muted: #64748b;
        }

        .login-page {
            display: flex;
            min-height: 100vh;
            background: #fff;
        }

        .visual-side {
            flex: 1.2;
            background: var(--admin-navy);
            background-image: radial-gradient(circle at 20% 30%, rgba(59, 130, 246, 0.15) 0%, transparent 50%),
                              radial-gradient(circle at 80% 70%, rgba(16, 185, 129, 0.1) 0%, transparent 50%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 80px;
            color: white;
            position: relative;
        }

        .visual-side::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: url('https://www.transparenttextures.com/patterns/carbon-fibre.png');
            opacity: 0.05;
        }

        .visual-content {
            position: relative;
            z-index: 1;
            max-width: 500px;
        }

        .visual-content h1 {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 24px;
            letter-spacing: -0.02em;
        }

        .visual-content p {
            font-size: 1.125rem;
            color: rgba(255, 255, 255, 0.7);
            line-height: 1.6;
        }

        .form-side {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            background: #f8fafc;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: white;
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
        }

        .form-header {
            margin-bottom: 32px;
            text-align: center;
        }

        .form-header h2 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--admin-text-main);
            margin-bottom: 8px;
        }

        .form-header p {
            color: var(--admin-text-muted);
            font-size: 0.95rem;
        }

        .input-group {
            margin-bottom: 20px;
            position: relative;
        }

        .input-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--admin-text-main);
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--admin-text-muted);
            font-size: 1.2rem;
        }

        .custom-input {
            width: 100%;
            padding: 12px 16px 12px 42px;
            border: 1.5px solid var(--admin-border);
            border-radius: 12px;
            font-size: 0.95rem;
            transition: all 0.2s;
            outline: none;
            background: #fff;
        }

        .custom-input:focus {
            border-color: var(--admin-primary);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: var(--admin-navy);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 10px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .btn-login:hover {
            background: var(--admin-primary);
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.2);
        }

        .form-footer {
            margin-top: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.875rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            color: var(--admin-text-muted);
        }

        .btn-forgot {
            color: var(--admin-primary);
            font-weight: 600;
            text-decoration: none;
        }

        .btn-forgot:hover {
            text-decoration: underline;
        }

        .error-msg {
            color: #ef4444;
            font-size: 0.8rem;
            margin-top: 5px;
            display: block;
        }

        @media (max-width: 1024px) {
            .visual-side { display: none; }
            .form-side { background: #f1f5f9; }
        }
    </style>

    <div class="login-page">
        <!-- Visual Sidebar -->
        <div class="visual-side">
            <div class="visual-content">
                <h1>{{ settings('site_name', 'Tech Hub') }} Admin</h1>
                <p>Manage your store, track orders, and monitor performance with our sleek and powerful command center.</p>
            </div>
            
            <div style="position: absolute; bottom: 40px; left: 80px; display: flex; gap: 20px; opacity: 0.5;">
                <div style="text-align: center;">
                    <div style="font-size: 1.5rem; font-weight: 700;">100%</div>
                    <div style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px;">Secure</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 1.5rem; font-weight: 700;">24/7</div>
                    <div style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px;">Monitoring</div>
                </div>
            </div>
        </div>

        <!-- Form Side -->
        <div class="form-side">
            <div class="login-card">
                <div class="form-header">
                    @if(settings('site_logo'))
                        <div class="mb-6 flex justify-center">
                            <img src="{{ settings('site_logo') }}" alt="{{ settings('site_name') }}" class="h-16 w-auto">
                        </div>
                    @endif
                    <h2>Welcome Back</h2>
                    <p>Enter your credentials to access the dashboard</p>
                </div>

                <x-auth-session-status class="mb-4 text-center" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email Address -->
                    <div class="input-group">
                        <label for="email">Email Address</label>
                        <div class="input-wrapper">
                            <i class="ri-mail-line"></i>
                            <input id="email" type="email" name="email" class="custom-input" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="Enter email">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="error-msg" />
                    </div>

                    <!-- Password -->
                    <div class="input-group">
                        <label for="password">Password</label>
                        <div class="input-wrapper">
                            <i class="ri-lock-2-line"></i>
                            <input id="password" type="password" name="password" class="custom-input" required autocomplete="current-password" placeholder="••••••••">
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="error-msg" />
                    </div>

                    <!-- Options -->
                    <div class="form-footer">
                        <label for="remember_me" class="remember-me">
                            <input id="remember_me" type="checkbox" name="remember" class="rounded border-gray-300">
                            <span>Remember me</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="btn-forgot">Forgot Password?</a>
                        @endif
                    </div>

                    <button type="submit" class="btn-login">
                        Sign In to Dashboard
                    </button>
                </form>
            </div>

            <p style="margin-top: 32px; color: var(--admin-text-muted); font-size: 0.875rem;">
                &copy; {{ date('Y') }} {{ settings('site_name', 'Tech Hub') }}. All rights reserved.
            </p>
        </div>
    </div>
</x-admin-auth-layout>
