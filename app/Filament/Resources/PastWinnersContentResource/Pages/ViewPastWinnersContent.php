<?php

namespace App\Filament\Resources\PastWinnersContentResource\Pages;

use App\Filament\Resources\PastWinnersContentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPastWinnersContent extends ViewRecord
{
    protected static string $resource = PastWinnersContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
