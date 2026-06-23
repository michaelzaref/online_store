<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(protected ProductService $productService) {}

    public function index(Request $request)
    {
        $filters = $request->only([
            'search', 'brand', 'category', 'gender', 'watch_type',
            'case_material', 'strap_material', 'color', 'min_price', 'max_price', 'in_stock',
        ]);
        $sort = $request->get('sort', 'newest');
        $view = $request->get('view', 'grid');

        $products = $this->productService
            ->filter($filters, $sort)
            ->paginate(config('store.products_per_page'))
            ->withQueryString();

        return view('store.products.index', [
            'products' => $products,
            'filters' => $filters,
            'sort' => $sort,
            'view' => $view,
            'filterOptions' => $this->productService->getFilterOptions(),
        ]);
    }

    public function show(string $slug)
    {
        $product = Product::where('slug', $slug)
            ->active()
            ->with(['brand', 'category', 'images', 'tags', 'reviews.user'])
            ->firstOrFail();

        $this->productService->recordView($product);

        return view('store.products.show', [
            'product' => $product,
            'related' => $this->productService->getRelated($product),
            'recentlyViewed' => $this->productService->getRecentlyViewed(6),
        ]);
    }

    public function search(Request $request)
    {
        $term = $request->get('q', '');

        if ($request->wantsJson()) {
            return response()->json($this->productService->autocomplete($term));
        }

        return redirect()->route('products.index', ['search' => $term]);
    }

    public function storeReview(Request $request, Product $product)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'body' => 'required|string|min:10',
        ]);

        Review::create([
            ...$validated,
            'product_id' => $product->id,
            'user_id' => $request->user()->id,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Thank you! Your review has been submitted for moderation.');
    }
}
