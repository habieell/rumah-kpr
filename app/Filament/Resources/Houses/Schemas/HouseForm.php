<?php

namespace App\Filament\Resources\Houses\Schemas;

use App\Models\Facility;
use Filament\Forms\Components\{FileUpload, Repeater, Select, TextInput, Textarea};
use Filament\Support\RawJs;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;

class HouseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make('Details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('price')
                            ->required()
                            ->prefix('IDR')
                            ->inputMode('numeric')
                            ->mask(RawJs::make("\$money(\$input, ',', '.', 0)"))
                            ->stripCharacters(['.', ',', 'IDR', ' '])
                            ->dehydrateStateUsing(fn($state) => (int) preg_replace('/\D+/', '', (string) $state)),

                        Select::make('certificate')
                            ->options([
                                'SHM' => 'SHM',
                                'SHGB' => 'SHGB',
                                'Patches' => 'Patches',
                            ])
                            ->required(),

                        FileUpload::make('thumbnail')
                            ->image()
                            ->required(),

                        Repeater::make('photos')
                            ->relationship('photos')
                            ->schema([
                                FileUpload::make('photo')
                                    ->required(),
                            ]),

                        Repeater::make('facilities')
                            ->relationship('facilities')
                            ->schema([
                                Select::make('facility_id')
                                    ->label('Facility')
                                    ->options(Facility::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->required(),
                            ]),

                    ]),

                Fieldset::make('Additional')
                    ->schema([
                        Textarea::make('about')
                            ->required(),

                        Select::make('city_id')
                            ->relationship('city', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('electric')
                            ->required()
                            ->numeric()
                            ->prefix('Watts'),

                        TextInput::make('land_area')
                            ->required()
                            ->numeric()
                            ->prefix('m²'),

                        TextInput::make('building_area')
                            ->required()
                            ->numeric()
                            ->prefix('m²'),

                        TextInput::make('bedroom')
                            ->required()
                            ->numeric()
                            ->prefix('Unit'),

                        TextInput::make('bathroom')
                            ->required()
                            ->numeric()
                            ->prefix('Unit'),
                    ]),
            ]);
    }
}
