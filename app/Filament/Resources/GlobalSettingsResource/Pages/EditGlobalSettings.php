<?php

namespace App\Filament\Resources\GlobalSettingsResource\Pages;

use App\Filament\Resources\GlobalSettingsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGlobalSettings extends EditRecord
{
    protected static string $resource = GlobalSettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
