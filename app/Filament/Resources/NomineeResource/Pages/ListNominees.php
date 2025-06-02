<?php

namespace App\Filament\Resources\NomineeResource\Pages;

use App\Filament\Resources\NomineeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNominees extends ListRecords
{
    protected static string $resource = NomineeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
