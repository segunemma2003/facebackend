<?php

namespace App\Filament\Resources\GeneralGlobalSettingsResource\Pages;

use App\Filament\Resources\GeneralGlobalSettingsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGeneralGlobalSettings extends ListRecords
{
    protected static string $resource = GeneralGlobalSettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
