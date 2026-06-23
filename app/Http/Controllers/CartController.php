<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(protected CartService $cartService) {}

    public function index()
    {
        $cart = $this->cartService->getCart();
        $totals = $this->cartService->totals();

        return view('store.cart.index', compact('cart', 'totals'));
    }

    public function add(Request $request, Product $product)
    {
        $request->validate(['quantity' => 'integer|min:1']);

        if (! $product->isInStock()) {
            return back()->with('error', 'This product is out of stock.');
        }

        $this->cartService->add($product, $request->integer('quantity', 1));

        if ($request->wantsJson()) {
            $cart = $this->cartService->getCart();

            return response()->json([
                'message' => 'Added to cart.',
                'count' => $cart->itemCount(),
            ]);
        }

        return back()->with('success', 'Added to cart.');
    }

    public function update(Request $request, CartItem $item)
    {
        $this->cartService->update($item, $request->integer('quantity', 1));

        return back()->with('success', 'Cart updated.');
    }

    public function remove(CartItem $item)
    {
        $this->cartService->remove($item);

        return back()->with('success', 'Item removed from cart.');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $result = $this->cartService->applyCoupon($request->code);

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function removeCoupon()
    {
        $this->cartService->removeCoupon();

        return back()->with('success', 'Coupon removed.');
    }
}
