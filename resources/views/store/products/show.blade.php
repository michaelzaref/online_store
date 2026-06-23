@extends('layouts.store')

@section('title', $product->name)
@section('meta_description', $product->meta_description ?? Str::limit($product->description, 160))

@section('content')
<div class="max-w-7xl mx-auto">
    {{-- Mobile-style product hero --}}
    <div class="relative bg-gradient-to-b from-brand-bg to-brand-tan px-5 pt-4 pb-8 md:rounded-b-5xl">
        <div class="flex items-center justify-between mb-4">
            <a href="{{ route('products.index') }}" class="btn-icon-circle">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            @auth
                <form action="{{ route('wishlist.toggle', $product) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-icon-circle">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </button>
                </form>
            @endauth
        </div>

        <div class="aspect-square max-w-md mx-auto flex items-center justify-center">
            @if($product->images->first())
                <img src="{{ asset('storage/'.$product->images->first()->path) }}" alt="{{ $product->name }}" class="max-h-full max-w-full object-contain drop-shadow-2xl" id="zoom-img">
            @else
                <svg class="w-32 h-32 text-brand-text/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            @endif
        </div>

        @if($product->images->count() > 1)
            <div class="flex justify-center gap-2 mt-4 overflow-x-auto">
                @foreach($product->images as $image)
                    <button type="button" onclick="document.getElementById('zoom-img').src='{{ asset('storage/'.$image->path) }}'" class="w-14 h-14 rounded-2xl border-2 border-white overflow-hidden flex-shrink-0 shadow-luxury">
                        <img src="{{ asset('storage/'.$image->path) }}" class="w-full h-full object-cover">
                    </button>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Product info --}}
    <div class="px-5 py-8 max-w-2xl mx-auto md:max-w-none md:grid md:grid-cols-2 md:gap-12 md:px-8">
        <div class="md:order-2">
            @if($product->brand)
                <p class="section-label">{{ $product->brand->name }}</p>
            @endif
            <h1 class="font-display text-2xl md:text-4xl font-semibold uppercase tracking-wide text-brand-text mt-1">{{ $product->name }}</h1>
            @if($product->short_description)
                <p class="text-brand-text/60 mt-2 text-sm">{{ $product->short_description }}</p>
            @endif

            <div class="mt-5">
                <span class="price-badge text-base">{{ number_format($product->effective_price) }} {{ config('store.currency_symbol') }}</span>
                @if($product->sale_price)
                    <span class="ml-2 text-brand-text/40 line-through text-sm">{{ number_format($product->price) }}</span>
                @endif
            </div>

            @if($product->case_diameter)
                <div class="mt-8">
                    <p class="section-label mb-3">Case Size</p>
                    <div class="flex gap-2 overflow-x-auto pb-2">
                        @foreach([38, 40, 41, 42] as $size)
                            <button type="button" class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium border transition {{ abs($product->case_diameter - $size) < 1 ? 'bg-brand-primary text-white border-brand-primary' : 'bg-white border-brand-text/15 text-brand-text/60' }}">
                                {{ $size }}MM
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($product->color)
                <div class="mt-6">
                    <p class="section-label mb-3">Color</p>
                    <span class="inline-block px-4 py-2 rounded-full bg-white border border-brand-text/15 text-sm">{{ $product->color }}</span>
                </div>
            @endif

            <div class="mt-6">
                @if($product->isInStock())
                    <span class="text-brand-accent text-sm font-medium">● In Stock</span>
                @else
                    <span class="text-red-600 text-sm font-medium">Out of Stock</span>
                @endif
            </div>

            {{-- Specs --}}
            <div class="mt-10 card-luxury p-6">
                <h3 class="section-label mb-4">Specifications</h3>
                <dl class="grid grid-cols-2 gap-3 text-sm">
                    @foreach([
                        'Movement' => $product->movement_type,
                        'Type' => $product->watch_type ? ucfirst($product->watch_type) : null,
                        'Case' => $product->case_material,
                        'Strap' => $product->strap_material,
                        'Water' => $product->water_resistance,
                        'Glass' => $product->glass_type,
                    ] as $label => $value)
                        @if($value)
                            <dt class="text-brand-text/50">{{ $label }}</dt>
                            <dd class="font-medium">{{ $value }}</dd>
                        @endif
                    @endforeach
                </dl>
            </div>

            @if($product->description)
                <p class="mt-6 text-sm text-brand-text/70 leading-relaxed">{{ $product->description }}</p>
            @endif
        </div>

        {{-- Desktop buy section --}}
        <div class="hidden md:flex md:order-1 md:items-start md:pt-8">
            @if($product->isInStock())
                <form action="{{ route('cart.add', $product) }}" method="POST" class="w-full buy-bar md:rounded-full static">
                    @csrf
                    <input type="hidden" name="quantity" value="1">
                    <span class="text-white/60 text-sm hidden lg:block">SKU: {{ $product->sku }}</span>
                    <button type="submit" class="btn-pill-white flex-1 text-center">Buy Now</button>
                    <a href="{{ route('cart.index') }}" class="btn-icon-circle bg-white/10 text-white border-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </a>
                </form>
            @endif
        </div>
    </div>

    {{-- Mobile fixed buy bar --}}
    @if($product->isInStock())
        <form action="{{ route('cart.add', $product) }}" method="POST" class="buy-bar md:hidden">
            @csrf
            <input type="hidden" name="quantity" value="1">
            <span class="text-white/50">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
            <button type="submit" class="btn-pill-white flex-1 text-center py-3">Buy Now</button>
            <a href="{{ route('cart.index') }}" class="text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </a>
        </form>
    @endif

    @if($related->isNotEmpty())
        <section class="max-w-7xl mx-auto px-5 py-12 pb-28 md:pb-12">
            <h2 class="section-title mb-6">You May Also Like</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($related as $item)
                    <x-product-card :product="$item" />
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
