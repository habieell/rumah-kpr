<?php

namespace App\Filament\Resources\Interests\Tables;

use Filament\Actions\{BulkActionGroup, DeleteBulkAction, EditAction, ForceDeleteBulkAction, RestoreBulkAction};
use Filament\Tables\Table;
use Filament\Tables\Columns\{ImageColumn, TextColumn};
use Filament\Tables\Filters\TrashedFilter;

class InterestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('house.thumbnail')
                    ->label('')
                    ->square()
                    ->width(60),

                TextColumn::make('house.name')
                    ->label('House')
                    ->searchable(),
                TextColumn::make('bank.name'),
                TextColumn::make('interest'),
                TextColumn::make('duration'),
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
