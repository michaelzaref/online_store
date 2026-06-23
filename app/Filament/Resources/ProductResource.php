<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Basic Information')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true),
                                Forms\Components\TextInput::make('sku')
                                    ->label('SKU')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                                Forms\Components\Select::make('brand_id')
                                    ->relationship('brand', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),
                                    ]),
                                Forms\Components\Select::make('category_id')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),
                                    ]),
                                Forms\Components\Textarea::make('short_description')
                                    ->rows(2)
                                    ->maxLength(500)
                                    ->columnSpanFull(),
                                Forms\Components\RichEditor::make('description')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Forms\Components\Section::make('Watch Specifications')
                            ->schema([
                                Forms\Components\Select::make('watch_type')
                                    ->options([
                                        'analog' => 'Analog',
                                        'digital' => 'Digital',
                                        'smartwatch' => 'Smartwatch',
                                        'automatic' => 'Automatic',
                                        'quartz' => 'Quartz',
                                    ]),
                                Forms\Components\Select::make('gender')
                                    ->options([
                                        'men' => 'Men',
                                        'women' => 'Women',
                                        'unisex' => 'Unisex',
                                    ]),
                                Forms\Components\Select::make('movement_type')
                                    ->options([
                                        'Automatic' => 'Automatic',
                                        'Manual' => 'Manual',
                                        'Quartz' => 'Quartz',
                                        'Solar' => 'Solar',
                                        'Kinetic' => 'Kinetic',
                                        'Smart' => 'Smart',
                                    ])
                                    ->searchable(),
                                Forms\Components\TextInput::make('case_diameter')
                                    ->numeric()
                                    ->suffix('mm'),
                                Forms\Components\TextInput::make('case_material')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('strap_material')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('water_resistance')
                                    ->maxLength(255)
                                    ->placeholder('e.g. 100m'),
                                Forms\Components\TextInput::make('weight')
                                    ->numeric()
                                    ->suffix('g'),
                                Forms\Components\TextInput::make('glass_type')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('warranty_period')
                                    ->numeric()
                                    ->suffix('months'),
                                Forms\Components\TextInput::make('country_of_manufacture')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('color')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('video_url')
                                    ->url()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Forms\Components\Section::make('Product Images')
                            ->schema([
                                Forms\Components\Repeater::make('images')
                                    ->relationship('images')
                                    ->schema([
                                        Forms\Components\FileUpload::make('path')
                                            ->image()
                                            ->disk('public')
                                            ->directory('products')
                                            ->required(),
                                        Forms\Components\TextInput::make('alt')
                                            ->maxLength(255),
                                        Forms\Components\Toggle::make('is_primary')
                                            ->label('Primary image'),
                                        Forms\Components\TextInput::make('sort_order')
                                            ->numeric()
                                            ->default(0),
                                    ])
                                    ->columns(2)
                                    ->defaultItems(0)
                                    ->reorderableWithButtons()
                                    ->collapsible()
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Section::make('SEO')
                            ->schema([
                                Forms\Components\TextInput::make('meta_title')
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('meta_description')
                                    ->rows(3)
                                    ->maxLength(500)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->collapsed(),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Pricing & Inventory')
                            ->schema([
                                Forms\Components\TextInput::make('price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('$')
                                    ->minValue(0),
                                Forms\Components\TextInput::make('sale_price')
                                    ->numeric()
                                    ->prefix('$')
                                    ->minValue(0),
                                Forms\Components\TextInput::make('cost_price')
                                    ->numeric()
                                    ->prefix('$')
                                    ->minValue(0),
                                Forms\Components\TextInput::make('stock_quantity')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0),
                                Forms\Components\TextInput::make('low_stock_threshold')
                                    ->numeric()
                                    ->default(5)
                                    ->minValue(0)
                                    ->helperText('Alert when stock falls to this level'),
                            ]),

                        Forms\Components\Section::make('Tags')
                            ->schema([
                                Forms\Components\Select::make('tags')
                                    ->relationship('tags', 'name')
                                    ->multiple()
                                    ->preload()
                                    ->searchable()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),
                                    ]),
                            ]),

                        Forms\Components\Section::make('Visibility')
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true),
                                Forms\Components\Toggle::make('is_featured')
                                    ->label('Featured'),
                                Forms\Components\Toggle::make('is_new_arrival')
                                    ->label('New Arrival'),
                                Forms\Components\Toggle::make('is_best_seller')
                                    ->label('Best Seller'),
                                Forms\Components\Toggle::make('is_limited_edition')
                                    ->label('Limited Edition'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('images.path')
                    ->label('Image')
                    ->circular()
                    ->stacked()
                    ->limit(1),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Product $record): ?string => $record->sku),
                Tables\Columns\TextColumn::make('brand.name')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sale_price')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Stock')
                    ->sortable()
                    ->badge()
                    ->color(fn (Product $record): string => match (true) {
                        $record->stock_quantity <= 0 => 'danger',
                        $record->isLowStock() => 'warning',
                        default => 'success',
                    })
                    ->formatStateUsing(fn (Product $record): string => match (true) {
                        $record->stock_quantity <= 0 => 'Out of stock',
                        $record->isLowStock() => "Low ({$record->stock_quantity})",
                        default => (string) $record->stock_quantity,
                    }),
                Tables\Columns\TextColumn::make('sales_count')
                    ->label('Sales')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('brand_id')
                    ->relationship('brand', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('watch_type')
                    ->options([
                        'analog' => 'Analog',
                        'digital' => 'Digital',
                        'smartwatch' => 'Smartwatch',
                        'automatic' => 'Automatic',
                        'quartz' => 'Quartz',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
                Tables\Filters\Filter::make('low_stock')
                    ->label('Low Stock')
                    ->query(fn (Builder $query): Builder => $query
                        ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
                        ->where('stock_quantity', '>', 0)),
                Tables\Filters\Filter::make('out_of_stock')
                    ->label('Out of Stock')
                    ->query(fn (Builder $query): Builder => $query->where('stock_quantity', '<=', 0)),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Product::query()
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->where('stock_quantity', '>', 0)
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Products with low stock';
    }
}
