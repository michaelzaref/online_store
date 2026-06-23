@extends('layouts.store')

@section('title', 'Shopping Cart')

@section('content')
<div class="max-w-7xl mx-auto px-5 py-8 pb-28 md:pb-8">
    <p class="section-label">Your Bag</p>
    <h1 class="section-title mb-8">Shopping Cart</h1>

    @if($cart->items->isEmpty())
        <div class="card-luxury p-16 text-center">
            <p class="text-brand-text/60 mb-6">Your cart is empty.</p>
            <a href="{{ route('products.index') }}" class="btn-primary">Continue Shopping</a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-4">
                @foreach($cart->items as $item)
                    <div class="card-luxury p-4 flex gap-4">
                        <div class="w-24 h-24 bg-gradient-to-b from-brand-bg to-brand-tan rounded-2xl overflow-hidden flex-shrink-0 flex items-center justify-center">
                            @if($item->product->images->first())
                                <img src="{{ asset('storage/'.$item->product->images->first()->path) }}" class="max-h-full max-w-full object-contain">
                            @else
                                <svg class="w-10 h-10 text-brand-text/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('products.show', $item->product->slug) }}" class="font-display font-semibold uppercase text-sm tracking-wide hover:text-brand-accent">{{ $item->product->name }}</a>
                            <p class="text-xs text-brand-text/50 mt-1">{{ $item->product->brand?->name }}</p>
                            <p class="font-semibold mt-2">{{ number_format($item->price) }} {{ config('store.currency_symbol') }}</p>
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            <form action="{{ route('cart.update', $item) }}" method="POST" class="flex items-center gap-2">
                                @csrf @method('PATCH')
                                <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock_quantity }}" class="input-field w-16 text-center text-sm rounded-full py-1" onchange="this.form.submit()">
                            </form>
                            <p class="font-semibold text-sm">{{ number_format($item->total()) }} {{ config('store.currency_symbol') }}</p>
                            <form action="{{ route('cart.remove', $item) }}" method="POST">
                                @csrf @method('DELETE')
                                <button class="text-red-600 text-xs hover:underline">Remove</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="card-luxury p-6 h-fit lg:sticky lg:top-24">
                <h3 class="section-label mb-4">Order Summary</h3>

                <form action="{{ route('cart.coupon') }}" method="POST" class="flex gap-2 mb-4">
                    @csrf
                    <input type="text" name="code" placeholder="Coupon code" value="{{ $cart->coupon?->code }}" class="input-field flex-1 rounded-full text-sm">
                    <button type="submit" class="btn-primary px-4 py-2 text-sm">Apply</button>
                </form>
                @if($cart->coupon)
                    <form action="{{ route('cart.coupon.remove') }}" method="POST" class="mb-4">
                        @csrf @method('DELETE')
                        <button class="text-sm text-red-600 hover:underline">Remove: {{ $cart->coupon->code }}</button>
                    </form>
                @endif

                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-brand-text/60">Subtotal</dt><dd>{{ number_format($totals['subtotal']) }} {{ config('store.currency_symbol') }}</dd></div>
                    @if($totals['discount'] > 0)
                        <div class="flex justify-between text-brand-accent"><dt>Discount</dt><dd>-{{ number_format($totals['discount']) }}</dd></div>
                    @endif
                    <div class="flex justify-between"><dt class="text-brand-text/60">Shipping</dt><dd class="text-brand-text/60">At checkout</dd></div>
                    @if($totals['tax'] > 0)
                        <div class="flex justify-between"><dt class="text-brand-text/60">Tax</dt><dd>{{ number_format($totals['tax']) }}</dd></div>
                    @endif
                    <div class="flex justify-between font-bold text-lg border-t border-brand-text/10 pt-3 mt-3">
                        <dt>Total</dt><dd>{{ number_format($totals['total']) }} {{ config('store.currency_symbol') }}</dd>
                    </div>
                </dl>

                <a href="{{ route('checkout.index') }}" class="block w-full btn-primary text-center mt-6">Proceed to Checkout</a>
            </div>
        </div>
    @endif
</div>
@endsection
