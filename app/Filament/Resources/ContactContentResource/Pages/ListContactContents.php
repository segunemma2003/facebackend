<?php

namespace App\Filament\Resources\ContactContentResource\Pages;

use App\Filament\Resources\ContactContentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContactContents extends ListRecords
{
    protected static string $resource = ContactContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
