<x-admin-auth-layout>
    <style>
        :root {
            --admin-navy: #0f172a;
            --admin-primary: #3b82f6;
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
            font-size: 0.9rem;
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

        .btn-submit {
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
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            margin-top: 10px;
        }

        .btn-submit:hover {
            background: var(--admin-primary);
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.2);
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
                <h1>ELECTROMART Admin</h1>
                <p>Securely reset your password and regain access to your administrative command center.</p>
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
                    <h2>New Password</h2>
                    <p>Enter your new credentials below</p>
                </div>

                <form method="POST" action="{{ route('password.store') }}">
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <!-- Email Address -->
                    <div class="input-group">
                        <label for="email">Email Address</label>
                        <div class="input-wrapper">
                            <i class="ri-mail-line"></i>
                            <input id="email" type="email" name="email" class="custom-input" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="error-msg" />
                    </div>

                    <!-- Password -->
                    <div class="input-group">
                        <label for="password">New Password</label>
                        <div class="input-wrapper">
                            <i class="ri-lock-2-line"></i>
                            <input id="password" type="password" name="password" class="custom-input" required autocomplete="new-password" placeholder="••••••••">
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="error-msg" />
                    </div>

                    <!-- Confirm Password -->
                    <div class="input-group">
                        <label for="password_confirmation">Confirm Password</label>
                        <div class="input-wrapper">
                            <i class="ri-lock-check-line"></i>
                            <input id="password_confirmation" type="password" name="password_confirmation" class="custom-input" required autocomplete="new-password" placeholder="••••••••">
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="error-msg" />
                    </div>

                    <button type="submit" class="btn-submit">
                        Update Password & Sign In
                    </button>
                </form>
            </div>

            <p style="margin-top: 32px; color: var(--admin-text-muted); font-size: 0.875rem;">
                &copy; {{ date('Y') }} Tech Hub. All rights reserved.
            </p>
        </div>
    </div>
</x-admin-auth-layout>
