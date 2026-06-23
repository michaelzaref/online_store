<?php

use App\Models\Product;
use Illuminate\Support\Facades\Route;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

Route::get('/sitemap.xml', function () {
    $sitemap = Sitemap::create()
        ->add(Url::create('/')->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)->setPriority(1.0))
        ->add(Url::create('/shop')->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)->setPriority(0.9));

    Product::active()->each(function (Product $product) use ($sitemap) {
        $sitemap->add(
            Url::create('/shop/'.$product->slug)
                ->setLastModificationDate($product->updated_at)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority(0.8)
        );
    });

    return $sitemap->toResponse(request());
});
