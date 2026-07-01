<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        View::composer('layouts.store', function ($view) {
            $cartService = app(CartService::class);
            $cart = $cartService->getCart();
            $view->with('cartCount', $cart->itemCount());
            $view->with('compareCount', count(session('compare', [])));
        });
    }
}
