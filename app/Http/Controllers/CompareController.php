<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    public function index(Request $request)
    {
        $ids = session('compare', []);
        $products = Product::whereIn('id', $ids)->with(['brand', 'images'])->get();

        return view('store.compare', compact('products'));
    }

    public function toggle(Request $request, Product $product)
    {
        $compare = session('compare', []);
        $limit = config('store.compare_limit', 4);

        if (in_array($product->id, $compare)) {
            $compare = array_values(array_diff($compare, [$product->id]));
            $message = 'Removed from comparison.';
        } else {
            if (count($compare) >= $limit) {
                return back()->with('error', "You can compare up to {$limit} watches.");
            }
            $compare[] = $product->id;
            $message = 'Added to comparison.';
        }

        session(['compare' => $compare]);

        if ($request->wantsJson()) {
            return response()->json(['message' => $message, 'count' => count($compare)]);
        }

        return back()->with('success', $message);
    }

    public function clear()
    {
        session()->forget('compare');

        return redirect()->route('compare.index')->with('success', 'Comparison cleared.');
    }
}
