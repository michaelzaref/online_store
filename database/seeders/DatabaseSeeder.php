<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ShippingMethod;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@elite-store.online',
            'password' => bcrypt('password'),
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'John Customer',
            'email' => 'customer@example.com',
            'password' => bcrypt('password'),
            'is_admin' => false,
            'email_verified_at' => now(),
        ]);

        $brands = collect([
            ['name' => 'Rolex', 'description' => 'Swiss luxury watchmaker since 1905.'],
            ['name' => 'Omega', 'description' => 'Precision timekeeping since 1848.'],
            ['name' => 'TAG Heuer', 'description' => 'Swiss avant-garde since 1860.'],
            ['name' => 'Seiko', 'description' => 'Japanese innovation in horology.'],
            ['name' => 'Casio', 'description' => 'Digital and analog excellence.'],
            ['name' => 'Tissot', 'description' => 'Innovation by tradition since 1853.'],
        ])->map(fn ($b) => Brand::create([...$b, 'slug' => Str::slug($b['name']), 'is_active' => true]));

        $categories = collect([
            'Luxury', 'Sport', 'Dress', 'Diving', 'Smartwatch', 'Vintage',
        ])->map(fn ($name) => Category::create(['name' => $name, 'slug' => Str::slug($name), 'is_active' => true]));

        $watchData = [
            ['name' => 'Submariner Date', 'brand' => 'Rolex', 'category' => 'Diving', 'price' => 450000, 'sale_price' => 425000, 'watch_type' => 'automatic', 'gender' => 'men', 'movement_type' => 'Automatic', 'case_diameter' => 41, 'case_material' => 'Oystersteel', 'strap_material' => 'Oystersteel', 'water_resistance' => '300m', 'is_featured' => true, 'is_best_seller' => true],
            ['name' => 'Speedmaster Moonwatch', 'brand' => 'Omega', 'category' => 'Sport', 'price' => 280000, 'watch_type' => 'automatic', 'gender' => 'men', 'movement_type' => 'Manual-winding', 'case_diameter' => 42, 'case_material' => 'Stainless Steel', 'strap_material' => 'Leather', 'water_resistance' => '50m', 'is_featured' => true, 'is_limited_edition' => true],
            ['name' => 'Carrera Chronograph', 'brand' => 'TAG Heuer', 'category' => 'Sport', 'price' => 185000, 'sale_price' => 165000, 'watch_type' => 'automatic', 'gender' => 'men', 'movement_type' => 'Automatic', 'case_diameter' => 44, 'case_material' => 'Steel', 'strap_material' => 'Rubber', 'water_resistance' => '100m', 'is_new_arrival' => true],
            ['name' => 'Prospex Diver', 'brand' => 'Seiko', 'category' => 'Diving', 'price' => 45000, 'watch_type' => 'automatic', 'gender' => 'men', 'movement_type' => 'Automatic', 'case_diameter' => 45, 'case_material' => 'Stainless Steel', 'strap_material' => 'Silicone', 'water_resistance' => '200m', 'is_best_seller' => true],
            ['name' => 'G-Shock GA-2100', 'brand' => 'Casio', 'category' => 'Sport', 'price' => 8500, 'watch_type' => 'digital', 'gender' => 'unisex', 'movement_type' => 'Quartz', 'case_diameter' => 45.4, 'case_material' => 'Resin', 'strap_material' => 'Resin', 'water_resistance' => '200m', 'is_new_arrival' => true],
            ['name' => 'PRX Powermatic 80', 'brand' => 'Tissot', 'category' => 'Dress', 'price' => 32000, 'sale_price' => 28500, 'watch_type' => 'automatic', 'gender' => 'unisex', 'movement_type' => 'Automatic', 'case_diameter' => 40, 'case_material' => 'Stainless Steel', 'strap_material' => 'Stainless Steel', 'water_resistance' => '100m', 'is_featured' => true],
            ['name' => 'Datejust 36', 'brand' => 'Rolex', 'category' => 'Dress', 'price' => 380000, 'watch_type' => 'automatic', 'gender' => 'women', 'movement_type' => 'Automatic', 'case_diameter' => 36, 'case_material' => 'Gold/Steel', 'strap_material' => 'Jubilee', 'water_resistance' => '100m', 'is_limited_edition' => true],
            ['name' => 'Seamaster Aqua Terra', 'brand' => 'Omega', 'category' => 'Dress', 'price' => 195000, 'watch_type' => 'automatic', 'gender' => 'men', 'movement_type' => 'Co-Axial Automatic', 'case_diameter' => 41, 'case_material' => 'Stainless Steel', 'strap_material' => 'Leather', 'water_resistance' => '150m'],
            ['name' => 'Formula 1', 'brand' => 'TAG Heuer', 'category' => 'Sport', 'price' => 75000, 'watch_type' => 'quartz', 'gender' => 'women', 'movement_type' => 'Quartz', 'case_diameter' => 35, 'case_material' => 'Steel', 'strap_material' => 'Steel', 'water_resistance' => '200m', 'is_best_seller' => true],
            ['name' => 'Presage Cocktail Time', 'brand' => 'Seiko', 'category' => 'Dress', 'price' => 28000, 'watch_type' => 'automatic', 'gender' => 'women', 'movement_type' => 'Automatic', 'case_diameter' => 33.5, 'case_material' => 'Stainless Steel', 'strap_material' => 'Leather', 'water_resistance' => '50m', 'is_new_arrival' => true],
            ['name' => 'Edifice Smart', 'brand' => 'Casio', 'category' => 'Smartwatch', 'price' => 12000, 'watch_type' => 'smartwatch', 'gender' => 'men', 'movement_type' => 'Quartz', 'case_diameter' => 44, 'case_material' => 'Resin/Steel', 'strap_material' => 'Resin', 'water_resistance' => '100m'],
            ['name' => 'Gentleman Powermatic 80', 'brand' => 'Tissot', 'category' => 'Dress', 'price' => 35000, 'watch_type' => 'automatic', 'gender' => 'men', 'movement_type' => 'Automatic', 'case_diameter' => 40, 'case_material' => 'Stainless Steel', 'strap_material' => 'Leather', 'water_resistance' => '100m'],
        ];

        foreach ($watchData as $i => $data) {
            $brand = $brands->firstWhere('name', $data['brand']);
            $category = $categories->firstWhere('name', $data['category']);
            unset($data['brand'], $data['category']);

            $product = Product::create([
                ...$data,
                'slug' => Str::slug($data['name']),
                'sku' => 'ES-'.str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'description' => "Premium {$data['name']} from {$brand->name}. Crafted with exceptional attention to detail.",
                'short_description' => "Luxury {$category->name} timepiece by {$brand->name}.",
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'cost_price' => $data['price'] * 0.6,
                'stock_quantity' => rand(3, 50),
                'low_stock_threshold' => 5,
                'weight' => rand(80, 200),
                'glass_type' => 'Sapphire Crystal',
                'warranty_period' => 2,
                'country_of_manufacture' => in_array($data['name'], ['Prospex Diver', 'Presage Cocktail Time', 'G-Shock GA-2100', 'Edifice Smart']) ? 'Japan' : 'Switzerland',
                'color' => ['Black', 'Silver', 'Blue', 'Gold', 'Green'][rand(0, 4)],
                'is_active' => true,
                'views_count' => rand(100, 5000),
                'sales_count' => rand(10, 500),
            ]);

            ProductImage::create([
                'product_id' => $product->id,
                'path' => 'products/placeholder.jpg',
                'is_primary' => true,
                'sort_order' => 0,
            ]);
        }

        ShippingMethod::insert([
            ['name' => 'Standard Delivery', 'code' => 'standard', 'description' => 'Delivered within 5-7 days (arranged manually)', 'base_rate' => 50, 'estimated_days_min' => 5, 'estimated_days_max' => 7, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Express Delivery', 'code' => 'express', 'description' => 'Delivered within 2-3 days (arranged manually)', 'base_rate' => 120, 'estimated_days_min' => 2, 'estimated_days_max' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Same Day Delivery', 'code' => 'same_day', 'description' => 'Cairo & Giza — arranged manually', 'base_rate' => 200, 'estimated_days_min' => 0, 'estimated_days_max' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        Coupon::create([
            'code' => 'WELCOME10',
            'description' => '10% off your first order',
            'type' => 'percentage',
            'value' => 10,
            'min_order_amount' => 5000,
            'max_uses' => 1000,
            'is_active' => true,
            'expires_at' => now()->addYear(),
        ]);

        Coupon::create([
            'code' => 'FREESHIP',
            'description' => 'Free shipping on any order',
            'type' => 'free_shipping',
            'value' => 0,
            'min_order_amount' => 10000,
            'is_active' => true,
        ]);

        Banner::create([
            'title' => 'Timeless Elegance',
            'subtitle' => 'Discover the world\'s finest luxury watches',
            'image' => 'banners/hero.jpg',
            'link' => '/shop',
            'button_text' => 'Shop Collection',
            'position' => 'hero',
            'is_active' => true,
        ]);

        Testimonial::insert([
            ['customer_name' => 'Ahmed Hassan', 'customer_title' => 'Verified Buyer', 'content' => 'Exceptional quality and fast delivery. My Rolex arrived perfectly packaged.', 'rating' => 5, 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['customer_name' => 'Sarah Mitchell', 'customer_title' => 'Watch Collector', 'content' => 'Best watch store in Egypt. Authentic pieces with great customer service.', 'rating' => 5, 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['customer_name' => 'Omar El-Sayed', 'customer_title' => 'Verified Buyer', 'content' => 'The Omega Speedmaster is stunning. Highly recommend Elite Store!', 'rating' => 5, 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
