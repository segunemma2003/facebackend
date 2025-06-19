<?php

namespace App\Filament\Resources\CategoriesContentResource\Pages;

use App\Filament\Resources\CategoriesContentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCategoriesContent extends ViewRecord
{
    protected static string $resource = CategoriesContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
