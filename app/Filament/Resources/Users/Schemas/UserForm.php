<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\{FileUpload, Select, TextInput};
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->maxLength(255)
                    ->required(),

                TextInput::make('phone')
                    ->maxLength(255)
                    ->required(),

                TextInput::make('email')
                    ->maxLength(255)
                    ->email()
                    ->required(),

                TextInput::make('password')
                    ->helperText('Minimum 9 characters')
                    ->password()
                    ->required()
                    ->minLength(9)
                    ->maxLength(255),

                Select::make('roles')
                    ->label('Role')
                    ->relationship('roles', 'name')
                    ->required(),

                FileUpload::make('photo')
                    ->required()
                    ->image(),
            ]);
    }
}
