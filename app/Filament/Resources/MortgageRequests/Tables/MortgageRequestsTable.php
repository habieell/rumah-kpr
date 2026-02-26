<?php

namespace App\Filament\Resources\MortgageRequests\Tables;

use App\Models\MortgageRequest;
use Filament\Actions\{Action, BulkActionGroup, DeleteBulkAction, EditAction, ForceDeleteBulkAction, RestoreBulkAction};
use Filament\Tables\Table;
use Filament\Tables\Columns\{ImageColumn, TextColumn};
use Filament\Tables\Filters\TrashedFilter;

class MortgageRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('house.thumbnail'),

                TextColumn::make('customer.name')
                    ->searchable(),

                TextColumn::make('house.name'),

                TextColumn::make('status'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn(MortgageRequest $record) => asset('storage/' . $record->documents))
                    ->openUrlInNewTab(),
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
