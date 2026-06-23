@extends('layouts.store')

@section('title', 'Compare Watches')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="font-display text-3xl font-bold">Compare Watches</h1>
        @if($products->isNotEmpty())
            <form action="{{ route('compare.clear') }}" method="POST">
                @csrf @method('DELETE')
                <button class="text-red-600 text-sm hover:underline">Clear All</button>
            </form>
        @endif
    </div>

    @if($products->isEmpty())
        <p class="text-brand-text/60">No watches to compare. <a href="{{ route('products.index') }}" class="text-brand-accent hover:underline">Browse collection</a></p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full bg-white border border-brand-text/10 rounded-lg text-sm">
                <thead>
                    <tr class="border-b border-brand-text/10">
                        <th class="p-4 text-left text-brand-text/60 font-medium w-40">Feature</th>
                        @foreach($products as $product)
                            <th class="p-4 text-center min-w-48">
                                <a href="{{ route('products.show', $product->slug) }}" class="font-medium hover:text-brand-accent">{{ $product->name }}</a>
                                <p class="text-brand-text/60 text-xs mt-1">{{ $product->brand?->name }}</p>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach([
                        'Price' => fn($p) => number_format($p->effective_price).' EGP',
                        'Movement' => fn($p) => $p->movement_type,
                        'Type' => fn($p) => ucfirst($p->watch_type ?? '-'),
                        'Case Diameter' => fn($p) => $p->case_diameter ? $p->case_diameter.' mm' : '-',
                        'Case Material' => fn($p) => $p->case_material ?? '-',
                        'Strap' => fn($p) => $p->strap_material ?? '-',
                        'Water Resistance' => fn($p) => $p->water_resistance ?? '-',
                        'Weight' => fn($p) => $p->weight ? $p->weight.' g' : '-',
                    ] as $label => $getter)
                        <tr class="border-b border-brand-text/10">
                            <td class="p-4 text-brand-text/60">{{ $label }}</td>
                            @foreach($products as $product)
                                <td class="p-4 text-center">{{ $getter($product) }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                    <tr>
                        <td class="p-4"></td>
                        @foreach($products as $product)
                            <td class="p-4 text-center">
                                @if($product->isInStock())
                                    <form action="{{ route('cart.add', $product) }}" method="POST">
                                        @csrf
                                        <button class="bg-brand-primary text-white px-4 py-2 rounded text-sm hover:bg-brand-accent transition">Add to Cart</button>
                                    </form>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
