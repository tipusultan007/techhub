<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@hasSection('title') @yield('title') @else @yield('header', 'Admin Dashboard') @endif | {{ settings('site_name', 'Tech Hub') }} Admin</title>

    <!-- Tailwind CSS & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

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

    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <link rel="icon" href="{{ settings('site_favicon') ? asset(settings('site_favicon')) : asset('favicon.ico') }}">
    
    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- Custom Styles -->
    @stack('styles')

    <style>
        :root {
            --brand-navy: #024959;
            --brand-emerald: #2dae9a;
        }
        body { font-family: 'Outfit', sans-serif; }

        /* Global Utility Overrides to match Brand Color */
        .bg-blue-600, .bg-indigo-600, .bg-blue-500 { background-color: var(--brand-emerald) !important; }
        .text-blue-600, .text-indigo-600, .text-blue-500 { color: var(--brand-emerald) !important; }
        .border-blue-600, .border-indigo-600, .border-blue-500 { border-color: var(--brand-emerald) !important; }
        .hover\:bg-blue-700:hover, .hover\:bg-indigo-700:hover, .hover\:bg-blue-600:hover { background-color: #248e7e !important; }
        .hover\:text-blue-700:hover, .hover\:text-indigo-700:hover, .hover\:text-blue-600:hover { color: #248e7e !important; }
        
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
            background: linear-gradient(135deg, #2dae9a 0%, #024959 100%);
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

        @media print {
            .no-print { display: none !important; }
            body { background: white !important; overflow: visible !important; }
            .flex-1 { overflow: visible !important; }
            main { overflow: visible !important; }
        }
    </style>
</head>
<!-- FIX: Use h-screen and overflow-hidden on body to prevent double scrollbars -->

<body class="bg-gray-100 font-sans antialiased h-screen flex flex-col overflow-hidden">

    <!-- === MOBILE HEADER (Visible only on small screens) === -->
    <div class="md:hidden bg-slate-900 text-white p-4 flex justify-between items-center z-50 flex-shrink-0 shadow-md no-print">
        <a href="{{ route('dashboard') }}" class="flex flex-col leading-none">
            <span class="font-bold text-xl uppercase tracking-tighter">
                <span class="text-white">{{ settings('site_name', 'TECH HUB') }}</span>
            </span>
            <span class="text-[0.6rem] uppercase tracking-[0.2em] text-gray-400 font-bold mt-1">Information Technology</span>
        </a>
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
               class="bg-[#0f172a] text-white w-72 absolute inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 sidebar-transition z-40 h-full flex flex-col border-r border-slate-800 shadow-2xl no-print">

            <!-- App Logo Area -->
            <div class="p-6 mb-2">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                    @if(settings('site_logo_scrolled'))
                        <div class="h-16 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform overflow-hidden">
                            <img src="{{ settings('site_logo_scrolled') }}" alt="Logo" class="w-full h-full object-contain">
                        </div>
                    @else
                        <div class="w-10 h-10 bg-[#2dae9a] rounded-xl flex items-center justify-center shadow-lg shadow-emerald-900/20 group-hover:scale-110 transition-transform">
                            <i class="fas fa-bolt text-white text-xl"></i>
                        </div>
                        <div>
                            <span class="text-xl font-extrabold tracking-tight text-white block leading-none uppercase">TECH<span class="text-[#2dae9a]">HUB</span></span>
                            <span class="text-[0.6rem] font-bold uppercase tracking-widest text-gray-400 mt-1 block">Information Technology</span>
                        </div>
                    @endif
                </a>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 sidebar-scroll overflow-y-auto pb-6">

                <a href="{{ route('dashboard') }}"
                   class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> <span>Dashboard</span>
                </a>

                <!-- PRIMARY ACTION: POS -->
                @can('access pos')
                <div class="px-3 mt-4 mb-6">
                    <a href="{{ route('pos.index') }}"
                       class="flex items-center justify-center gap-3 py-3.5 px-4 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-bold shadow-lg shadow-emerald-900/20 transform hover:-translate-y-0.5 transition-all text-sm">
                        <i class="fas fa-cash-register text-base"></i> Launch POS Terminal
                    </a>
                </div>
                @endcan

                <a href="{{ route('home') }}" target="_blank"
                   class="sidebar-item hover:text-white group">
                    <i class="fas fa-external-link-alt group-hover:text-[#2dae9a] transition-colors"></i> <span>Visit Shop Frontend</span>
                </a>

                <!-- SALES & CUSTOMERS -->
                <div class="sidebar-header">Core Operations</div>
                @can('view quotations')
                <a href="{{ route('quotations.index') }}"
                   class="sidebar-item {{ request()->routeIs('quotations*') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice"></i> <span>Quotations</span>
                </a>
                @endcan
                @can('view orders')
                <a href="{{ route('orders.index') }}"
                   class="sidebar-item {{ request()->routeIs('orders*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-bag"></i> <span>Invoices</span>
                </a>
                @endcan
                @can('view purchases')
                <a href="{{ route('purchases.index') }}"
                   class="sidebar-item {{ request()->routeIs('purchases*') ? 'active' : '' }}">
                    <i class="fas fa-truck-loading"></i> <span>Purchase Orders</span>
                </a>
                @endcan
                <a href="{{ route('delivery-challans.index') }}"
                   class="sidebar-item {{ request()->routeIs('delivery-challans*') ? 'active' : '' }}">
                    <i class="fas fa-truck"></i> <span>Delivery Challans</span>
                </a>
                @can('manage returns')
                <a href="{{ route('returns.index') }}"
                   class="sidebar-item {{ request()->routeIs('returns*') ? 'active' : '' }}">
                    <i class="fas fa-sync-alt"></i> <span>Returns & Refunds</span>
                </a>
                @endcan

                <a href="{{ route('customers.index') }}"
                   class="sidebar-item {{ request()->routeIs('customers*') ? 'active' : '' }}">
                    <i class="fas fa-user-friends"></i> <span>Customer CRM</span>
                </a>

                <div class="sidebar-header">Technical Services</div>
                <a href="{{ route('amcs.index') }}"
                   class="sidebar-item {{ request()->routeIs('amcs*') ? 'active' : '' }}">
                    <i class="fas fa-file-contract"></i> <span>AMC Management</span>
                </a>
                <a href="{{ route('amc-templates.index') }}"
                   class="sidebar-item {{ request()->routeIs('amc-templates*') ? 'active' : '' }}">
                    <i class="fas fa-scroll"></i> <span>Agreement Templates</span>
                </a>

                
                
                <!-- CATALOG & INVENTORY -->
                <div class="sidebar-header">Catalog Management</div>
                @can('view products')
                <a href="{{ route('products.index') }}"
                   class="sidebar-item {{ request()->routeIs('products*') ? 'active' : '' }}">
                    <i class="fas fa-layer-group"></i> <span>Product Catalog</span>
                </a>
                @endcan

                @can('manage categories')
                <a href="{{ route('categories.index') }}"
                   class="sidebar-item {{ request()->routeIs('categories*') ? 'active' : '' }}">
                    <i class="fas fa-th-large"></i> <span>Categories</span>
                </a>
                @endcan

                @can('manage brands')
                <a href="{{ route('brands.index') }}"
                   class="sidebar-item {{ request()->routeIs('brands*') ? 'active' : '' }}">
                    <i class="fas fa-award"></i> <span>Brand Registry</span>
                </a>
                @endcan

                @if(auth()->user()->hasAnyRole(['Admin','Super Admin']))
                <a href="{{ route('menus.index') }}"
                   class="sidebar-item {{ request()->routeIs('menus*') ? 'active' : '' }}">
                    <i class="ri-navigation-line"></i> <span>Menu Builder</span>
                </a>
                @endif

                <!-- FINANCIALS -->
                <div class="sidebar-header">Financial Resources</div>
                
                <a href="{{ route('suppliers.index') }}"
                   class="sidebar-item {{ request()->routeIs('suppliers*') ? 'active' : '' }}">
                    <i class="fas fa-address-book"></i> <span>Supplier Registry</span>
                </a>

                @can('view expenses')
                <a href="{{ route('expenses.index') }}"
                   class="sidebar-item {{ request()->routeIs('expenses*') ? 'active' : '' }}">
                    <i class="fas fa-receipt"></i> <span>Expense Tracking</span>
                </a>
                @endcan

                <!-- MARKETING -->
                <div class="sidebar-header">Growth & Content</div>
                @can('manage coupons')
                <a href="{{ route('coupons.admin.index') }}"
                   class="sidebar-item {{ request()->routeIs('coupons.admin.*') ? 'active' : '' }}">
                    <i class="fas fa-tags"></i> <span>Promo Coupons</span>
                </a>
                @endcan

                @can('manage banners')
                <a href="{{ route('banners.index') }}"
                   class="sidebar-item {{ request()->routeIs('banners.*') ? 'active' : '' }}">
                    <i class="fas fa-paint-brush"></i> <span>Home Experience</span>
                </a>
                @endcan

                @can('manage popups')
                <a href="{{ route('popups.admin.index') }}"
                   class="sidebar-item {{ request()->routeIs('popups.admin.*') ? 'active' : '' }}">
                    <i class="fas fa-bullhorn"></i> <span>Promo Popups</span>
                </a>
                @endcan

                @can('manage solutions')
                <a href="{{ route('solutions.admin.index') }}"
                   class="sidebar-item {{ request()->routeIs('solutions.admin.*') ? 'active' : '' }}">
                    <i class="fas fa-laptop-code"></i> <span>Service Solutions</span>
                </a>
                @endcan

                <a href="{{ route('admin.contact_messages.index') }}"
                   class="sidebar-item {{ request()->routeIs('admin.contact_messages.*') ? 'active' : '' }}">
                    <i class="fas fa-envelope-open-text"></i> <span>Contact Messages</span>
                </a>



                @can('manage pages')
                <a href="{{ route('pages.admin.index') }}"
                   class="sidebar-item {{ request()->routeIs('pages.admin.*') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice"></i> <span>Custom Pages</span>
                </a>
                @endcan

                <!-- ANALYTICS -->
                <div class="sidebar-header">Intelligence & Reports</div>
                @can('view reports')
                <a href="{{ route('reports.sales') }}"
                   class="sidebar-item {{ request()->routeIs('reports.sales') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i> <span>Sales Analytics</span>
                </a>

                <a href="{{ route('reports.purchases') }}"
                   class="sidebar-item {{ request()->routeIs('reports.purchases') ? 'active' : '' }}">
                    <i class="fas fa-shopping-cart"></i> <span>Purchase Reports</span>
                </a>

                <a href="{{ route('reports.profit_loss') }}"
                   class="sidebar-item {{ request()->routeIs('reports.profit_loss') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice-dollar"></i> <span>Profit & Loss Statement</span>
                </a>

                <a href="{{ route('reports.inventory') }}"
                   class="sidebar-item {{ request()->routeIs('reports.inventory') ? 'active' : '' }}">
                    <i class="fas fa-cubes"></i> <span>Stock Evaluation</span>
                </a>

                <a href="{{ route('inventory.transactions') }}"
                   class="sidebar-item {{ request()->routeIs('inventory.transactions') ? 'active' : '' }}">
                    <i class="fas fa-history"></i> <span>Inventory History</span>
                </a>

                <a href="{{ route('reports.vat') }}"
                   class="sidebar-item {{ request()->routeIs('reports.vat') ? 'active' : '' }}">
                    <i class="fas fa-calculator"></i> <span>VAT & Tax Compliance</span>
                </a>

                <a href="{{ route('reports.expenses') }}"
                   class="sidebar-item {{ request()->routeIs('reports.expenses') ? 'active' : '' }}">
                    <i class="fas fa-money-bill-wave"></i> <span>Expense Reports</span>
                </a>

                <a href="{{ route('reports.sales-by-person') }}"
                   class="sidebar-item {{ request()->routeIs('reports.sales-by-person') ? 'active' : '' }}">
                    <i class="fas fa-user-tag"></i> <span>Sales by Sales Person</span>
                </a>

                <a href="{{ route('reports.low-stock') }}"
                   class="sidebar-item {{ request()->routeIs('reports.low-stock') ? 'active' : '' }}">
                    <i class="fas fa-triangle-exclamation"></i> <span>Low Stock Alerts</span>
                </a>
                @endcan

                <!-- ADMINISTRATION -->
                <div class="sidebar-header">Administration</div>
                @can('manage users')
                <a href="{{ route('users.index') }}"
                   class="sidebar-item {{ request()->routeIs('users*') ? 'active' : '' }}">
                    <i class="fas fa-user-lock"></i> <span>Access Control</span>
                </a>
                @endcan

                @can('manage roles')
                <a href="{{ route('roles.index') }}"
                   class="sidebar-item {{ request()->routeIs('roles*') ? 'active' : '' }}">
                    <i class="fas fa-shield-halved"></i> <span>Assign & Manager Roles</span>
                </a>
                @endcan

                @can('manage settings')
                <a href="{{ route('settings.edit') }}"
                   class="sidebar-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <i class="fas fa-sliders-h"></i> <span>General Settings</span>
                </a>
                @endcan

                @can('manage notifications')
                <a href="{{ route('notifications.index') }}"
                   class="sidebar-item {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                    <i class="fas fa-bell"></i> <span>System Notifications</span>
                </a>
                @endcan

                @if(auth()->user()->hasAnyRole(['Admin','Super Admin']))
                <a href="{{ route('activity-logs.index') }}"
                   class="sidebar-item {{ request()->routeIs('activity-logs.*') ? 'active' : '' }}">
                    <i class="fas fa-history"></i> <span>Activity Logs</span>
                </a>
                <a href="{{ route('admin.changelog.index') }}"
                   class="sidebar-item {{ request()->routeIs('admin.changelog.*') ? 'active' : '' }}">
                    <i class="fas fa-list-alt"></i> <span>System Change Log</span>
                </a>
                @endif

            </nav>

            <!-- User Footer Section -->
            <div class="mt-auto p-4 bg-[#0a0f1d] border-t border-slate-800">
                <div class="flex items-center gap-3 p-2 rounded-xl bg-slate-900/50">
                    <div class="relative">
                        <div class="w-10 h-10 rounded-full bg-[#2dae9a] border-2 border-slate-700 flex items-center justify-center font-bold text-white shadow-inner">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-[#0a0f1d] rounded-full"></div>
                    </div>
                    <div class="flex-1 overflow-hidden">
                        <p class="text-[0.8rem] font-bold truncate text-gray-100 leading-tight">{{ Auth::user()->name }}</p>
                        <p class="text-[0.65rem] text-[#2dae9a] font-bold uppercase tracking-wider">{{ Auth::user()->getRoleNames()->first() ?? 'Staff' }}</p>
                        <p class="text-[0.6rem] text-gray-500 font-bold uppercase tracking-widest mt-0.5">Build v{{ app_version() }}</p>
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
            <header class="bg-white shadow-sm h-16 hidden md:flex items-center justify-between px-6 flex-shrink-0 no-print">
                <h2 class="text-xl font-bold text-gray-800">@yield('header')</h2>
                
                <div class="flex items-center gap-6">
                    <!-- Notifications Dropdown -->
                    <div class="relative" id="notification-dropdown-wrapper">
                        <button id="notification-bell" class="relative p-2 text-gray-400 hover:text-[#2dae9a] transition-colors focus:outline-none">
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
                                <a href="{{ route('notifications.markAllAsRead') }}" class="text-[0.65rem] font-bold text-[#2dae9a] hover:underline">Mark all read</a>
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

                    <div class="text-sm text-gray-500 font-medium hidden lg:block">
                        {{ now()->format('l, d F Y') }}
                    </div>

                    <!-- User Dropdown -->
                    <div class="relative" id="user-dropdown-wrapper">
                        <button id="user-dropdown-btn" class="flex items-center gap-3 p-1 rounded-xl hover:bg-gray-50 transition-all focus:outline-none">
                            <div class="w-8 h-8 rounded-lg bg-[#2dae9a] flex items-center justify-center font-bold text-white shadow-sm">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div class="hidden lg:block text-left">
                                <p class="text-xs font-bold text-gray-900 leading-none">{{ Auth::user()->name }}</p>
                                <p class="text-[0.6rem] text-gray-400 font-bold uppercase tracking-widest mt-0.5">{{ Auth::user()->getRoleNames()->first() ?? 'Staff' }}</p>
                            </div>
                            <i class="fas fa-chevron-down text-[0.6rem] text-gray-400 ml-1"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="user-menu" class="absolute right-0 mt-3 w-48 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden z-50 hidden opacity-0 transform scale-95 transition-all duration-200 origin-top-right">
                            <div class="p-2">
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-bold text-gray-600 hover:bg-gray-50 hover:text-[#2dae9a] transition-all">
                                    <i class="fas fa-user-circle text-base"></i> My Profile
                                </a>
                                <a href="{{ route('settings.edit') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-bold text-gray-600 hover:bg-gray-50 hover:text-[#2dae9a] transition-all">
                                    <i class="fas fa-cog text-base"></i> Settings
                                </a>
                                <div class="h-px bg-gray-50 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-bold text-red-600 hover:bg-red-50 transition-all text-left">
                                        <i class="fas fa-power-off text-base"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
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
                    <!-- Session Success Handled by Toastr -->
                @endif

                @if (session('error'))
                    <!-- Session Error Handled by Toastr -->
                @endif

                @if ($errors->any())
                    <!-- Validation Errors Handled by Toastr -->
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Global Deletion & Notification Logic -->
    <script>
        $(document).ready(function() {
            // Configure Toastr
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "timeOut": "5000"
            };

            // Convert Session Messages to Toastr
            @if(session('success'))
                toastr.success("{{ session('success') }}");
            @endif

            @if(session('error'))
                toastr.error("{{ session('error') }}");
            @endif

            @if($errors->any())
                @foreach($errors->all() as $error)
                    toastr.error("{{ $error }}");
                @endforeach
            @endif

            // Global Delete Confirmation
            $(document).on('click', '.btn-delete-confirm', function(e) {
                e.preventDefault();
                const form = $(this).closest('form');
                const title = $(this).data('title') || 'Delete Record?';
                const type = $(this).data('type') || 'Record';
                const summary = $(this).data('summary') || {};
                
                let summaryHtml = `
                    <div class="text-left bg-gray-50 p-4 rounded-lg border border-gray-200 mt-4">
                        <p class="text-xs text-gray-400 font-black uppercase tracking-widest mb-2">${type} Summary</p>
                `;

                for (const [label, value] of Object.entries(summary)) {
                    summaryHtml += `
                        <div class="flex justify-between py-1.5 border-b border-gray-100 last:border-0">
                            <span class="text-gray-500 text-xs font-bold uppercase">${label}:</span>
                            <span class="font-black text-gray-800 text-sm">${value}</span>
                        </div>
                    `;
                }
                summaryHtml += `</div>`;

                Swal.fire({
                    title: title,
                    html: summaryHtml + `<p class="text-sm text-gray-500 mt-4">Are you sure you want to delete this ${type.toLowerCase()}? This action cannot be undone.</p>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '<i class="fas fa-trash-alt mr-2"></i> Yes, Delete',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        confirmButton: 'px-6 py-2.5 rounded-lg font-bold shadow-lg shadow-red-500/30',
                        cancelButton: 'px-6 py-2.5 rounded-lg font-bold'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
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

        // User Dropdown Logic
        const userBtn = document.getElementById('user-dropdown-btn');
        const userMenu = document.getElementById('user-menu');
        const userWrapper = document.getElementById('user-dropdown-wrapper');

        userBtn?.addEventListener('click', (e) => {
            e.stopPropagation();
            userMenu.classList.toggle('hidden');
            setTimeout(() => {
                userMenu.classList.toggle('opacity-0');
                userMenu.classList.toggle('scale-95');
            }, 10);
        });

        document.addEventListener('click', (e) => {
            if (!userWrapper?.contains(e.target)) {
                userMenu?.classList.add('opacity-0', 'scale-95');
                setTimeout(() => userMenu?.classList.add('hidden'), 200);
            }
        });
    </script>

    @stack('scripts')
    @yield('scripts')
</body>

</html>
