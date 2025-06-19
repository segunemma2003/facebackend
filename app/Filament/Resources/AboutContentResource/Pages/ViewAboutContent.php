<?php

namespace App\Filament\Resources\AboutContentResource\Pages;

use App\Filament\Resources\AboutContentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAboutContent extends ViewRecord
{
    protected static string $resource = AboutContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
