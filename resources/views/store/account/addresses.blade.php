@extends('layouts.store')

@section('title', 'My Addresses')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="font-display text-3xl font-bold mb-8">Saved Addresses</h1>
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        @include('store.account.partials.sidebar')
        <div class="lg:col-span-3 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($addresses as $address)
                    <div class="bg-white border border-brand-text/10 rounded-lg p-4">
                        <div class="flex justify-between mb-2">
                            <span class="font-medium">{{ $address->label }}</span>
                            @if($address->is_default)<span class="text-xs bg-brand-accent/10 text-brand-accent px-2 py-0.5 rounded">Default</span>@endif
                        </div>
                        <p class="text-sm text-brand-text/70">{{ $address->fullName() }}</p>
                        <p class="text-sm text-brand-text/70">{{ $address->address_line_1 }}</p>
                        <p class="text-sm text-brand-text/70">{{ $address->city }}, {{ $address->postal_code }}</p>
                        <form action="{{ route('account.addresses.destroy', $address) }}" method="POST" class="mt-3">
                            @csrf @method('DELETE')
                            <button class="text-red-600 text-sm hover:underline">Delete</button>
                        </form>
                    </div>
                @endforeach
            </div>

            <form action="{{ route('account.addresses.store') }}" method="POST" class="bg-white border border-brand-text/10 rounded-lg p-6">
                @csrf
                <h3 class="font-semibold mb-4">Add New Address</h3>
                <div class="grid grid-cols-2 gap-4">
                    <input type="text" name="label" placeholder="Label (Home, Work)" required class="border border-brand-text/15 rounded px-3 py-2">
                    <input type="text" name="first_name" placeholder="First Name" required class="border border-brand-text/15 rounded px-3 py-2">
                    <input type="text" name="last_name" placeholder="Last Name" required class="border border-brand-text/15 rounded px-3 py-2">
                    <input type="tel" name="phone" placeholder="Phone" class="border border-brand-text/15 rounded px-3 py-2">
                    <input type="text" name="address_line_1" placeholder="Address" required class="border border-brand-text/15 rounded px-3 py-2 col-span-2">
                    <input type="text" name="city" placeholder="City" required class="border border-brand-text/15 rounded px-3 py-2">
                    <input type="text" name="postal_code" placeholder="Postal Code" required class="border border-brand-text/15 rounded px-3 py-2">
                    <label class="flex items-center gap-2 col-span-2 text-sm"><input type="checkbox" name="is_default" value="1"> Set as default</label>
                </div>
                <button type="submit" class="mt-4 bg-brand-primary text-white px-6 py-2 rounded text-sm hover:bg-brand-accent transition">Save Address</button>
            </form>
        </div>
    </div>
</div>
@endsection
