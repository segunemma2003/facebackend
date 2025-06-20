<?php

namespace App\Filament\Resources\GalleryContentResource\Pages;

use App\Filament\Resources\GalleryContentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGalleryContent extends EditRecord
{
    protected static string $resource = GalleryContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}