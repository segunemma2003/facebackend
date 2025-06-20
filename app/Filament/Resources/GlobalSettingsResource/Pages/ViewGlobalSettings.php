<?php

namespace App\Filament\Resources\GlobalSettingsResource\Pages;

use App\Filament\Resources\GlobalSettingsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewGlobalSettings extends ViewRecord
{
    protected static string $resource = GlobalSettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
