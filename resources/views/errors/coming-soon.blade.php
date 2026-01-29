<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coming Soon - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-[#0f172a] text-white flex items-center justify-center min-h-screen p-6 overflow-hidden relative">
    <!-- Animated background elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-[#2dae9a]/10 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-[#024959]/30 rounded-full blur-3xl" style="animation: pulse 4s infinite;"></div>
    </div>

    <div class="max-w-xl w-full text-center relative z-10">
        <!-- Logo -->
        <div class="mb-12">
            <span class="text-3xl font-extrabold tracking-tight text-white uppercase">TECH<span class="text-[#2dae9a]">HUB</span></span>
            <div class="text-[0.6rem] uppercase tracking-[0.3em] text-gray-400 font-bold mt-1">Information Technology</div>
        </div>

        <!-- Icon -->
        <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-[#2dae9a] to-[#015e55] rounded-3xl shadow-2xl shadow-emerald-500/20 mb-10 transform -rotate-12 hover:rotate-0 transition-transform duration-500">
            <i class="fas fa-rocket text-4xl text-white"></i>
        </div>

        <h1 class="text-5xl font-extrabold mb-6 tracking-tight leading-tight">Something Big is Coming!</h1>
        <p class="text-lg text-gray-400 mb-12 leading-relaxed font-medium">
            {{ settings('coming_soon_message') ?: 'We\'re working hard to bring you the best tech shopping experience. Something exciting is just around the corner!' }}
        </p>

        <!-- Notify Form (Placeholder) -->
        <div class="max-w-md mx-auto mb-16">
            <div class="flex p-1.5 bg-white/5 border border-white/10 rounded-2xl backdrop-blur-md">
                <input type="email" placeholder="Enter your email" class="flex-1 bg-transparent border-none focus:ring-0 px-4 text-sm font-medium">
                <button class="bg-[#2dae9a] hover:bg-[#248e7e] px-6 py-3 rounded-xl text-sm font-black uppercase tracking-widest transition-all shadow-lg shadow-emerald-500/10">Notify Me</button>
            </div>
            <p class="text-[0.65rem] text-gray-500 mt-4 font-bold uppercase tracking-widest">No spam, just important updates.</p>
        </div>

        <div class="flex justify-center gap-6 text-xl">
            <a href="#" class="text-gray-500 hover:text-white transition-colors"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="text-gray-500 hover:text-white transition-colors"><i class="fab fa-instagram"></i></a>
            <a href="#" class="text-gray-500 hover:text-white transition-colors"><i class="fab fa-twitter"></i></a>
            <a href="#" class="text-gray-500 hover:text-white transition-colors"><i class="fab fa-linkedin-in"></i></a>
        </div>

        <div class="mt-16 pt-8 border-t border-white/5">
            <p class="text-[0.65rem] font-black text-gray-600 uppercase tracking-widest">© {{ date('Y') }} {{ settings('website_name', 'Tech Hub') }} - Stay Tuned</p>
        </div>
    </div>

    <style>
        @keyframes pulse {
            0%, 100% { opacity: 0.3; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.1); }
        }
    </style>
</body>
</html>
