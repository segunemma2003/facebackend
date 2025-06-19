<?php

namespace App\Filament\Resources\ContactContentResource\Pages;

use App\Filament\Resources\ContactContentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewContactContent extends ViewRecord
{
    protected static string $resource = ContactContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
