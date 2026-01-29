<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: 'Outfit', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #334155; margin: 0; padding: 0; line-height: 1.6; }
        .wrapper { width: 100%; padding: 40px 0; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .header { background: #ffffff; padding: 30px; text-align: center; border-bottom: 1px solid #f1f5f9; }
        .logo-img { height: 40px; width: auto; }
        .logo-text { font-size: 24px; font-weight: 800; color: #2dae9a; text-decoration: none; letter-spacing: -0.5px; }
        .content { padding: 40px; }
        .footer { padding: 30px; text-align: center; font-size: 12px; color: #94a3b8; background-color: #f8fafc; border-top: 1px solid #f1f5f9; }
        .button { display: inline-block; padding: 14px 28px; background-color: #2dae9a; color: #ffffff !important; text-decoration: none; border-radius: 12px; font-weight: 700; margin-top: 20px; transition: opacity 0.2s; }
        .button:hover { opacity: 0.9; }
        h1 { font-size: 24px; font-weight: 1000; color: #0f172a; margin-top: 0; letter-spacing: -0.5px; }
        p { margin-bottom: 20px; }
        .divider { border-top: 1px solid #e2e8f0; margin: 30px 0; }
        .order-meta { background-color: #f8fafc; border-radius: 12px; padding: 20px; margin: 20px 0; }
        .order-meta table { width: 100%; border-collapse: collapse; }
        .order-meta td { padding: 5px 0; font-size: 14px; }
        .label { font-weight: 700; color: #64748b; width: 120px; }
        .text-right { text-align: right; }
        .font-bold { font-weight: 700; }
        .status-badge { display: inline-block; padding: 6px 16px; border-radius: 9999px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; background: #2dae9a15; color: #2dae9a; border: 1px solid #2dae9a20; }
        .social-link { color: #2dae9a; text-decoration: none; margin: 0 5px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <a href="{{ url('/') }}" class="logo">
                    @if(settings('site_logo'))
                        <img src="{{ settings('site_logo') }}" alt="{{ settings('site_name') }}" class="logo-img">
                    @else
                        <span class="logo-text">{{ settings('site_name', 'Tech Hub') }}</span>
                    @endif
                </a>
            </div>
            <div class="content">
                @yield('content')
            </div>
            <div class="footer">
                <div style="margin-bottom: 15px;">
                    @if(settings('social_facebook')) <a href="{{ settings('social_facebook') }}" class="social-link">Facebook</a> @endif
                    @if(settings('social_instagram')) <a href="{{ settings('social_instagram') }}" class="social-link">Instagram</a> @endif
                    @if(settings('social_twitter')) <a href="{{ settings('social_twitter') }}" class="social-link">Twitter</a> @endif
                </div>
                &copy; {{ date('Y') }} {{ settings('site_name') }}. All rights reserved.<br>
                {{ settings('contact_address') }}<br>
                @if(settings('contact_phone')) <span>Phone: {{ settings('contact_phone') }}</span> @endif
                @if(settings('contact_email')) <span style="margin-left: 10px;">Email: {{ settings('contact_email') }}</span> @endif
                <div style="margin-top: 15px;">
                    <a href="{{ url('/') }}" style="color: #64748b; text-decoration: underline;">Visit Website</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
