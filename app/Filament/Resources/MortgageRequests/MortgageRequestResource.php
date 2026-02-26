<?php

namespace App\Filament\Resources\MortgageRequests;

use Illuminate\Database\Eloquent\{Builder, SoftDeletingScope};
use App\Filament\Resources\MortgageRequests\Pages\{CreateMortgageRequest, EditMortgageRequest, ListMortgageRequests};
use App\Filament\Resources\MortgageRequests\RelationManagers\InstallmentsRelationManager;
use App\Filament\Resources\MortgageRequests\Schemas\MortgageRequestForm;
use App\Filament\Resources\MortgageRequests\Tables\MortgageRequestsTable;
use App\Models\MortgageRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class MortgageRequestResource extends Resource
{
    protected static ?string $model = MortgageRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|UnitEnum|null $navigationGroup = 'Transactions';

    public static function form(Schema $schema): Schema
    {
        return MortgageRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MortgageRequestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            InstallmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMortgageRequests::route('/'),
            'create' => CreateMortgageRequest::route('/create'),
            'edit' => EditMortgageRequest::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
