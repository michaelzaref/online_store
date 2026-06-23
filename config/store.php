<?php

return [
    'name' => env('STORE_NAME', 'Elite Store'),
    'email' => env('STORE_EMAIL', 'support@elite-store.online'),
    'phone' => env('STORE_PHONE', '+20 100 000 0000'),
    'currency' => env('STORE_CURRENCY', 'EGP'),
    'currency_symbol' => env('STORE_CURRENCY_SYMBOL', 'EGP'),
    'tax_rate' => (float) env('STORE_TAX_RATE', 0),
    'low_stock_threshold' => (int) env('STORE_LOW_STOCK_THRESHOLD', 5),
    'products_per_page' => (int) env('STORE_PRODUCTS_PER_PAGE', 12),
    'recently_viewed_limit' => 10,
    'compare_limit' => 4,
    'colors' => [
        'primary' => '#0F0F0F',
        'secondary' => '#D4AF37',
        'background' => '#F8F6F2',
        'text' => '#2B2B2B',
        'accent' => '#0F5132',
    ],
];
