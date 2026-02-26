<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\{BulkActionGroup, DeleteBulkAction, EditAction, ForceDeleteBulkAction, RestoreBulkAction};
use Filament\Tables\Table;
use Filament\Tables\Columns\{ImageColumn, TextColumn};
use Filament\Tables\Filters\TrashedFilter;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo')
                    ->label('')
                    ->circular()
                    ->width(60),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
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
