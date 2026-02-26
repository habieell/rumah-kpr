<?php

namespace App\Filament\Resources\Banks\Tables;

use Filament\Actions\{BulkActionGroup, DeleteBulkAction, EditAction, ForceDeleteBulkAction, RestoreBulkAction, ViewAction};
use Filament\Tables\Table;
use Filament\Tables\Columns\{ImageColumn, TextColumn};
use Filament\Tables\Filters\TrashedFilter;

class BanksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo'),
                TextColumn::make('name')
                    ->searchable(),

            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                ViewAction::make(),
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
