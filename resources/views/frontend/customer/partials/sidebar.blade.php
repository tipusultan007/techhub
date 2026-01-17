@push('styles')
    <style>

        .page-header { margin: 30px 0; display: flex; justify-content: space-between; align-items: center; }
        .page-title { font-size: 1.8rem; font-weight: 800; color: var(--text-main); }

        .account-layout {
            display: grid; grid-template-columns: 280px 1fr; gap: 30px; margin-bottom: 60px;
        }

        /* Sidebar Navigation */
        .account-sidebar {
            background: white; border-radius: var(--radius); border: 1px solid var(--border);
            overflow: hidden; height: fit-content; box-shadow: var(--shadow);
        }

        .user-mini-profile {
            padding: 25px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 15px;
        }
        .user-avatar {
            width: 50px; height: 50px; background: #eff6ff; color: var(--brand-deep-blue); border-radius: 50%;
            display: flex; align-items: center; justify-content: center; font-size: 1.2rem; font-weight: 700;
        }
        .user-info h3 { font-size: 1rem; font-weight: 700; margin-bottom: 2px; }
        .user-info p { font-size: 0.8rem; color: var(--text-muted); word-break: break-all; }

        .sidebar-menu { padding: 10px 0; }
        .menu-link {
            display: flex; align-items: center; gap: 12px; padding: 12px 25px;
            color: var(--text-muted); font-weight: 500; font-size: 0.95rem;
            border-left: 3px solid transparent; transition: 0.2s;
        }
        .menu-link:hover { background: #f8fafc; color: var(--brand-magenta); }
        .menu-link.active {
            background: #fdf4ff; color: var(--brand-magenta); border-left-color: var(--brand-magenta); font-weight: 700;
        }
        .menu-link i { font-size: 1.2rem; }

        .logout-btn {
            width: 100%; text-align: left; background: none; border: none; cursor: pointer;
            color: #ef4444 !important; display: flex; align-items: center; gap: 12px; padding: 12px 25px; font-weight: 500; font-size: 0.95rem;
        }
        .logout-btn:hover { background: #fef2f2 !important; }

        /* Dashboard Content */
        .dash-content { display: flex; flex-direction: column; gap: 30px; }

        /* Stats Cards */
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .stat-card {
            background: white; border-radius: var(--radius); border: 1px solid var(--border);
            padding: 20px; display: flex; align-items: center; gap: 20px; transition: 0.2s;
            box-shadow: var(--shadow);
        }
        .stat-card:hover { transform: translateY(-3px); }
        .stat-icon {
            width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;
        }
        .bg-blue-light { background: #eff6ff; color: var(--brand-deep-blue); }
        .bg-green-light { background: #ecfdf5; color: #10b981; }
        .bg-orange-light { background: #fff7ed; color: #f59e0b; }

        .stat-info h4 { font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; margin-bottom: 5px; }
        .stat-info span { font-size: 1.5rem; font-weight: 800; color: var(--text-main); }

        /* Content Sections */
        .content-card {
            background: white; border: 1px solid var(--border); border-radius: var(--radius); padding: 25px; box-shadow: var(--shadow);
        }
        .card-header {
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #f1f5f9;
        }
        .card-title { font-size: 1.1rem; font-weight: 800; color: var(--text-main); }
        .link-btn { font-size: 0.9rem; color: var(--brand-magenta); font-weight: 600; text-decoration: none; }
        .link-btn:hover { text-decoration: underline; }

        /* Table */
        .table-responsive { overflow-x: auto; }
        .order-table { width: 100%; border-collapse: collapse; min-width: 600px; }
        .order-table th { text-align: left; padding: 12px; background: #f8fafc; font-size: 0.8rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; }
        .order-table td { padding: 15px 12px; border-bottom: 1px solid #f1f5f9; font-size: 0.95rem; }
        .order-table tr:last-child td { border-bottom: none; }

        .status-badge { padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; display: inline-block; }
        .status-delivered { background: #ecfdf5; color: #10b981; }
        .status-processing { background: #fff7ed; color: #f59e0b; }
        .status-cancelled { background: #fef2f2; color: #ef4444; }
        .status-pending { background: #fefce8; color: #ca8a04; }

        .btn-sm-outline {
            padding: 6px 14px; border: 1px solid var(--border); border-radius: 4px; font-size: 0.85rem; font-weight: 600; color: var(--text-main); cursor: pointer; transition: 0.2s; background: white; text-decoration: none; display: inline-block;
        }
        .btn-sm-outline:hover { border-color: var(--brand-deep-blue); color: var(--brand-deep-blue); }

        /* Address Grid */
        .address-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .address-box { border: 1px solid var(--border); border-radius: 8px; padding: 20px; position: relative; }
        .addr-name { font-weight: 700; margin-bottom: 10px; font-size: 1rem; }
        .addr-text { font-size: 0.9rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 15px; }

        @media (max-width: 900px) {
            .account-layout { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: 1fr; }
            .address-grid { grid-template-columns: 1fr; }
        }
    </style>
@endpush

<aside class="account-sidebar">
    <div class="user-mini-profile">
        <div class="user-avatar">
            {{ substr(Auth::guard('customer')->user()->name, 0, 2) }}
        </div>
        <div class="user-info">
            <h3>{{ Auth::guard('customer')->user()->name }}</h3>
            <p>{{ Auth::guard('customer')->user()->email }}</p>
        </div>
    </div>

    <nav class="sidebar-menu">
        <!-- Dashboard -->
        <a href="{{ route('customer.dashboard') }}"
           class="menu-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
            <i class="ri-dashboard-line"></i> Dashboard
        </a>

        <!-- Orders -->
        <a href="{{ route('customer.orders') }}"
           class="menu-link {{ request()->routeIs('customer.orders*') ? 'active' : '' }}">
            <i class="ri-box-3-line"></i> Orders
        </a>

        <!-- Wishlist -->
        <a href="{{ route('customer.wishlist') }}"
           class="menu-link {{ request()->routeIs('customer.wishlist*') ? 'active' : '' }}">
            <i class="ri-heart-line"></i> Wishlist
        </a>

        <!-- Addresses -->
        <a href="{{ route('customer.addresses') }}"
           class="menu-link {{ request()->routeIs('customer.addresses*') ? 'active' : '' }}">
            <i class="ri-map-pin-line"></i> Addresses
        </a>

        <!-- Account Details (Profile) -->
        <a href="{{ route('customer.profile.edit') }}"
           class="menu-link {{ request()->routeIs('customer.profile*') ? 'active' : '' }}">
            <i class="ri-settings-4-line"></i> Account Details
        </a>

        <!-- Logout -->
        <form action="{{ route('customer.logout') }}" method="POST">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="ri-logout-box-r-line"></i> Log Out
            </button>
        </form>
    </nav>
</aside>
