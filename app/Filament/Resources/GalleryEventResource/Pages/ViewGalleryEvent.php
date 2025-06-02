<?php

namespace App\Filament\Resources\GalleryEventResource\Pages;

use App\Filament\Resources\GalleryEventResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewGalleryEvent extends ViewRecord
{
    protected static string $resource = GalleryEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
