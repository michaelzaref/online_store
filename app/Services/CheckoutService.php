<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\ShippingMethod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    public function __construct(
        protected CartService $cartService,
    ) {}

    public function createOrder(array $data): Order
    {
        $cart = $this->cartService->getCart();

        if ($cart->items->isEmpty()) {
            throw new \RuntimeException('Your cart is empty.');
        }

        foreach ($cart->items as $item) {
            if ($item->quantity > $item->product->stock_quantity) {
                throw new \RuntimeException("Insufficient stock for {$item->product->name}.");
            }
        }

        $shippingMethod = ShippingMethod::findOrFail($data['shipping_method_id']);
        $totals = $this->calculateTotals($shippingMethod);

        return DB::transaction(function () use ($cart, $data, $shippingMethod, $totals) {
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => Auth::id(),
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => 'cash_on_delivery',
                'payment_gateway' => null,
                'subtotal' => $totals['subtotal'],
                'discount_amount' => $totals['discount'],
                'shipping_amount' => $totals['shipping'],
                'tax_amount' => $totals['tax'],
                'total' => $totals['total'],
                'coupon_id' => $cart->coupon_id,
                'coupon_code' => $cart->coupon?->code,
                'shipping_method_id' => $shippingMethod->id,
                'billing_address' => $data['billing_address'],
                'shipping_address' => $data['shipping_address'],
                'notes' => $data['notes'] ?? null,
                'gift_wrap' => $data['gift_wrap'] ?? false,
                'gift_message' => $data['gift_message'] ?? null,
                'guest_email' => Auth::guest() ? $data['guest_email'] : null,
                'guest_name' => Auth::guest() ? $data['guest_name'] : null,
            ]);

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'product_sku' => $item->product->sku,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->price,
                    'total_price' => $item->price * $item->quantity,
                ]);

                $item->product->decrement('stock_quantity', $item->quantity);
                $item->product->increment('sales_count', $item->quantity);
            }

            if ($cart->coupon) {
                $cart->coupon->increment('used_count');
            }

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => 'pending',
                'comment' => 'Order placed. Cash on delivery.',
                'user_id' => Auth::id(),
            ]);

            $this->cartService->clear();

            return $order->load('items');
        });
    }

    public function calculateTotals(?ShippingMethod $shippingMethod = null): array
    {
        $cart = $this->cartService->getCart();
        $subtotal = $cart->subtotal();
        $shipping = $shippingMethod?->base_rate ?? 0;
        $discount = 0;

        if ($cart->coupon) {
            $discount = $cart->coupon->calculateDiscount($subtotal, (float) $shipping);
        }

        $taxRate = config('store.tax_rate', 0);
        $taxable = max(0, $subtotal - $discount);
        $tax = round($taxable * ($taxRate / 100), 2);
        $total = max(0, $subtotal - $discount + $shipping + $tax);

        return compact('subtotal', 'shipping', 'discount', 'tax', 'total');
    }

    public function markAsPaid(Order $order, string $transactionId): Order
    {
        $order->update([
            'status' => 'paid',
            'payment_status' => 'paid',
            'payment_transaction_id' => $transactionId,
            'paid_at' => now(),
        ]);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => 'paid',
            'comment' => 'Payment received.',
        ]);

        if ($order->user) {
            $points = (int) floor($order->total / 10);
            $order->user->increment('loyalty_points', $points);
        }

        return $order;
    }
}
