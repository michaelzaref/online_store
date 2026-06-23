<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $completedStatuses = ['paid', 'processing', 'shipped', 'delivered'];

        $totalRevenue = Order::query()
            ->whereIn('status', $completedStatuses)
            ->sum('total');

        $revenueThisMonth = Order::query()
            ->whereIn('status', $completedStatuses)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        $revenueLastMonth = Order::query()
            ->whereIn('status', $completedStatuses)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('total');

        $revenueChange = $revenueLastMonth > 0
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
            : ($revenueThisMonth > 0 ? 100 : 0);

        $ordersThisMonth = Order::query()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $ordersLastMonth = Order::query()
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        $ordersChange = $ordersLastMonth > 0
            ? round((($ordersThisMonth - $ordersLastMonth) / $ordersLastMonth) * 100, 1)
            : ($ordersThisMonth > 0 ? 100 : 0);

        $customerCount = User::query()->where('is_admin', false)->count();

        $customersWithOrders = User::query()
            ->where('is_admin', false)
            ->has('orders')
            ->count();

        $conversionRate = $customerCount > 0
            ? round(($customersWithOrders / $customerCount) * 100, 1)
            : 0;

        $lowStockCount = Product::query()
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->where('stock_quantity', '>', 0)
            ->count();

        return [
            Stat::make('Total Revenue', '$'.number_format((float) $totalRevenue, 2))
                ->description($revenueChange >= 0 ? "+{$revenueChange}% from last month" : "{$revenueChange}% from last month")
                ->descriptionIcon($revenueChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueChange >= 0 ? 'success' : 'danger')
                ->chart($this->getRevenueSparkline()),

            Stat::make('Orders This Month', number_format($ordersThisMonth))
                ->description($ordersChange >= 0 ? "+{$ordersChange}% from last month" : "{$ordersChange}% from last month")
                ->descriptionIcon($ordersChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color('primary'),

            Stat::make('Customers', number_format($customerCount))
                ->description("{$customersWithOrders} with orders")
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Conversion Rate', "{$conversionRate}%")
                ->description('Customers who placed an order')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('warning'),

            Stat::make('Low Stock Alerts', number_format($lowStockCount))
                ->description('Products below threshold')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($lowStockCount > 0 ? 'danger' : 'success')
                ->url(\App\Filament\Resources\ProductResource::getUrl('index')),
        ];
    }

    protected function getRevenueSparkline(): array
    {
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);

            $data[] = (float) Order::query()
                ->whereIn('status', ['paid', 'processing', 'shipped', 'delivered'])
                ->whereDate('created_at', $date)
                ->sum('total');
        }

        return $data;
    }
}
