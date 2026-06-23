<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();

        return view('store.account.dashboard', [
            'ordersCount' => $user->orders()->count(),
            'wishlistCount' => $user->wishlists()->count(),
            'totalSpent' => $user->totalSpent(),
            'loyaltyPoints' => $user->loyalty_points,
            'recentOrders' => $user->orders()->latest()->limit(5)->get(),
        ]);
    }

    public function orders(Request $request)
    {
        $orders = $request->user()->orders()->with('items')->latest()->paginate(10);

        return view('store.account.orders', compact('orders'));
    }

    public function orderShow(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $order->load(['items.product', 'statusHistory', 'shippingMethod']);

        return view('store.account.order-show', compact('order'));
    }

    public function addresses(Request $request)
    {
        $addresses = $request->user()->addresses()->latest()->get();

        return view('store.account.addresses', compact('addresses'));
    }

    public function storeAddress(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:50',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:2',
            'is_default' => 'boolean',
        ]);

        if ($request->boolean('is_default')) {
            $request->user()->addresses()->update(['is_default' => false]);
        }

        $request->user()->addresses()->create($validated);

        return back()->with('success', 'Address saved.');
    }

    public function destroyAddress(Request $request, Address $address)
    {
        abort_unless($address->user_id === $request->user()->id, 403);
        $address->delete();

        return back()->with('success', 'Address deleted.');
    }

    public function wishlist(Request $request)
    {
        $items = Wishlist::where('user_id', $request->user()->id)
            ->with(['product.images', 'product.brand'])
            ->get();

        return view('store.account.wishlist', compact('items'));
    }
}
