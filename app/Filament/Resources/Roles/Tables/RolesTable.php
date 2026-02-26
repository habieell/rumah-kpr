<?php

namespace App\Filament\Resources\Roles\Tables;

use Filament\Actions\{BulkActionGroup, DeleteBulkAction, EditAction, ForceDeleteBulkAction, RestoreBulkAction};
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
// use Filament\Tables\Filters\TrashedFilter;

class RolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
            ])
            ->filters([
                // TrashedFilter::make(),
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
