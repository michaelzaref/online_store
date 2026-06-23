@extends('layouts.store')

@section('title', 'Checkout')

@section('content')
<div class="max-w-7xl mx-auto px-5 py-8 pb-28 md:pb-8">
    <p class="section-label">Secure Checkout</p>
    <h1 class="section-title mb-8">Checkout</h1>

    <form action="{{ route('checkout.store') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        @csrf
        <div class="lg:col-span-2 space-y-6">
            @guest
                <div class="card-luxury p-6">
                    <h3 class="section-label mb-4">Guest Information</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <input type="text" name="guest_name" placeholder="Full Name" required class="input-field col-span-2 rounded-full">
                        <input type="email" name="guest_email" placeholder="Email" required class="input-field col-span-2 rounded-full">
                    </div>
                </div>
            @endguest

            @foreach(['billing' => 'Billing Address', 'shipping' => 'Shipping Address'] as $type => $label)
                <div class="card-luxury p-6">
                    <h3 class="section-label mb-4">{{ $label }}</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <input type="text" name="{{ $type }}_address[first_name]" placeholder="First Name" required class="input-field rounded-full">
                        <input type="text" name="{{ $type }}_address[last_name]" placeholder="Last Name" required class="input-field rounded-full">
                        <input type="tel" name="{{ $type }}_address[phone]" placeholder="Phone" required class="input-field col-span-2 rounded-full">
                        <input type="text" name="{{ $type }}_address[address_line_1]" placeholder="Address" required class="input-field col-span-2 rounded-full">
                        <input type="text" name="{{ $type }}_address[city]" placeholder="City" required class="input-field rounded-full">
                        <input type="text" name="{{ $type }}_address[postal_code]" placeholder="Postal Code" required class="input-field rounded-full">
                        <input type="text" name="{{ $type }}_address[country]" value="EG" placeholder="Country (EG)" required class="input-field col-span-2 rounded-full">
                    </div>
                </div>
            @endforeach

            <div class="card-luxury p-6">
                <h3 class="section-label mb-2">Delivery Option</h3>
                <p class="text-sm text-brand-text/60 mb-4">Delivery is arranged manually after you place your order.</p>
                @foreach($shippingMethods as $method)
                    <label class="flex items-center gap-3 p-4 rounded-2xl border border-brand-text/10 mb-2 cursor-pointer hover:border-brand-accent transition">
                        <input type="radio" name="shipping_method_id" value="{{ $method->id }}" required {{ $loop->first ? 'checked' : '' }}>
                        <div class="flex-1">
                            <span class="font-medium">{{ $method->name }}</span>
                            @if($method->description)<p class="text-sm text-brand-text/60">{{ $method->description }}</p>@endif
                        </div>
                        <span class="price-badge text-sm">{{ number_format($method->base_rate) }} {{ config('store.currency_symbol') }}</span>
                    </label>
                @endforeach
            </div>

            <div class="card-luxury p-6 border-brand-accent/30 bg-brand-accent/5">
                <h3 class="font-semibold mb-2 text-brand-accent">Cash on Delivery</h3>
                <p class="text-sm text-brand-text/70">Pay in cash when your order arrives. No online payment required.</p>
            </div>

            <div class="card-luxury p-6">
                <label class="flex items-center gap-2 mb-3">
                    <input type="checkbox" name="gift_wrap" value="1">
                    <span class="text-sm">Add gift wrapping</span>
                </label>
                <textarea name="gift_message" rows="2" placeholder="Gift message (optional)" class="input-field w-full text-sm mb-3 rounded-2xl"></textarea>
                <textarea name="notes" rows="2" placeholder="Order notes (optional)" class="input-field w-full text-sm rounded-2xl"></textarea>
            </div>
        </div>

        <div class="card-luxury p-6 h-fit lg:sticky lg:top-24">
            <h3 class="section-label mb-4">Order Summary</h3>
            @foreach($cart->items as $item)
                <div class="flex justify-between text-sm mb-2 gap-2">
                    <span class="truncate">{{ $item->product->name }} × {{ $item->quantity }}</span>
                    <span class="flex-shrink-0">{{ number_format($item->total()) }}</span>
                </div>
            @endforeach
            <div class="border-t border-brand-text/10 pt-3 mt-3 space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-brand-text/60">Subtotal</span><span>{{ number_format($totals['subtotal']) }}</span></div>
                @if($totals['discount'] > 0)
                    <div class="flex justify-between text-brand-accent"><span>Discount</span><span>-{{ number_format($totals['discount']) }}</span></div>
                @endif
            </div>
            <button type="submit" class="w-full btn-primary mt-6">Place Order</button>
        </div>
    </form>
</div>
@endsection
