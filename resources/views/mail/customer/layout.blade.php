<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: 'Outfit', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #334155; margin: 0; padding: 0; line-height: 1.6; }
        .wrapper { width: 100%; padding: 40px 0; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .header { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 40px; text-align: center; }
        .logo { font-size: 28px; font-weight: 800; color: #ffffff; text-decoration: none; letter-spacing: -0.5px; }
        .logo span { color: #fbbf24; }
        .content { padding: 40px; }
        .footer { padding: 30px; text-align: center; font-size: 12px; color: #94a3b8; background-color: #f1f5f9; }
        .button { display: inline-block; padding: 14px 28px; background-color: #2563eb; color: #ffffff !important; text-decoration: none; border-radius: 12px; font-weight: 700; margin-top: 20px; transition: background-color 0.2s; }
        .button:hover { background-color: #1d4ed8; }
        h1 { font-size: 24px; font-weight: 800; color: #0f172a; margin-top: 0; letter-spacing: -0.5px; }
        p { margin-bottom: 20px; }
        .divider { border-top: 1px solid #e2e8f0; margin: 30px 0; }
        .order-meta { background-color: #f8fafc; border-radius: 12px; padding: 20px; margin: 20px 0; }
        .order-meta table { width: 100%; border-collapse: collapse; }
        .order-meta td { padding: 5px 0; font-size: 14px; }
        .label { font-weight: 700; color: #64748b; width: 120px; }
        .text-right { text-align: right; }
        .font-bold { font-weight: 700; }
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 9999px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; background: #dcfce7; color: #166534; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <a href="{{ config('app.url') }}" class="logo">Electro<span>Mart</span></a>
            </div>
            <div class="content">
                @yield('content')
            </div>
            <div class="footer">
                &copy; {{ date('Y') }} ElectroMart Enterprise. All rights reserved.<br>
                Dubai Silicon Oasis, Dubai, UAE.<br>
                <a href="{{ route('home') }}" style="color: #64748b; text-decoration: underline;">Visit our Shop</a> | <a href="#" style="color: #64748b; text-decoration: underline;">Support</a>
            </div>
        </div>
    </div>
</body>
</html>
