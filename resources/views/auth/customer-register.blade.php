<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | Tech Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

    <style>
        /* Reuse Variables from Login Style */
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
        .logo { font-size: 1.5rem; font-weight: 800; color: var(--brand-navy); }
        .back-link { font-size: 0.9rem; color: var(--text-muted); font-weight: 500; }
        .back-link:hover { color: var(--brand-emerald); }

        .auth-wrapper { flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px 20px; }
        .auth-card { background: white; width: 100%; max-width: 1000px; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.1); display: grid; grid-template-columns: 1fr 1.2fr; min-height: 600px; }

        .auth-visual { background: var(--brand-gradient); position: relative; display: flex; flex-direction: column; justify-content: center; padding: 40px; color: white; order: 2; }
        .visual-content h2 { font-size: 2rem; font-weight: 800; margin-bottom: 15px; }
        .visual-content p { font-size: 1rem; opacity: 0.9; line-height: 1.6; }

        .auth-form-side { padding: 50px; display: flex; flex-direction: column; justify-content: center; order: 1; }
        .form-title { font-size: 1.8rem; font-weight: 700; margin-bottom: 5px; }
        .form-sub { color: var(--text-muted); font-size: 0.95rem; margin-bottom: 25px; }
        .form-sub a { color: var(--brand-navy); font-weight: 600; }

        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px; }
        .form-group { margin-bottom: 15px; }
        .label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; }
        .input-field { width: 100%; padding: 12px 15px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none; }
        .input-field:focus { border-color: var(--brand-emerald); box-shadow: 0 0 0 3px rgba(45, 174, 154, 0.1); }

        .btn-submit { width: 100%; padding: 14px; background: var(--brand-navy); color: white; border: none; border-radius: var(--radius-sm); font-weight: 600; font-size: 1rem; cursor: pointer; margin-top: 10px; transition: 0.2s; }
        .btn-submit:hover { background: var(--brand-emerald); }
        .text-red-500 { color: #ef4444; font-size: 0.8rem; margin-top: 5px; display: block; }

        .terms-text { font-size: 0.8rem; color: var(--text-muted); margin-top: 15px; text-align: center; }
        .terms-text a { text-decoration: underline; }


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
        <a href="{{ route('customer.login') }}" class="back-link"><i class="ri-arrow-left-line"></i> Sign In Instead</a>
    </div>
</header>

<div class="auth-wrapper">
    <div class="auth-card">

        <div class="auth-visual">
            <div class="visual-content">
                <h2>Join the Club</h2>
                <p>Create an account to get free delivery on your first order, track shipments easily, and save your favorite items.</p>
            </div>
        </div>

        <div class="auth-form-side">
            <h1 class="form-title">Create Account</h1>
            <p class="form-sub">Already have an account? <a href="{{ route('customer.login') }}">Log in</a></p>

            <form method="POST" action="{{ route('customer.register.store') }}">
                @csrf
                <div class="form-grid">
                    <div class="form-group">
                        <label class="label">First Name</label>
                        <input type="text" name="first_name" class="input-field" value="{{ old('first_name') }}" required>
                        @error('first_name') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="label">Last Name</label>
                        <input type="text" name="last_name" class="input-field" value="{{ old('last_name') }}" required>
                        @error('last_name') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="label">Email Address</label>
                    <input type="email" name="email" class="input-field" value="{{ old('email') }}" required>
                    @error('email') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="label">Mobile Number (UAE)</label>
                    <input type="tel" name="phone" class="input-field" placeholder="+971 50 123 4567" value="{{ old('phone') }}" required>
                    @error('phone') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="label">Password</label>
                        <input type="password" name="password" class="input-field" required>
                        @error('password') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="input-field" required>
                    </div>
                </div>

                <label style="display:flex; align-items:start; gap:10px; cursor:pointer; font-size:0.85rem; color:var(--text-muted);">
                    <input type="checkbox" style="accent-color:var(--brand-primary); margin-top:3px;">
                    I agree to the Terms of Service and Privacy Policy.
                </label>

                <button type="submit" class="btn-submit">Create Account</button>

                <p class="terms-text">By creating an account, you agree to receive updates about our products. You can unsubscribe at any time.</p>
            </form>
        </div>

    </div>
</div>

</body>
</html>
