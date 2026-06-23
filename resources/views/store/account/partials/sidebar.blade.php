<nav class="bg-white border border-brand-text/10 rounded-lg p-4 space-y-1 text-sm">
    <a href="{{ route('account.dashboard') }}" class="block px-3 py-2 rounded {{ request()->routeIs('account.dashboard') ? 'bg-brand-accent/10 text-brand-accent font-medium' : 'hover:bg-brand-bg' }}">Dashboard</a>
    <a href="{{ route('account.orders') }}" class="block px-3 py-2 rounded {{ request()->routeIs('account.orders*') ? 'bg-brand-accent/10 text-brand-accent font-medium' : 'hover:bg-brand-bg' }}">Orders</a>
    <a href="{{ route('account.addresses') }}" class="block px-3 py-2 rounded {{ request()->routeIs('account.addresses*') ? 'bg-brand-accent/10 text-brand-accent font-medium' : 'hover:bg-brand-bg' }}">Addresses</a>
    <a href="{{ route('account.wishlist') }}" class="block px-3 py-2 rounded {{ request()->routeIs('account.wishlist') ? 'bg-brand-accent/10 text-brand-accent font-medium' : 'hover:bg-brand-bg' }}">Wishlist</a>
    <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded {{ request()->routeIs('profile.*') ? 'bg-brand-accent/10 text-brand-accent font-medium' : 'hover:bg-brand-bg' }}">Profile Settings</a>
</nav>
