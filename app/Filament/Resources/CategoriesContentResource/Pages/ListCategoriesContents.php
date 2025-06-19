<?php

namespace App\Filament\Resources\CategoriesContentResource\Pages;

use App\Filament\Resources\CategoriesContentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCategoriesContents extends ListRecords
{
    protected static string $resource = CategoriesContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
