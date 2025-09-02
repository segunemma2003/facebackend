<?php

namespace App\Filament\Resources\AboutSettingsResource\Pages;

use App\Filament\Resources\AboutSettingsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAboutSettings extends EditRecord
{
    protected static string $resource = AboutSettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
