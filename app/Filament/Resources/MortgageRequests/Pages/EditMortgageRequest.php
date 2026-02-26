<?php

namespace App\Filament\Resources\MortgageRequests\Pages;

use App\Filament\Resources\MortgageRequests\MortgageRequestResource;
use Filament\Actions\{DeleteAction, ForceDeleteAction, RestoreAction};
use Filament\Resources\Pages\EditRecord;

class EditMortgageRequest extends EditRecord
{
    protected static string $resource = MortgageRequestResource::class;

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
