@extends('layouts.store')

@section('title', 'My Orders')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="font-display text-3xl font-bold mb-8">My Orders</h1>
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        @include('store.account.partials.sidebar')
        <div class="lg:col-span-3">
            @forelse($orders as $order)
                <div class="bg-white border border-brand-text/10 rounded-lg p-4 mb-4">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <p class="font-medium">{{ $order->order_number }}</p>
                            <p class="text-sm text-brand-text/60">{{ $order->created_at->format('F d, Y') }}</p>
                        </div>
                        <span class="text-sm px-3 py-1 rounded-full capitalize
                            @if($order->status === 'delivered') bg-brand-accent/10 text-brand-accent
                            @elseif($order->status === 'cancelled') bg-red-100 text-red-700
                            @else bg-brand-accent/10 text-brand-accent @endif">
                            {{ $order->status }}
                        </span>
                    </div>
                    <div class="text-sm text-brand-text/70 mb-3">
                        @foreach($order->items as $item)
                            <span>{{ $item->product_name }} × {{ $item->quantity }}</span>@if(!$loop->last), @endif
                        @endforeach
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-semibold">{{ number_format($order->total) }} EGP</span>
                        <a href="{{ route('account.orders.show', $order) }}" class="text-brand-accent text-sm hover:underline">View Details →</a>
                    </div>
                </div>
            @empty
                <p class="text-brand-text/60">No orders yet. <a href="{{ route('products.index') }}" class="text-brand-accent hover:underline">Start shopping</a></p>
            @endforelse
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
