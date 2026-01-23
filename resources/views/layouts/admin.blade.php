<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ElectroMart') }} Admin</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Remix Icons (for IT Solutions icons) -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Custom Styles -->
    @stack('styles')

    <style>
        body { font-family: 'Outfit', sans-serif; }

        /* Sidebar Custom Styles */
        .sidebar-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            margin: 0.25rem 0.5rem;
            border-radius: 0.625rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            color: #94a3b8;
        }

        .sidebar-item i {
            width: 1.5rem;
            font-size: 1.1rem;
            margin-right: 0.75rem;
            transition: transform 0.2s;
        }

        .sidebar-item:hover {
            background-color: rgba(255, 255, 255, 0.05);
            color: #f8fafc;
        }

        .sidebar-item:hover i {
            transform: translateX(2px);
        }

        .sidebar-item.active {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .sidebar-item.active i {
            color: white;
        }

        .sidebar-header {
            padding: 1.5rem 1.5rem 0.5rem;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #475569;
        }

        /* Custom Scrollbar for Sidebar */
        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 10px;
        }

        .sidebar-transition {
            transition: transform 0.3s ease-in-out;
        }
    </style>
</head>
<!-- FIX: Use h-screen and overflow-hidden on body to prevent double scrollbars -->

<body class="bg-gray-100 font-sans antialiased h-screen flex flex-col overflow-hidden">

    <!-- === MOBILE HEADER (Visible only on small screens) === -->
    <div class="md:hidden bg-slate-900 text-white p-4 flex justify-between items-center z-50 flex-shrink-0 shadow-md">
        <span class="font-bold text-xl"><i class="fas fa-bolt text-yellow-400"></i> ElectroMart</span>
        <button id="mobile-menu-btn" class="focus:outline-none text-white">
            <i class="fas fa-bars text-2xl"></i>
        </button>
    </div>

    <!-- === MAIN LAYOUT CONTAINER === -->
    <div class="flex flex-1 h-full overflow-hidden relative">

        <!-- === SIDEBAR === -->
        <!-- FIX:
             1. 'fixed inset-y-0' ensures full height on mobile.
             2. 'md:static' puts it in normal flow on desktop.
             3. 'md:h-full' ensures it stretches on desktop.
        -->
        <aside id="sidebar"
               class="bg-[#0f172a] text-white w-72 absolute inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 sidebar-transition z-40 h-full flex flex-col border-r border-slate-800 shadow-2xl">

            <!-- App Logo Area -->
            <div class="p-6 mb-2">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-900 group-hover:scale-110 transition-transform">
                        <i class="fas fa-bolt text-white text-xl"></i>
                    </div>
                    <div>
                        <span class="text-xl font-extrabold tracking-tight text-white block leading-none">ElectroMart</span>
                        <span class="text-[0.65rem] font-bold uppercase tracking-widest text-blue-500 mt-1 block">Enterprise Admin</span>
                    </div>
                </a>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 sidebar-scroll overflow-y-auto pb-6">

                <a href="{{ route('dashboard') }}"
                   class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> <span>Dashboard</span>
                </a>

                <!-- PRIMARY ACTION: POS -->
                @role('Cashier|Manager|Admin')
                <div class="px-3 mt-4 mb-6">
                    <a href="{{ route('pos.index') }}"
                       class="flex items-center justify-center gap-3 py-3.5 px-4 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-bold shadow-lg shadow-emerald-900/20 transform hover:-translate-y-0.5 transition-all text-sm">
                        <i class="fas fa-cash-register text-base"></i> Launch POS Terminal
                    </a>
                </div>
                @endrole

                <a href="{{ route('home') }}" target="_blank"
                   class="sidebar-item hover:text-white group">
                    <i class="fas fa-external-link-alt group-hover:text-blue-400 transition-colors"></i> <span>Visit Shop Frontend</span>
                </a>

                <!-- SALES & CUSTOMERS -->
                <div class="sidebar-header">Core Operations</div>
                @role('Admin')
                <a href="{{ route('orders.index') }}"
                   class="sidebar-item {{ request()->routeIs('orders*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-bag"></i> <span>Order History</span>
                </a>
                @endrole

                <a href="{{ route('returns.index') }}"
                   class="sidebar-item {{ request()->routeIs('returns*') ? 'active' : '' }}">
                    <i class="fas fa-sync-alt"></i> <span>Returns & Refunds</span>
                </a>

                <a href="{{ route('customers.index') }}"
                   class="sidebar-item {{ request()->routeIs('customers*') ? 'active' : '' }}">
                    <i class="fas fa-user-friends"></i> <span>Customer CRM</span>
                </a>

                <a href="{{ route('quotations.index') }}"
                   class="sidebar-item {{ request()->routeIs('quotations*') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice"></i> <span>Quotations</span>
                </a>

                <!-- CATALOG & INVENTORY -->
                <div class="sidebar-header">Catalog Management</div>
                @role('Manager|Admin')
                <a href="{{ route('products.index') }}"
                   class="sidebar-item {{ request()->routeIs('products*') ? 'active' : '' }}">
                    <i class="fas fa-layer-group"></i> <span>Product Catalog</span>
                </a>

                <a href="{{ route('categories.index') }}"
                   class="sidebar-item {{ request()->routeIs('categories*') ? 'active' : '' }}">
                    <i class="fas fa-th-large"></i> <span>Categories</span>
                </a>

                <a href="{{ route('brands.index') }}"
                   class="sidebar-item {{ request()->routeIs('brands*') ? 'active' : '' }}">
                    <i class="fas fa-award"></i> <span>Brand Registry</span>
                </a>
                @endrole

                <!-- FINANCIALS -->
                <div class="sidebar-header">Financial Resources</div>
                @role('Manager|Admin')
                <a href="{{ route('purchases.index') }}"
                   class="sidebar-item {{ request()->routeIs('purchases*') ? 'active' : '' }}">
                    <i class="fas fa-truck-loading"></i> <span>Purchase Orders</span>
                </a>

                <a href="{{ route('expenses.index') }}"
                   class="sidebar-item {{ request()->routeIs('expenses*') ? 'active' : '' }}">
                    <i class="fas fa-receipt"></i> <span>Expense Tracking</span>
                </a>
                @endrole

                <!-- MARKETING -->
                <div class="sidebar-header">Growth & Content</div>
                @role('Admin')
                <a href="{{ route('coupons.admin.index') }}"
                   class="sidebar-item {{ request()->routeIs('coupons.admin.*') ? 'active' : '' }}">
                    <i class="fas fa-tags"></i> <span>Promo Coupons</span>
                </a>

                <a href="{{ route('banners.index') }}"
                   class="sidebar-item {{ request()->routeIs('banners.*') ? 'active' : '' }}">
                    <i class="fas fa-paint-brush"></i> <span>Home Experience</span>
                </a>

                <a href="{{ route('popups.admin.index') }}"
                   class="sidebar-item {{ request()->routeIs('popups.admin.*') ? 'active' : '' }}">
                    <i class="fas fa-bullhorn"></i> <span>Promo Popups</span>
                </a>

                <a href="{{ route('solutions.admin.index') }}"
                   class="sidebar-item {{ request()->routeIs('solutions.admin.*') ? 'active' : '' }}">
                    <i class="fas fa-laptop-code"></i> <span>Service Solutions</span>
                </a>

                <a href="{{ route('pages.admin.index') }}"
                   class="sidebar-item {{ request()->routeIs('pages.admin.*') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice"></i> <span>Custom Pages</span>
                </a>
                @endrole

                <!-- ANALYTICS -->
                <div class="sidebar-header">Analytics</div>
                @role('Admin')
                <a href="{{ route('reports.sales') }}"
                   class="sidebar-item {{ request()->routeIs('reports.sales') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie"></i> <span>Business Analytics</span>
                </a>

                <a href="{{ route('reports.vat') }}"
                   class="sidebar-item {{ request()->routeIs('reports.vat') ? 'active' : '' }}">
                    <i class="fas fa-calculator"></i> <span>Tax Compliance</span>
                </a>
                @endrole

                <!-- ADMINISTRATION -->
                <div class="sidebar-header">Administration</div>
                @role('Admin')
                <a href="{{ route('users.index') }}"
                   class="sidebar-item {{ request()->routeIs('users*') ? 'active' : '' }}">
                    <i class="fas fa-user-lock"></i> <span>Access Control</span>
                </a>

                <a href="{{ route('settings.edit') }}"
                   class="sidebar-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <i class="fas fa-sliders-h"></i> <span>General Settings</span>
                </a>

                <a href="{{ route('notifications.index') }}"
                   class="sidebar-item {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                    <i class="fas fa-bell"></i> <span>System Notifications</span>
                </a>
                @endrole

            </nav>

            <!-- User Footer Section -->
            <div class="mt-auto p-4 bg-[#0a0f1d] border-t border-slate-800">
                <div class="flex items-center gap-3 p-2 rounded-xl bg-slate-900/50">
                    <div class="relative">
                        <div class="w-10 h-10 rounded-full bg-blue-600 border-2 border-slate-700 flex items-center justify-center font-bold text-white shadow-inner">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-[#0a0f1d] rounded-full"></div>
                    </div>
                    <div class="flex-1 overflow-hidden">
                        <p class="text-[0.8rem] font-bold truncate text-gray-100 leading-tight">{{ Auth::user()->name }}</p>
                        <p class="text-[0.65rem] text-blue-400 font-bold uppercase tracking-wider">{{ Auth::user()->getRoleNames()->first() ?? 'Staff' }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:text-white hover:bg-slate-800 transition-all" title="Secure Logout">
                            <i class="fas fa-power-off text-sm"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- === OVERLAY (Mobile) === -->
        <div id="mobile-overlay" class="fixed inset-0 bg-black opacity-50 z-30 hidden md:hidden"></div>

        <!-- === MAIN CONTENT (Scrolls Independently) === -->
        <main class="flex-1 flex flex-col h-full overflow-hidden bg-gray-100">
            <!-- Top Header (Desktop) -->
            <header class="bg-white shadow-sm h-16 hidden md:flex items-center justify-between px-6 flex-shrink-0">
                <h2 class="text-xl font-bold text-gray-800">@yield('header')</h2>
                
                <div class="flex items-center gap-6">
                    <!-- Notifications Dropdown -->
                    <div class="relative" id="notification-dropdown-wrapper">
                        <button id="notification-bell" class="relative p-2 text-gray-400 hover:text-blue-600 transition-colors focus:outline-none">
                            <i class="fas fa-bell text-xl"></i>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <span class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white text-[0.6rem] font-bold flex items-center justify-center rounded-full border-2 border-white">
                                    {{ auth()->user()->unreadNotifications->count() }}
                                </span>
                            @endif
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="notification-menu" class="absolute right-0 mt-3 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden z-50 hidden opacity-0 transform scale-95 transition-all duration-200 origin-top-right">
                            <div class="px-5 py-4 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                                <h3 class="text-xs font-black text-gray-900 uppercase tracking-widest">Notifications</h3>
                                <a href="{{ route('notifications.markAllAsRead') }}" class="text-[0.65rem] font-bold text-blue-600 hover:underline">Mark all read</a>
                            </div>

                            <div class="max-h-[350px] overflow-y-auto">
                                @forelse(auth()->user()->unreadNotifications->take(5) as $notification)
                                    <div class="p-4 border-b border-gray-50 hover:bg-gray-50/50 transition-colors flex gap-4 items-start relative group">
                                        <div class="w-10 h-10 rounded-xl bg-{{ $notification->data['color'] ?? 'blue' }}-50 flex items-center justify-center text-{{ $notification->data['color'] ?? 'blue' }}-600 shrink-0">
                                            <i class="{{ $notification->data['icon'] ?? 'fas fa-info-circle' }} text-sm"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-bold text-gray-900 truncate">{{ $notification->data['title'] }}</p>
                                            <p class="text-[0.7rem] text-gray-500 line-clamp-2 mt-0.5 leading-relaxed">{{ $notification->data['message'] }}</p>
                                            <p class="text-[0.6rem] text-gray-400 mt-1 font-medium">{{ $notification->created_at->diffForHumans() }}</p>
                                        </div>
                                        <a href="{{ $notification->data['action_url'] ?? '#' }}" 
                                           onclick="markNotificationAsRead('{{ $notification->id }}')"
                                           class="absolute inset-0 z-0"></a>
                                    </div>
                                @empty
                                    <div class="p-8 text-center">
                                        <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                            <i class="fas fa-bell-slash text-gray-200 text-lg"></i>
                                        </div>
                                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">No new alerts</p>
                                    </div>
                                @endforelse
                            </div>

                            <a href="{{ route('notifications.index') }}" class="block py-3 text-center text-[0.7rem] font-black text-gray-400 uppercase tracking-widest hover:bg-gray-50 border-t border-gray-50 transition-colors">
                                View all notification history
                            </a>
                        </div>
                    </div>

                    <div class="text-sm text-gray-500 font-medium">
                        {{ now()->format('l, d F Y') }}
                    </div>
                </div>
            </header>

            <!-- Mobile Page Title -->
            <div class="md:hidden p-4 bg-white shadow-sm flex-shrink-0">
                <h2 class="text-lg font-bold text-gray-800">@yield('header')</h2>
            </div>

            <!-- Scrollable Content Body -->
            <div class="flex-1 overflow-y-auto p-6 scroll-smooth">
                <!-- Flash Messages -->
                @if (session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm relative"
                        role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                        <button onclick="this.parentElement.remove()" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm relative"
                        role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                        <button onclick="this.parentElement.remove()" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Mobile Sidebar Logic -->
    <script>
        const btn = document.getElementById('mobile-menu-btn');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('mobile-overlay');

        function toggleSidebar() {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        btn.addEventListener('click', toggleSidebar);
        overlay.addEventListener('click', toggleSidebar);
    </script>

    <!-- Notifications JS -->
    <script>
        const bell = document.getElementById('notification-bell');
        const menu = document.getElementById('notification-menu');
        const wrapper = document.getElementById('notification-dropdown-wrapper');

        bell?.addEventListener('click', (e) => {
            e.stopPropagation();
            menu.classList.toggle('hidden');
            setTimeout(() => {
                menu.classList.toggle('opacity-0');
                menu.classList.toggle('scale-95');
            }, 10);
        });

        document.addEventListener('click', (e) => {
            if (!wrapper?.contains(e.target)) {
                menu?.classList.add('opacity-0', 'scale-95');
                setTimeout(() => menu?.classList.add('hidden'), 200);
            }
        });

        function markNotificationAsRead(id) {
            fetch(`/backend/notifications/${id}/mark-as-read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            }).catch(err => console.error('Error marking notification as read:', err));
        }
    </script>

    @stack('scripts')
    @yield('scripts')
</body>

</html>
