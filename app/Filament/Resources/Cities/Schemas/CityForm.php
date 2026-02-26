<?php

namespace App\Filament\Resources\Cities\Schemas;

use Filament\Forms\Components\{FileUpload, TextInput};
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Fieldset;

class CityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make('Details')
                    ->schema([
                        TextInput::make('name')
                            ->maxLength(255)
                            ->required(),

                        FileUpload::make('photo')
                            ->required()
                            ->image(),
                    ]),
            ]);
    }
}
