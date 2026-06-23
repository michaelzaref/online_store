@extends('layouts.store')

@section('title', 'Shop')

@section('content')
<div class="max-w-7xl mx-auto px-5 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <p class="section-label">Catalog</p>
            <h1 class="section-title">All Watches</h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ request()->fullUrlWithQuery(['view' => 'grid']) }}" class="btn-icon-circle {{ ($view ?? 'grid') === 'grid' ? 'ring-2 ring-brand-accent' : '' }}">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16"><path d="M1 1h6v6H1V1zm8 0h6v6H9V1zM1 9h6v6H1V9zm8 0h6v6H9V9z"/></svg>
            </a>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        <aside class="lg:w-64 flex-shrink-0">
            <form method="GET" class="card-luxury p-5 space-y-4 sticky top-24">
                <h3 class="section-label">Filters</h3>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search..." class="input-field w-full rounded-full">
                <select name="brand" class="input-field w-full rounded-full">
                    <option value="">All Brands</option>
                    @foreach($filterOptions['brands'] as $brand)
                        <option value="{{ $brand->slug }}" @selected(($filters['brand'] ?? '') === $brand->slug)>{{ $brand->name }}</option>
                    @endforeach
                </select>
                <select name="sort" onchange="this.form.submit()" class="input-field w-full rounded-full">
                    @foreach(['newest' => 'Newest', 'price_asc' => 'Price ↑', 'price_desc' => 'Price ↓', 'best_selling' => 'Best Selling'] as $val => $label)
                        <option value="{{ $val }}" @selected($sort === $val)>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-primary w-full text-sm py-2.5">Apply</button>
            </form>
        </aside>

        <div class="flex-1">
            <p class="text-sm text-brand-text/50 mb-6">{{ $products->total() }} watches</p>

            @if($products->isEmpty())
                <div class="card-luxury p-12 text-center text-brand-text/50">No watches found.</div>
            @else
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 md:gap-6">
                    @foreach($products as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
                <div class="mt-10">{{ $products->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
