<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scheduled Maintenance - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-6">
    <div class="max-w-xl w-full text-center">
        <!-- Logo -->
        <div class="mb-12">
            <span class="text-3xl font-extrabold tracking-tight text-[#024959] uppercase">TECH<span class="text-[#2dae9a]">HUB</span></span>
            <div class="text-[0.6rem] uppercase tracking-[0.3em] text-gray-400 font-bold mt-1">Information Technology</div>
        </div>

        <!-- Illustration -->
        <div class="relative w-48 h-48 mx-auto mb-8">
            <div class="absolute inset-0 bg-[#2dae9a]/10 rounded-full animate-pulse"></div>
            <div class="absolute inset-4 bg-[#2dae9a]/20 rounded-full animate-ping" style="animation-duration: 3s;"></div>
            <div class="absolute inset-0 flex items-center justify-center">
                <i class="fas fa-tools text-6xl text-[#2dae9a]"></i>
            </div>
        </div>

        <h1 class="text-4xl font-extrabold text-gray-900 mb-4 tracking-tight">Under Maintenance</h1>
        <p class="text-lg text-gray-500 mb-10 leading-relaxed font-medium">
            {{ settings('maintenance_message') ?: 'We are currently performing some scheduled maintenance to improve our services. We\'ll be back online shortly!' }}
        </p>

        <div class="inline-flex items-center gap-3 px-6 py-3 bg-white border border-gray-100 rounded-2xl shadow-sm text-sm font-bold text-gray-400">
            <i class="fas fa-envelope text-[#2dae9a]"></i>
            Questions? Contact us at <span class="text-[#024959]">{{ settings('website_email', 'support@techhubrak.ae') }}</span>
        </div>

        <div class="mt-12 pt-8 border-t border-gray-100">
            <p class="text-[0.65rem] font-black text-gray-300 uppercase tracking-widest">© {{ date('Y') }} {{ settings('website_name', 'Tech Hub') }} - All Rights Reserved</p>
        </div>
    </div>
</body>
</html>
