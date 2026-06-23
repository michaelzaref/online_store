<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('store.name')) — Luxury Watches</title>
    <meta name="description" content="@yield('meta_description', 'Precision meets elegance. Premium luxury watches.')">
    @stack('meta')
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=playfair-display:400,500,600,700|inter:300,400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="pb-24 md:pb-0">
    {{-- Header --}}
    <header class="sticky top-0 z-50 bg-brand-bg/90 backdrop-blur-md border-b border-brand-text/5">
        <div class="max-w-7xl mx-auto px-5 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <svg class="w-5 h-5 text-brand-secondary" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.4 5.8 6.3.5-4.8 4.1 1.5 6.2L12 15.9 6.6 18.6l1.5-6.2L3.3 8.3l6.3-.5L12 2z"/></svg>
                <span class="font-display text-lg font-semibold tracking-[0.15em] uppercase text-brand-text">{{ config('store.name') }}</span>
            </a>

            <div class="flex items-center gap-3">
                <a href="{{ route('products.index') }}" class="btn-icon-circle hidden sm:flex" title="Shop">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 4h18M3 8h18M7 12h10"/></svg>
                </a>
                <a href="{{ route('cart.index') }}" class="btn-icon-circle relative" title="Cart">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    @if($cartCount ?? 0)
                        <span class="absolute -top-1 -right-1 w-4 h-4 bg-brand-accent text-white text-[10px] rounded-full flex items-center justify-center font-bold">{{ $cartCount }}</span>
                    @endif
                </a>
                @auth
                    <a href="{{ route('account.dashboard') }}" class="btn-icon-circle-dark hidden sm:flex" title="Account">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-icon-circle-dark hidden sm:flex" title="Login">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    @if(session('success'))
        <div class="bg-brand-accent text-white text-center py-2.5 text-sm font-medium">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-700 text-white text-center py-2.5 text-sm font-medium">{{ session('error') }}</div>
    @endif

    <main>@yield('content')</main>

    {{-- Mobile bottom nav --}}
    <nav class="bottom-nav">
        <a href="{{ route('home') }}" class="bottom-nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        </a>
        <a href="{{ route('products.index') }}" class="bottom-nav-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </a>
        <a href="{{ auth()->check() ? route('account.wishlist') : route('login') }}" class="bottom-nav-item {{ request()->routeIs('*.wishlist*') ? 'active' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
        </a>
        <a href="{{ auth()->check() ? route('account.dashboard') : route('login') }}" class="bottom-nav-item {{ request()->routeIs('account.*') ? 'active' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        </a>
    </nav>

    {{-- Desktop footer --}}
    <footer class="hidden md:block bg-brand-primary text-white/70 mt-20">
        <div class="max-w-7xl mx-auto px-6 py-14 grid grid-cols-4 gap-8">
            <div>
                <p class="font-display text-xl text-brand-secondary tracking-widest uppercase mb-3">{{ config('store.name') }}</p>
                <p class="text-sm leading-relaxed">Tradition shaped through master craftsmanship, precision and timeless elegance.</p>
            </div>
            <div>
                <h4 class="text-white text-xs tracking-widest uppercase mb-4">Shop</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('products.index') }}" class="hover:text-brand-secondary transition">All Watches</a></li>
                    <li><a href="{{ route('products.index', ['sort' => 'newest']) }}" class="hover:text-brand-secondary transition">New Arrivals</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white text-xs tracking-widest uppercase mb-4">Support</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('contact') }}" class="hover:text-brand-secondary transition">Contact</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white text-xs tracking-widest uppercase mb-4">Newsletter</h4>
                <form action="{{ route('newsletter.subscribe') }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="email" name="email" required placeholder="Email" class="flex-1 bg-white/10 border border-white/10 rounded-full px-4 py-2 text-sm text-white placeholder-white/40 focus:outline-none focus:ring-1 focus:ring-brand-secondary">
                    <button type="submit" class="bg-brand-secondary text-brand-primary px-5 py-2 rounded-full text-sm font-semibold">Join</button>
                </form>
            </div>
        </div>
        <div class="border-t border-white/10 text-center py-5 text-xs">&copy; {{ date('Y') }} {{ config('store.name') }}</div>
    </footer>

    @stack('scripts')
</body>
</html>
