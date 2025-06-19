<?php

namespace App\Filament\Resources\HomepageContentResource\Pages;

use App\Filament\Resources\HomepageContentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewHomepageContent extends ViewRecord
{
    protected static string $resource = HomepageContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
