<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | Tech Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

    <style>
        /* Reuse Variables */
        :root {
            --brand-navy: #024959; --brand-emerald: #2dae9a;
            --brand-gradient: linear-gradient(135deg, #024959 0%, #037F8C 100%);
            --text-main: #0f172a; --text-muted: #64748b; --border-color: #e2e8f0;
            --radius-md: 12px; --radius-sm: 6px;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: #f8fafc; color: var(--text-main); min-height: 100vh; display: flex; flex-direction: column; }
        a { text-decoration: none; color: inherit; transition: 0.2s; }

        header { background: white; border-bottom: 1px solid var(--border-color); padding: 20px 0; }
        .container { max-width: 1300px; margin: 0 auto; padding: 0 20px; display: flex; align-items: center; justify-content: space-between; }
        .logo { font-size: 1.5rem; font-weight: 800; color: var(--brand-navy); display:flex; flex-direction:column; line-height:1; }
        .back-link { font-size: 0.9rem; color: var(--text-muted); font-weight: 500; }

        .auth-wrapper { flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px 20px; }
        .auth-card { background: white; width: 100%; max-width: 900px; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.1); display: grid; grid-template-columns: 1fr 1fr; min-height: 500px; }

        .auth-visual { background: var(--brand-gradient); position: relative; display: flex; flex-direction: column; justify-content: center; padding: 40px; color: white; order: 2; }
        .visual-content h2 { font-size: 2rem; font-weight: 800; margin-bottom: 15px; }
        .visual-content p { font-size: 1rem; opacity: 0.9; line-height: 1.6; }

        .auth-form-side { padding: 50px; display: flex; flex-direction: column; justify-content: center; order: 1; }
        .form-title { font-size: 1.8rem; font-weight: 700; margin-bottom: 5px; }
        .form-desc { color: var(--text-muted); font-size: 0.95rem; margin-bottom: 30px; }

        .form-group { margin-bottom: 20px; }
        .label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; }
        .input-field { width: 100%; padding: 12px 15px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none; }
        .input-field:focus { border-color: var(--brand-emerald); box-shadow: 0 0 0 3px rgba(45, 174, 154, 0.1); }

        .btn-submit { width: 100%; padding: 14px; background: var(--brand-navy); color: white; border: none; border-radius: var(--radius-sm); font-weight: 600; font-size: 1rem; cursor: pointer; transition: 0.2s; margin-top: 10px; }
        .btn-submit:hover { background: var(--brand-emerald); }
        .text-red-500 { color: #ef4444; font-size: 0.8rem; margin-top: 5px; display: block; }

        @media (max-width: 768px) { .auth-card { grid-template-columns: 1fr; } .auth-visual { display: none; } .auth-form-side { padding: 30px; order: 1; } }
    </style>
</head>
<body>

<header>
    <div class="container">
        <a href="{{ route('home') }}" class="logo">
            @if(settings('site_logo'))
                <img src="{{ settings('site_logo') }}" alt="{{ settings('site_name') }}" style="max-height: 60px;">
            @else
                {{ settings('site_name', 'Tech Hub') }}
            @endif
        </a>
    </div>
</header>

<div class="auth-wrapper">
    <div class="auth-card">

        <div class="auth-visual">
            <div class="visual-content">
                <h2>Reset Password</h2>
                <p>Create a strong password to keep your account secure. We recommend using a mix of letters, numbers, and symbols.</p>
            </div>
        </div>

        <div class="auth-form-side">
            <h1 class="form-title">Set New Password</h1>
            <p class="form-desc">Please enter your new password below.</p>

            <form method="POST" action="{{ route('customer.password.update') }}">
                @csrf
                <!-- Token from URL -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="form-group">
                    <label class="label">Email Address</label>
                    <input type="email" name="email" class="input-field" value="{{ old('email', $request->email) }}" required autofocus>
                    @error('email') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="label">New Password</label>
                    <input type="password" name="password" class="input-field" required>
                    @error('password') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="input-field" required>
                </div>

                <button type="submit" class="btn-submit">Reset Password</button>
            </form>
        </div>

    </div>
</div>

</body>
</html>
