<?php

namespace App\Filament\Resources\NomineesContentResource\Pages;

use App\Filament\Resources\NomineesContentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewNomineesContent extends ViewRecord
{
    protected static string $resource = NomineesContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
