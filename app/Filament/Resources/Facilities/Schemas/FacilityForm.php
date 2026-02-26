<?php

namespace App\Filament\Resources\Facilities\Schemas;

use Filament\Forms\Components\{FileUpload, TextInput};
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Fieldset;

class FacilityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make('Details')
                    ->schema([
                        // ...
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
