<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Models\User;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewCustomer extends ViewRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Customer Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('name'),
                        Infolists\Components\TextEntry::make('email')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('phone')
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('loyalty_points'),
                        Infolists\Components\TextEntry::make('email_verified_at')
                            ->dateTime()
                            ->placeholder('Not verified'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->dateTime()
                            ->label('Registered'),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Purchase Summary')
                    ->schema([
                        Infolists\Components\TextEntry::make('orders_count')
                            ->label('Total Orders')
                            ->getStateUsing(fn (User $record): int => $record->orders()->count()),
                        Infolists\Components\TextEntry::make('total_spent')
                            ->label('Total Spent')
                            ->getStateUsing(fn (User $record): float => $record->totalSpent())
                            ->money('USD'),
                        Infolists\Components\TextEntry::make('reviews_count')
                            ->label('Reviews')
                            ->getStateUsing(fn (User $record): int => $record->reviews()->count()),
                    ])
                    ->columns(3),
            ]);
    }
}
