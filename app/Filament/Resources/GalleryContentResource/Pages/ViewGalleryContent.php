<?php

namespace App\Filament\Resources\GalleryContentResource\Pages;

use App\Filament\Resources\GalleryContentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewGalleryContent extends ViewRecord
{
    protected static string $resource = GalleryContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
