<?php

namespace App\Filament\Resources\PastWinnersContentResource\Pages;

use App\Filament\Resources\PastWinnersContentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPastWinnersContents extends ListRecords
{
    protected static string $resource = PastWinnersContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
