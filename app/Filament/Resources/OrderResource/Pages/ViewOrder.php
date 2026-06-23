<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions;
use Filament\Forms;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('updateStatus')
                ->label('Update Status')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->form([
                    Forms\Components\Select::make('status')
                        ->options(OrderResource::statusOptions())
                        ->required()
                        ->default(fn (Order $record): string => $record->status),
                    Forms\Components\TextInput::make('tracking_number')
                        ->maxLength(255)
                        ->default(fn (Order $record): ?string => $record->tracking_number),
                    Forms\Components\TextInput::make('shipping_carrier')
                        ->maxLength(255)
                        ->default(fn (Order $record): ?string => $record->shipping_carrier),
                    Forms\Components\Textarea::make('comment')
                        ->rows(2),
                ])
                ->action(function (Order $record, array $data): void {
                    OrderResource::applyStatusUpdate($record, $data);

                    Notification::make()
                        ->title('Order status updated')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Order Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('order_number')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'gray',
                                'paid' => 'info',
                                'processing' => 'warning',
                                'shipped' => 'primary',
                                'delivered' => 'success',
                                'cancelled', 'refunded' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                        Infolists\Components\TextEntry::make('payment_status'),
                        Infolists\Components\TextEntry::make('payment_method'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->dateTime(),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Customer')
                    ->schema([
                        Infolists\Components\TextEntry::make('customerName')
                            ->label('Name')
                            ->getStateUsing(fn (Order $record): string => $record->customerName()),
                        Infolists\Components\TextEntry::make('customerEmail')
                            ->label('Email')
                            ->getStateUsing(fn (Order $record): string => $record->customerEmail()),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Shipping')
                    ->schema([
                        Infolists\Components\TextEntry::make('shippingMethod.name')
                            ->label('Method'),
                        Infolists\Components\TextEntry::make('shipping_carrier')
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('tracking_number')
                            ->placeholder('—')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('shipped_at')
                            ->dateTime()
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('delivered_at')
                            ->dateTime()
                            ->placeholder('—'),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Order Items')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('items')
                            ->schema([
                                Infolists\Components\TextEntry::make('product_name')
                                    ->label('Product'),
                                Infolists\Components\TextEntry::make('product_sku')
                                    ->label('SKU'),
                                Infolists\Components\TextEntry::make('quantity'),
                                Infolists\Components\TextEntry::make('unit_price')
                                    ->money('USD'),
                                Infolists\Components\TextEntry::make('total_price')
                                    ->money('USD'),
                            ])
                            ->columns(5),
                    ]),

                Infolists\Components\Section::make('Totals')
                    ->schema([
                        Infolists\Components\TextEntry::make('subtotal')
                            ->money('USD'),
                        Infolists\Components\TextEntry::make('discount_amount')
                            ->money('USD'),
                        Infolists\Components\TextEntry::make('shipping_amount')
                            ->money('USD'),
                        Infolists\Components\TextEntry::make('tax_amount')
                            ->money('USD'),
                        Infolists\Components\TextEntry::make('total')
                            ->money('USD')
                            ->weight('bold'),
                        Infolists\Components\TextEntry::make('coupon_code')
                            ->placeholder('—'),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Status History')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('statusHistory')
                            ->schema([
                                Infolists\Components\TextEntry::make('status')
                                    ->badge(),
                                Infolists\Components\TextEntry::make('comment')
                                    ->placeholder('—'),
                                Infolists\Components\TextEntry::make('user.name')
                                    ->label('Updated by')
                                    ->placeholder('System'),
                                Infolists\Components\TextEntry::make('created_at')
                                    ->dateTime(),
                            ])
                            ->columns(4),
                    ])
                    ->collapsed(),
            ]);
    }
}
