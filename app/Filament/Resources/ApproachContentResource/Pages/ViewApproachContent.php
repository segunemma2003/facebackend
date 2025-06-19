<?php

namespace App\Filament\Resources\ApproachContentResource\Pages;

use App\Filament\Resources\ApproachContentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewApproachContent extends ViewRecord
{
    protected static string $resource = ApproachContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
