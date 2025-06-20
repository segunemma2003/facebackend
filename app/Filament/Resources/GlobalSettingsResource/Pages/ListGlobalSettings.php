<?php

namespace App\Filament\Resources\GlobalSettingsResource\Pages;

use App\Filament\Resources\GlobalSettingsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGlobalSettings extends ListRecords
{
    protected static string $resource = GlobalSettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
