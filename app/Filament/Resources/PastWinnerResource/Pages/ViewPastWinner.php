<?php

namespace App\Filament\Resources\PastWinnerResource\Pages;

use App\Filament\Resources\PastWinnerResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPastWinner extends ViewRecord
{
    protected static string $resource = PastWinnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
