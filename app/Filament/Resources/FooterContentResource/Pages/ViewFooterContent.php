<?php

namespace App\Filament\Resources\FooterContentResource\Pages;

use App\Filament\Resources\FooterContentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFooterContent extends ViewRecord
{
    protected static string $resource = FooterContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
