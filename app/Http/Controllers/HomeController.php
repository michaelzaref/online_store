<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Testimonial;
use App\Services\ProductService;

class HomeController extends Controller
{
    public function __construct(protected ProductService $productService) {}

    public function index()
    {
        return view('store.home', [
            'banners' => Banner::where('is_active', true)->where('position', 'hero')->orderBy('sort_order')->get(),
            'featured' => Product::active()->where('is_featured', true)->with(['images', 'brand'])->limit(8)->get(),
            'newArrivals' => Product::active()->where('is_new_arrival', true)->with(['images', 'brand'])->latest()->limit(8)->get(),
            'bestSellers' => Product::active()->where('is_best_seller', true)->with(['images', 'brand'])->orderByDesc('sales_count')->limit(8)->get(),
            'limitedEdition' => Product::active()->where('is_limited_edition', true)->with(['images', 'brand'])->limit(4)->get(),
            'brands' => Brand::where('is_active', true)->orderBy('sort_order')->get(),
            'testimonials' => Testimonial::where('is_active', true)->orderBy('sort_order')->get(),
            'recentlyViewed' => $this->productService->getRecentlyViewed(6),
        ]);
    }
}
