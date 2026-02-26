<?php

namespace App\Filament\Resources\Interests\Pages;

use App\Filament\Resources\Interests\InterestResource;
use Filament\Actions\{DeleteAction, ForceDeleteAction, RestoreAction};
use Filament\Resources\Pages\EditRecord;

class EditInterest extends EditRecord
{
    protected static string $resource = InterestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }
}
