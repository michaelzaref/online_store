<?php

namespace App\Services;

use App\Models\Product;
use App\Models\RecentlyViewedProduct;
use Illuminate\Support\Facades\Auth;

class ProductService
{
    public function filter(array $filters = [], string $sort = 'newest')
    {
        $query = Product::query()
            ->active()
            ->with(['brand', 'images', 'category']);

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('brand', fn ($b) => $b->where('name', 'like', "%{$search}%"));
            });
        }

        if (! empty($filters['brand'])) {
            $query->whereHas('brand', fn ($q) => $q->where('slug', $filters['brand']));
        }

        if (! empty($filters['category'])) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $filters['category']));
        }

        if (! empty($filters['gender'])) {
            $query->where('gender', $filters['gender']);
        }

        if (! empty($filters['watch_type'])) {
            $query->where('watch_type', $filters['watch_type']);
        }

        if (! empty($filters['case_material'])) {
            $query->where('case_material', $filters['case_material']);
        }

        if (! empty($filters['strap_material'])) {
            $query->where('strap_material', $filters['strap_material']);
        }

        if (! empty($filters['color'])) {
            $query->where('color', $filters['color']);
        }

        if (! empty($filters['min_price'])) {
            $query->whereRaw('COALESCE(sale_price, price) >= ?', [$filters['min_price']]);
        }

        if (! empty($filters['max_price'])) {
            $query->whereRaw('COALESCE(sale_price, price) <= ?', [$filters['max_price']]);
        }

        if (! empty($filters['in_stock'])) {
            $query->inStock();
        }

        match ($sort) {
            'price_asc' => $query->orderByRaw('COALESCE(sale_price, price) ASC'),
            'price_desc' => $query->orderByRaw('COALESCE(sale_price, price) DESC'),
            'best_selling' => $query->orderByDesc('sales_count'),
            'popular' => $query->orderByDesc('views_count'),
            default => $query->latest(),
        };

        return $query;
    }

    public function autocomplete(string $term, int $limit = 8): array
    {
        return Product::active()
            ->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('sku', 'like', "%{$term}%")
                    ->orWhereHas('brand', fn ($b) => $b->where('name', 'like', "%{$term}%"));
            })
            ->with('images')
            ->limit($limit)
            ->get()
            ->map(fn ($p) => [
                'name' => $p->name,
                'slug' => $p->slug,
                'price' => $p->effective_price,
                'image' => $p->images->first()?->path,
                'brand' => $p->brand?->name,
            ])
            ->toArray();
    }

    public function recordView(Product $product): void
    {
        $product->increment('views_count');

        RecentlyViewedProduct::create([
            'user_id' => Auth::id(),
            'session_id' => Auth::check() ? null : session()->getId(),
            'product_id' => $product->id,
            'viewed_at' => now(),
        ]);
    }

    public function getRecentlyViewed(int $limit = 10)
    {
        return RecentlyViewedProduct::query()
            ->when(Auth::check(), fn ($q) => $q->where('user_id', Auth::id()))
            ->when(! Auth::check(), fn ($q) => $q->where('session_id', session()->getId()))
            ->orderByDesc('viewed_at')
            ->with('product.images', 'product.brand')
            ->limit($limit)
            ->get()
            ->pluck('product')
            ->filter()
            ->unique('id');
    }

    public function getRelated(Product $product, int $limit = 4)
    {
        return Product::active()
            ->where('id', '!=', $product->id)
            ->where(function ($q) use ($product) {
                $q->where('brand_id', $product->brand_id)
                    ->orWhere('category_id', $product->category_id);
            })
            ->with(['images', 'brand'])
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    public function getFilterOptions(): array
    {
        return [
            'brands' => \App\Models\Brand::where('is_active', true)->orderBy('name')->get(),
            'categories' => \App\Models\Category::where('is_active', true)->orderBy('name')->get(),
            'watch_types' => ['analog', 'digital', 'smartwatch', 'automatic', 'quartz'],
            'genders' => ['men', 'women', 'unisex'],
            'case_materials' => Product::whereNotNull('case_material')->distinct()->pluck('case_material'),
            'strap_materials' => Product::whereNotNull('strap_material')->distinct()->pluck('strap_material'),
            'colors' => Product::whereNotNull('color')->distinct()->pluck('color'),
        ];
    }
}
