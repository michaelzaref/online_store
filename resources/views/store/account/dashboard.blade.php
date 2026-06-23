@extends('layouts.store')

@section('title', 'My Account')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="font-display text-3xl font-bold mb-8">My Account</h1>
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        @include('store.account.partials.sidebar')
        <div class="lg:col-span-3">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white border border-brand-text/10 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-brand-text">{{ $ordersCount }}</p>
                    <p class="text-sm text-brand-text/60">Orders</p>
                </div>
                <div class="bg-white border border-brand-text/10 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-brand-text">{{ number_format($totalSpent) }}</p>
                    <p class="text-sm text-brand-text/60">Total Spent (EGP)</p>
                </div>
                <div class="bg-white border border-brand-text/10 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-brand-text">{{ $wishlistCount }}</p>
                    <p class="text-sm text-brand-text/60">Wishlist</p>
                </div>
                <div class="bg-white border border-brand-text/10 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-brand-text">{{ $loyaltyPoints }}</p>
                    <p class="text-sm text-brand-text/60">Loyalty Points</p>
                </div>
            </div>

            <h2 class="font-semibold text-lg mb-4">Recent Orders</h2>
            @forelse($recentOrders as $order)
                <div class="bg-white border border-brand-text/10 rounded-lg p-4 mb-3 flex justify-between items-center">
                    <div>
                        <p class="font-medium">{{ $order->order_number }}</p>
                        <p class="text-sm text-brand-text/60">{{ $order->created_at->format('M d, Y') }} · <span class="capitalize">{{ $order->status }}</span></p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold">{{ number_format($order->total) }} EGP</p>
                        <a href="{{ route('account.orders.show', $order) }}" class="text-sm text-brand-accent hover:underline">View</a>
                    </div>
                </div>
            @empty
                <p class="text-brand-text/60">No orders yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
