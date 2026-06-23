<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ShippingMethod;
use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected CheckoutService $checkoutService,
    ) {}

    public function index()
    {
        $cart = $this->cartService->getCart();

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $shippingMethods = ShippingMethod::where('is_active', true)->get();
        $totals = $this->checkoutService->calculateTotals($shippingMethods->first());

        return view('store.checkout.index', compact('cart', 'shippingMethods', 'totals'));
    }

    public function store(Request $request)
    {
        $rules = [
            'shipping_method_id' => 'required|exists:shipping_methods,id',
            'billing_address' => 'required|array',
            'billing_address.first_name' => 'required|string|max:100',
            'billing_address.last_name' => 'required|string|max:100',
            'billing_address.phone' => 'required|string|max:20',
            'billing_address.address_line_1' => 'required|string|max:255',
            'billing_address.city' => 'required|string|max:100',
            'billing_address.postal_code' => 'required|string|max:20',
            'billing_address.country' => 'required|string|max:2',
            'shipping_address' => 'required|array',
            'shipping_address.first_name' => 'required|string|max:100',
            'shipping_address.last_name' => 'required|string|max:100',
            'shipping_address.phone' => 'required|string|max:20',
            'shipping_address.address_line_1' => 'required|string|max:255',
            'shipping_address.city' => 'required|string|max:100',
            'shipping_address.postal_code' => 'required|string|max:20',
            'shipping_address.country' => 'required|string|max:2',
            'notes' => 'nullable|string|max:500',
            'gift_wrap' => 'boolean',
            'gift_message' => 'nullable|string|max:500',
        ];

        if (! auth()->check()) {
            $rules['guest_name'] = 'required|string|max:255';
            $rules['guest_email'] = 'required|email|max:255';
        }

        $validated = $request->validate($rules);

        try {
            $order = $this->checkoutService->createOrder($validated);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        return redirect()->route('checkout.success', $order)
            ->with('success', 'Order placed! Pay with cash when your order is delivered.');
    }

    public function success(Order $order)
    {
        return view('store.checkout.success', compact('order'));
    }
}
