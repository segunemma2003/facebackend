<?php

namespace App\Filament\Resources\HomeSettingsResource\Pages;

use App\Filament\Resources\HomeSettingsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHomeSettings extends ListRecords
{
    protected static string $resource = HomeSettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
