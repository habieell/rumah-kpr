<?php

namespace App\Filament\Resources\Interests;

use Illuminate\Database\Eloquent\{Builder, SoftDeletingScope};
use App\Filament\Resources\Interests\Pages\{CreateInterest, EditInterest, ListInterests};
use App\Filament\Resources\Interests\Schemas\InterestForm;
use App\Filament\Resources\Interests\Tables\InterestsTable;
use App\Models\Interest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class InterestResource extends Resource
{
    protected static ?string $model = Interest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = 'Vendors';

    public static function form(Schema $schema): Schema
    {
        return InterestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InterestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInterests::route('/'),
            'create' => CreateInterest::route('/create'),
            'edit' => EditInterest::route('/{record}/edit'),
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
