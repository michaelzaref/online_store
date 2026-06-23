<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'order_number';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Order Details')
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->disabled(),
                        Forms\Components\Select::make('status')
                            ->options(self::statusOptions())
                            ->required(),
                        Forms\Components\TextInput::make('payment_status')
                            ->maxLength(255)
                            ->helperText('Set to "paid" when cash is collected on delivery.'),
                        Forms\Components\TextInput::make('payment_method')
                            ->default('cash_on_delivery')
                            ->disabled(),
                        Forms\Components\TextInput::make('payment_transaction_id')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Customer')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->disabled(),
                        Forms\Components\TextInput::make('guest_name')
                            ->disabled(),
                        Forms\Components\TextInput::make('guest_email')
                            ->disabled(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Delivery')
                    ->description('Manage delivery manually. Update status and add notes as needed.')
                    ->schema([
                        Forms\Components\Select::make('shipping_method_id')
                            ->relationship('shippingMethod', 'name')
                            ->disabled(),
                        Forms\Components\TextInput::make('shipping_carrier')
                            ->label('Delivery Notes / Courier')
                            ->placeholder('e.g. Own driver, Aramex, customer pickup')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('tracking_number')
                            ->label('Reference / Tracking (optional)')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Totals')
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->numeric()
                            ->prefix('$')
                            ->disabled(),
                        Forms\Components\TextInput::make('discount_amount')
                            ->numeric()
                            ->prefix('$')
                            ->disabled(),
                        Forms\Components\TextInput::make('shipping_amount')
                            ->numeric()
                            ->prefix('$')
                            ->disabled(),
                        Forms\Components\TextInput::make('tax_amount')
                            ->numeric()
                            ->prefix('$')
                            ->disabled(),
                        Forms\Components\TextInput::make('total')
                            ->numeric()
                            ->prefix('$')
                            ->disabled(),
                        Forms\Components\TextInput::make('coupon_code')
                            ->disabled(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Addresses')
                    ->schema([
                        Forms\Components\KeyValue::make('billing_address')
                            ->disabled()
                            ->columnSpanFull(),
                        Forms\Components\KeyValue::make('shipping_address')
                            ->disabled()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('gift_wrap')
                            ->disabled(),
                        Forms\Components\Textarea::make('gift_message')
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('customerName')
                    ->label('Customer')
                    ->getStateUsing(fn (Order $record): string => $record->customerName())
                    ->searchable(['guest_name'])
                    ->description(fn (Order $record): ?string => $record->customerEmail() ?: null),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'paid' => 'info',
                        'processing' => 'warning',
                        'shipped' => 'primary',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        'refunded' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('payment_status')
                    ->badge()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('total')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('items_count')
                    ->counts('items')
                    ->label('Items'),
                Tables\Columns\TextColumn::make('tracking_number')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(self::statusOptions())
                    ->multiple(),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['until'], fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('updateStatus')
                    ->label('Update Status')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->options(self::statusOptions())
                            ->required()
                            ->default(fn (Order $record): string => $record->status),
                        Forms\Components\TextInput::make('tracking_number')
                            ->label('Reference / Tracking (optional)')
                            ->maxLength(255)
                            ->default(fn (Order $record): ?string => $record->tracking_number),
                        Forms\Components\TextInput::make('shipping_carrier')
                            ->label('Delivery Notes / Courier')
                            ->maxLength(255)
                            ->default(fn (Order $record): ?string => $record->shipping_carrier),
                        Forms\Components\Textarea::make('comment')
                            ->rows(2)
                            ->placeholder('Optional note for status history'),
                    ])
                    ->action(function (Order $record, array $data): void {
                        self::applyStatusUpdate($record, $data);

                        Notification::make()
                            ->title('Order status updated')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([]);
    }

    public static function applyStatusUpdate(Order $record, array $data): void
    {
        $status = $data['status'];

        $updates = [
            'status' => $status,
        ];

        if (isset($data['tracking_number'])) {
            $updates['tracking_number'] = $data['tracking_number'];
        }

        if (isset($data['shipping_carrier'])) {
            $updates['shipping_carrier'] = $data['shipping_carrier'];
        }

        if ($status === 'paid' && ! $record->paid_at) {
            $updates['paid_at'] = now();
        }

        if ($status === 'shipped' && ! $record->shipped_at) {
            $updates['shipped_at'] = now();
        }

        if ($status === 'delivered' && ! $record->delivered_at) {
            $updates['delivered_at'] = now();
        }

        if ($status === 'delivered' && $record->payment_method === 'cash_on_delivery') {
            $updates['payment_status'] = 'paid';
            if (! $record->paid_at) {
                $updates['paid_at'] = now();
            }
        }

        $wasPendingPayment = $record->payment_status !== 'paid';
        $record->update($updates);

        if ($status === 'delivered' && $record->user && $wasPendingPayment && $record->payment_method === 'cash_on_delivery') {
            $points = (int) floor($record->total / 10);
            $record->user->increment('loyalty_points', $points);
        }

        OrderStatusHistory::create([
            'order_id' => $record->id,
            'status' => $status,
            'comment' => $data['comment'] ?? null,
            'user_id' => auth()->id(),
        ]);
    }

    public static function statusOptions(): array
    {
        return [
            'pending' => 'Pending',
            'paid' => 'Paid',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
            'refunded' => 'Refunded',
        ];
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Order::query()->where('status', 'pending')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
