@extends('layouts.store')

@section('title', 'Order Confirmed')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-16 text-center">
    <div class="w-16 h-16 bg-brand-accent/10 rounded-full flex items-center justify-center mx-auto mb-6">
        <svg class="w-8 h-8 text-brand-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    </div>
    <h1 class="font-display text-3xl font-bold mb-2">Thank You!</h1>
    <p class="text-brand-text/70 mb-2">Your order <strong>{{ $order->order_number }}</strong> has been placed.</p>
    <p class="text-brand-text/70 mb-6">We'll contact you to arrange delivery. Please have <strong>{{ number_format($order->total) }} {{ config('store.currency_symbol') }}</strong> ready in cash on delivery.</p>

    <div class="bg-white border border-brand-text/10 rounded-lg p-6 text-left mb-8">
        <dl class="grid grid-cols-2 gap-3 text-sm">
            <dt class="text-brand-text/60">Order Number</dt><dd class="font-medium">{{ $order->order_number }}</dd>
            <dt class="text-brand-text/60">Status</dt><dd class="font-medium capitalize">{{ $order->status }}</dd>
            <dt class="text-brand-text/60">Total (pay on delivery)</dt><dd class="font-medium">{{ number_format($order->total) }} {{ config('store.currency_symbol') }}</dd>
            <dt class="text-brand-text/60">Payment</dt><dd class="font-medium">Cash on Delivery</dd>
        </dl>
    </div>

    <div class="flex gap-4 justify-center">
        @auth
            <a href="{{ route('account.orders.show', $order) }}" class="bg-brand-primary text-white px-6 py-3 rounded hover:bg-brand-accent transition">Track Order</a>
        @endauth
        <a href="{{ route('products.index') }}" class="border border-brand-text/15 px-6 py-3 rounded hover:border-brand-secondary transition">Continue Shopping</a>
    </div>
</div>
@endsection
