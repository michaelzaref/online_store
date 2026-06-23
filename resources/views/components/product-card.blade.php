@props(['product', 'compact' => false])

@php $image = $product->images->first(); @endphp

<div class="card-luxury group {{ $compact ? 'max-w-[180px]' : '' }}">
    <a href="{{ route('products.show', $product->slug) }}" class="block relative aspect-[3/4] bg-gradient-to-b from-brand-bg to-brand-tan p-6 flex items-center justify-center overflow-hidden">
        @if($image)
            <img src="{{ asset('storage/'.$image->path) }}" alt="{{ $product->name }}" class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-500 drop-shadow-lg">
        @else
            <svg class="w-20 h-20 text-brand-text/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        @endif
        @if($product->is_limited_edition)
            <span class="absolute top-3 right-3 text-[10px] tracking-widest uppercase border border-brand-secondary text-brand-secondary px-2 py-0.5 rounded-full">Limited</span>
        @endif
    </a>
    <div class="px-5 pb-5 pt-3 text-center">
        <p class="section-label mb-1">{{ $product->brand?->name ?? 'Watch' }}</p>
        <a href="{{ route('products.show', $product->slug) }}">
            <h3 class="font-display text-sm font-semibold uppercase tracking-wide text-brand-text group-hover:text-brand-accent transition line-clamp-2">{{ $product->name }}</h3>
        </a>
        <p class="mt-2 text-brand-text/80 font-semibold">{{ number_format($product->effective_price) }} {{ config('store.currency_symbol') }}</p>
    </div>
</div>
