<?php

namespace App\Filament\Resources\Houses\Tables;

use Filament\Actions\{BulkActionGroup, DeleteBulkAction, EditAction, ForceDeleteBulkAction, RestoreBulkAction};
use Filament\Tables\Table;
use Filament\Tables\Columns\{ImageColumn, TextColumn};
use Filament\Tables\Filters\TrashedFilter;

class HousesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail'),

                TextColumn::make('name')
                    ->searchable(),

                TextColumn::make('category.name'),
                TextColumn::make('city.name'),

                TextColumn::make('price')
                    ->label('Price')
                    ->formatStateUsing(fn($state) => 'IDR. ' . number_format((int) $state, 0, ',', '.')),


            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
