<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Tech Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

    <style>
        :root {
            --brand-navy: #024959;
            --brand-emerald: #03A696;
            --brand-gradient: linear-gradient(135deg, #024959 0%, #037F8C 100%);
            --text-main: #0f172a; --text-muted: #64748b; --border-color: #e2e8f0;
            --radius-md: 12px; --radius-sm: 6px;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: #f8fafc; color: var(--text-main); height: 100vh; display: flex; flex-direction: column; }
        a { text-decoration: none; color: inherit; transition: 0.2s; }

        header { background: white; border-bottom: 1px solid var(--border-color); padding: 20px 0; }
        .container { max-width: 1300px; margin: 0 auto; padding: 0 20px; display: flex; align-items: center; justify-content: space-between; }
        .logo { font-size: 1.5rem; font-weight: 800; display: flex; align-items: center; gap: 8px; color: var(--brand-navy); }
        .back-link { font-size: 0.9rem; color: var(--text-muted); font-weight: 500; display: flex; align-items: center; gap: 5px; }
        .back-link:hover { color: var(--brand-emerald); }

        .auth-wrapper { flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px 20px; }
        .auth-card { background: white; width: 100%; max-width: 900px; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.1); display: grid; grid-template-columns: 1fr 1fr; min-height: 550px; }

        .auth-visual { background: var(--brand-gradient); position: relative; display: flex; flex-direction: column; justify-content: center; padding: 40px; color: white; overflow: hidden; }
        .visual-content h2 { font-size: 2rem; font-weight: 800; margin-bottom: 15px; line-height: 1.2; }
        .visual-content p { font-size: 1rem; opacity: 0.9; line-height: 1.6; }

        .auth-form-side { padding: 50px; display: flex; flex-direction: column; justify-content: center; }
        .form-header { margin-bottom: 25px; }
        .form-title { font-size: 1.8rem; font-weight: 700; margin-bottom: 5px; }
        .form-sub { color: var(--text-muted); font-size: 0.95rem; }
        .form-sub a { color: var(--brand-navy); font-weight: 600; }

        .form-group { margin-bottom: 20px; }
        .label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: var(--text-main); }
        .input-field { width: 100%; padding: 12px 15px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); font-size: 0.95rem; outline: none; transition: 0.2s; }
        .input-field:focus { border-color: var(--brand-emerald); box-shadow: 0 0 0 3px rgba(3, 166, 150, 0.1); }

        .btn-submit { width: 100%; padding: 14px; background: var(--brand-navy); color: white; border: none; border-radius: var(--radius-sm); font-weight: 600; font-size: 1rem; cursor: pointer; transition: 0.2s; }
        .btn-submit:hover { background: var(--brand-emerald); }

        .text-red-500 { color: #ef4444; font-size: 0.8rem; margin-top: 5px; display: block; }

        @media (max-width: 768px) { .auth-card { grid-template-columns: 1fr; } .auth-visual { display: none; } .auth-form-side { padding: 30px; } }
    </style>
</head>
<body>

<header>
    <div class="container">
        <a href="{{ route('home') }}" class="logo">TECH HUB</a>
        <a href="{{ route('home') }}" class="back-link"><i class="ri-arrow-left-line"></i> Back to Shop</a>
    </div>
</header>

<div class="auth-wrapper">
    <div class="auth-card">

        <div class="auth-visual">
            <div class="visual-content">
                <h2>Welcome Back!</h2>
                <p>Access your orders, wishlist, and exclusive deals. The best electronics await.</p>
            </div>
        </div>

        <div class="auth-form-side">
            <div class="form-header">
                <h1 class="form-title">Sign In</h1>
                <p class="form-sub">New here? <a href="{{ route('customer.register') }}">Create an account</a></p>
            </div>

            <form method="POST" action="{{ route('customer.login.store') }}">
                @csrf
                <div class="form-group">
                    <label class="label">Email Address</label>
                    <input type="email" name="email" class="input-field" value="{{ old('email') }}" required autofocus>
                    @error('email') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="label">Password</label>
                    <input type="password" name="password" class="input-field" required>
                    @error('password') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="form-options" style="display:flex; justify-content:space-between; margin-bottom:20px; font-size:0.85rem;">
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                     <a href="{{ route('customer.password.request') }}" style="color:var(--brand-navy); font-weight:500;">Forgot Password?</a>
                </div>

                <button type="submit" class="btn-submit">Sign In</button>
            </form>
        </div>

    </div>
</div>

</body>
</html>
