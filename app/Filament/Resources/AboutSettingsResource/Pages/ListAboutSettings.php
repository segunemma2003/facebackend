<?php

namespace App\Filament\Resources\AboutSettingsResource\Pages;

use App\Filament\Resources\AboutSettingsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAboutSettings extends ListRecords
{
    protected static string $resource = AboutSettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
