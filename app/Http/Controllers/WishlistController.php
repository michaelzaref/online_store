<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $items = Wishlist::where('user_id', $request->user()->id)
            ->with(['product.images', 'product.brand'])
            ->latest()
            ->get();

        return view('store.wishlist.index', compact('items'));
    }

    public function toggle(Request $request, Product $product)
    {
        $wishlist = Wishlist::where('user_id', $request->user()->id)
            ->where('product_id', $product->id)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            $message = 'Removed from wishlist.';
            $added = false;
        } else {
            Wishlist::create([
                'user_id' => $request->user()->id,
                'product_id' => $product->id,
            ]);
            $message = 'Added to wishlist.';
            $added = true;
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => $message, 'added' => $added]);
        }

        return back()->with('success', $message);
    }
}
