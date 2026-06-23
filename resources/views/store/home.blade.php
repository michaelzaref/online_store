@extends('layouts.store')

@section('title', 'Home')

@section('content')
    {{-- Hero — reference splash style --}}
    @php
        $banner = $banners->first();
        $heroProduct = $featured->first() ?? $newArrivals->first();
        $heroImage = $banner?->image
            ? asset('storage/'.$banner->image)
            : ($heroProduct?->images->first() ? asset('storage/'.$heroProduct->images->first()->path) : null);
    @endphp
    <section class="hero-full">
        <div class="crown-watermark !absolute inset-0 md:!justify-start md:!pl-[6%] md:opacity-[0.05]">
            <svg class="w-64 h-64 md:w-[32rem] md:h-[32rem] text-brand-primary" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.4 5.8 6.3.5-4.8 4.1 1.5 6.2L12 15.9 6.6 18.6l1.5-6.2L3.3 8.3l6.3-.5L12 2z"/></svg>
        </div>

        {{-- Copy --}}
        <div class="hero-full-copy order-2 md:order-1 text-center md:text-left">
            <p class="section-label mb-3">{{ config('store.name') }}</p>
            <h1 class="font-display text-3xl sm:text-4xl md:text-5xl xl:text-6xl font-semibold text-brand-text uppercase tracking-wide leading-[1.08]">
                {{ $banner?->title ?? 'Precision Meets Elegance' }}
            </h1>
            <p class="mt-4 md:mt-6 text-sm md:text-base xl:text-lg text-brand-text/60 leading-relaxed max-w-lg mx-auto md:mx-0">
                {{ $banner?->subtitle ?? 'Tradition shaped through master craftsmanship, precision and timeless elegance.' }}
            </p>
            <div class="mt-8 md:mt-10 flex flex-col sm:flex-row items-center md:items-start gap-4">
                <a href="{{ $banner?->link ?? route('products.index') }}" class="btn-primary">
                    {{ $banner?->button_text ?? 'Shop Collection' }}
                </a>
                <div class="hidden md:flex items-center gap-5 text-brand-text/40">
                    <span class="w-9 h-9 rounded-full border border-brand-text/20 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                    <span class="tracking-[0.4em] text-xs">&rsaquo; &rsaquo; &rsaquo;</span>
                    <a href="{{ route('products.index') }}" class="w-10 h-10 rounded-full bg-brand-primary text-white flex items-center justify-center hover:bg-brand-accent transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>

        {{-- Full-bleed hero image --}}
        @if($heroImage)
            <div class="hero-full-media order-1 md:order-2">
                <img src="{{ $heroImage }}" alt="{{ $banner?->title ?? 'Hero watch' }}">
            </div>
        @else
            <div class="hero-full-media order-1 md:order-2 hero-gradient"></div>
        @endif

        {{-- Mobile swipe hint --}}
        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex items-center gap-6 text-brand-text/40 md:hidden z-20">
            <span class="w-8 h-8 rounded-full border border-brand-text/20 flex items-center justify-center bg-white/80">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
            <span class="tracking-[0.5em] text-xs">&rsaquo; &rsaquo; &rsaquo;</span>
            <a href="{{ route('products.index') }}" class="w-10 h-10 rounded-full bg-brand-primary text-white flex items-center justify-center hover:bg-brand-accent transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </section>

    {{-- New Watches --}}
    @if($newArrivals->isNotEmpty())
        <section class="max-w-7xl mx-auto px-5 py-12">
            <div class="flex items-center justify-between mb-6">
                <h2 class="section-title">New Watches</h2>
                <a href="{{ route('products.index', ['sort' => 'newest']) }}" class="text-sm text-brand-text/50 hover:text-brand-accent transition">See all</a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                @foreach($newArrivals->take(4) as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- Collection feature card --}}
    @if($featured->isNotEmpty())
        @php $collectionProduct = $featured->first(); @endphp
        <section class="max-w-7xl mx-auto px-5 pb-12">
            <div class="flex items-center justify-between mb-6">
                <h2 class="section-title">Collection</h2>
                <a href="{{ route('products.index') }}" class="text-sm text-brand-text/50 hover:text-brand-accent transition">See all</a>
            </div>
            <a href="{{ route('products.show', $collectionProduct->slug) }}" class="card-luxury flex flex-col md:flex-row overflow-hidden group">
                <div class="md:w-1/2 aspect-square md:aspect-auto bg-gradient-to-br from-brand-primary to-brand-accent flex items-center justify-center p-10">
                    @if($collectionProduct->images->first())
                        <img src="{{ asset('storage/'.$collectionProduct->images->first()->path) }}" alt="{{ $collectionProduct->name }}" class="max-h-64 object-contain drop-shadow-2xl group-hover:scale-105 transition-transform duration-500">
                    @endif
                </div>
                <div class="md:w-1/2 p-8 md:p-12 flex flex-col justify-center">
                    <p class="section-label mb-2">{{ $collectionProduct->brand?->name }}</p>
                    <h3 class="font-display text-2xl md:text-3xl font-semibold uppercase tracking-wide text-brand-text">{{ $collectionProduct->name }}</h3>
                    @if($collectionProduct->case_diameter)
                        <p class="text-brand-text/50 mt-2 text-sm">{{ $collectionProduct->case_diameter }} MM</p>
                    @endif
                    <div class="mt-6 flex items-center justify-between">
                        <span class="price-badge">{{ number_format($collectionProduct->effective_price) }} {{ config('store.currency_symbol') }}</span>
                        <span class="btn-icon-circle-dark">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17L17 7M17 7H7M17 7v10"/></svg>
                        </span>
                    </div>
                </div>
            </a>
        </section>
    @endif

    {{-- Best Sellers --}}
    @if($bestSellers->isNotEmpty())
        <section class="max-w-7xl mx-auto px-5 py-12 bg-white/50 rounded-5xl mx-5 mb-8">
            <div class="flex items-center justify-between mb-6 px-2">
                <h2 class="section-title">Best Sellers</h2>
                <a href="{{ route('products.index', ['sort' => 'best_selling']) }}" class="text-sm text-brand-text/50 hover:text-brand-accent transition">See all</a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                @foreach($bestSellers->take(4) as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        </section>
    @endif

    @if($brands->isNotEmpty())
        <section class="max-w-7xl mx-auto px-5 py-12">
            <h2 class="section-title text-center mb-8">Our Brands</h2>
            <div class="flex flex-wrap justify-center gap-4">
                @foreach($brands as $brand)
                    <a href="{{ route('products.index', ['brand' => $brand->slug]) }}" class="px-6 py-3 rounded-full bg-white shadow-luxury text-sm font-medium text-brand-text hover:text-brand-accent transition border border-brand-text/5">
                        {{ $brand->name }}
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    @if($testimonials->isNotEmpty())
        <section class="max-w-7xl mx-auto px-5 py-12 pb-20">
            <h2 class="section-title text-center mb-8">Testimonials</h2>
            <div class="grid md:grid-cols-3 gap-5">
                @foreach($testimonials as $testimonial)
                    <div class="card-luxury p-6">
                        <div class="flex text-brand-secondary text-sm mb-3">@for($i = 0; $i < $testimonial->rating; $i++)★@endfor</div>
                        <p class="text-sm text-brand-text/70 leading-relaxed">"{{ $testimonial->content }}"</p>
                        <p class="mt-4 font-medium text-sm">{{ $testimonial->customer_name }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
@endsection
