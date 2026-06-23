<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartService
{
    public function getCart(): Cart
    {
        $userId = Auth::id();
        $sessionId = session()->getId();

        $cart = Cart::query()
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->when(! $userId, fn ($q) => $q->where('session_id', $sessionId))
            ->with(['items.product.images', 'coupon'])
            ->first();

        if (! $cart) {
            $cart = Cart::create([
                'user_id' => $userId,
                'session_id' => $userId ? null : $sessionId,
            ]);
            $cart->load(['items.product.images', 'coupon']);
        }

        return $cart;
    }

    public function add(Product $product, int $quantity = 1): Cart
    {
        $cart = $this->getCart();
        $quantity = max(1, min($quantity, $product->stock_quantity));

        $item = $cart->items()->where('product_id', $product->id)->first();

        if ($item) {
            $newQty = min($item->quantity + $quantity, $product->stock_quantity);
            $item->update(['quantity' => $newQty, 'price' => $product->effective_price]);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $product->effective_price,
            ]);
        }

        return $cart->fresh(['items.product.images', 'coupon']);
    }

    public function update(CartItem $item, int $quantity): Cart
    {
        if ($quantity <= 0) {
            $item->delete();
        } else {
            $quantity = min($quantity, $item->product->stock_quantity);
            $item->update(['quantity' => $quantity]);
        }

        return $this->getCart();
    }

    public function remove(CartItem $item): Cart
    {
        $item->delete();

        return $this->getCart();
    }

    public function applyCoupon(string $code): array
    {
        $cart = $this->getCart();
        $coupon = Coupon::where('code', strtoupper($code))->first();

        if (! $coupon || ! $coupon->isValid($cart->subtotal())) {
            return ['success' => false, 'message' => 'Invalid or expired coupon code.'];
        }

        $cart->update(['coupon_id' => $coupon->id]);

        return ['success' => true, 'message' => 'Coupon applied successfully.', 'cart' => $this->getCart()];
    }

    public function removeCoupon(): Cart
    {
        $cart = $this->getCart();
        $cart->update(['coupon_id' => null]);

        return $this->getCart();
    }

    public function totals(): array
    {
        $cart = $this->getCart();
        $subtotal = $cart->subtotal();
        $shipping = 0;
        $discount = 0;

        if ($cart->coupon) {
            $discount = $cart->coupon->calculateDiscount($subtotal, $shipping);
        }

        $taxRate = config('store.tax_rate', 0);
        $taxable = max(0, $subtotal - $discount);
        $tax = round($taxable * ($taxRate / 100), 2);
        $total = max(0, $subtotal - $discount + $shipping + $tax);

        return compact('subtotal', 'shipping', 'discount', 'tax', 'total');
    }

    public function mergeGuestCart(): void
    {
        if (! Auth::check()) {
            return;
        }

        $sessionId = session()->getId();
        $guestCart = Cart::where('session_id', $sessionId)->with('items')->first();

        if (! $guestCart || $guestCart->items->isEmpty()) {
            return;
        }

        $userCart = $this->getCart();

        foreach ($guestCart->items as $item) {
            $existing = $userCart->items()->where('product_id', $item->product_id)->first();
            if ($existing) {
                $existing->update(['quantity' => $existing->quantity + $item->quantity]);
            } else {
                $userCart->items()->create($item->only(['product_id', 'quantity', 'price']));
            }
        }

        $guestCart->delete();
    }

    public function clear(): void
    {
        $cart = $this->getCart();
        $cart->items()->delete();
        $cart->update(['coupon_id' => null]);
    }
}
