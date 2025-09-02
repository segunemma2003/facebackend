<?php

namespace App\Filament\Resources\OurApproachResource\Pages;

use App\Filament\Resources\OurApproachResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOurApproach extends ListRecords
{
    protected static string $resource = OurApproachResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
