@extends('layouts.store')

@section('title', 'Order '.$order->order_number)

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        @include('store.account.partials.sidebar')
        <div class="lg:col-span-3">
            <h1 class="font-display text-2xl font-bold mb-2">Order {{ $order->order_number }}</h1>
            <p class="text-brand-text/60 text-sm mb-6">Placed on {{ $order->created_at->format('F d, Y \a\t g:i A') }}</p>

            <div class="bg-white border border-brand-text/10 rounded-lg p-6 mb-6">
                <h3 class="font-semibold mb-4">Items</h3>
                @foreach($order->items as $item)
                    <div class="flex justify-between py-3 border-b border-brand-text/10 last:border-0">
                        <div>
                            <p class="font-medium">{{ $item->product_name }}</p>
                            <p class="text-sm text-brand-text/60">SKU: {{ $item->product_sku }} · Qty: {{ $item->quantity }}</p>
                        </div>
                        <p class="font-semibold">{{ number_format($item->total_price) }} EGP</p>
                    </div>
                @endforeach
                <div class="mt-4 pt-4 border-t border-brand-text/10 space-y-1 text-sm">
                    <div class="flex justify-between"><span>Subtotal</span><span>{{ number_format($order->subtotal) }}</span></div>
                    @if($order->discount_amount > 0)<div class="flex justify-between text-brand-accent"><span>Discount</span><span>-{{ number_format($order->discount_amount) }}</span></div>@endif
                    <div class="flex justify-between"><span>Shipping</span><span>{{ number_format($order->shipping_amount) }}</span></div>
                    <div class="flex justify-between font-bold text-lg"><span>Total</span><span>{{ number_format($order->total) }} EGP</span></div>
                </div>
            </div>

            @if($order->tracking_number)
                <div class="bg-brand-accent/5 border border-brand-accent/20 rounded-lg p-4 mb-6">
                    <p class="font-medium">Tracking: {{ $order->tracking_number }}</p>
                    @if($order->shipping_carrier)<p class="text-sm text-brand-text/70">Carrier: {{ $order->shipping_carrier }}</p>@endif
                </div>
            @endif

            <div class="bg-white border border-brand-text/10 rounded-lg p-6">
                <h3 class="font-semibold mb-4">Order Timeline</h3>
                @foreach($order->statusHistory as $history)
                    <div class="flex gap-3 mb-3 text-sm">
                        <div class="w-2 h-2 bg-brand-accent rounded-full mt-1.5"></div>
                        <div>
                            <p class="font-medium capitalize">{{ $history->status }}</p>
                            <p class="text-brand-text/60">{{ $history->created_at->format('M d, Y g:i A') }}</p>
                            @if($history->comment)<p class="text-brand-text/70">{{ $history->comment }}</p>@endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
