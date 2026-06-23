@extends('layouts.store')

@section('title', 'Wishlist')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="font-display text-3xl font-bold mb-8">My Wishlist</h1>
    @if($items->isEmpty())
        <p class="text-brand-text/60">Your wishlist is empty. <a href="{{ route('products.index') }}" class="text-brand-accent hover:underline">Browse watches</a></p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($items as $item)
                <x-product-card :product="$item->product" />
            @endforeach
        </div>
    @endif
</div>
@endsection
