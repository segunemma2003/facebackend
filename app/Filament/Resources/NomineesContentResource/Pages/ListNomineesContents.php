<?php

namespace App\Filament\Resources\NomineesContentResource\Pages;

use App\Filament\Resources\NomineesContentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNomineesContents extends ListRecords
{
    protected static string $resource = NomineesContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
