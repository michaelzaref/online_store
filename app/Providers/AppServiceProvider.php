<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Services\CartService;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('layouts.store', function ($view) {
            $cartService = app(CartService::class);
            $cart = $cartService->getCart();
            $view->with('cartCount', $cart->itemCount());
            $view->with('compareCount', count(session('compare', [])));
        });
    }
}
