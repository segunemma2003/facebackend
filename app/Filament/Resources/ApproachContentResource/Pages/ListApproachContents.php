<?php

namespace App\Filament\Resources\ApproachContentResource\Pages;

use App\Filament\Resources\ApproachContentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListApproachContents extends ListRecords
{
    protected static string $resource = ApproachContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
