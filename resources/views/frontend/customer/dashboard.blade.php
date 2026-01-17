@extends('layouts.frontend')

@section('title', 'My Account | Tech Hub')



@section('content')
    <div class="container">

        <div class="page-header">
            <h1 class="page-title">My Dashboard</h1>
            <span style="font-size: 0.9rem; color: var(--text-muted);">
            Member since {{ Auth::guard('customer')->user()->created_at->format('M Y') }}
        </span>
        </div>

        <div class="account-layout">

            <!-- INCLUDE SIDEBAR PARTIAL -->
            @include('frontend.customer.partials.sidebar')

            <!-- DASHBOARD CONTENT -->
            <div class="dash-content">

                <!-- Stats Grid -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon bg-blue-light"><i class="ri-box-3-fill"></i></div>
                        <div class="stat-info">
                            <h4>Total Orders</h4>
                            <span>{{ $totalOrders }}</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon bg-orange-light"><i class="ri-time-fill"></i></div>
                        <div class="stat-info">
                            <h4>Pending</h4>
                            <span>{{ $pendingOrders }}</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon bg-green-light"><i class="ri-wallet-3-fill"></i></div>
                        <div class="stat-info">
                            <h4>Wallet</h4>
                            <span>{{ number_format($walletBalance, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders Table -->
                <div class="content-card">
                    <div class="card-header">
                        <h2 class="card-title">Recent Orders</h2>
                        <a href="{{ route('customer.orders') }}" class="link-btn">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="order-table">
                            <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td style="font-weight:600;">#{{ $order->invoice_no }}</td>
                                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                                    <td><span class="status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                                    <td>{{ number_format($order->total, 2) }} AED</td>
                                    <td><a href="{{ route('customer.orders.show', $order) }}" class="btn-sm-outline">View</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center p-4">No recent orders.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
