<?php

namespace App\Filament\Resources\CategoriesContentResource\Pages;

use App\Filament\Resources\CategoriesContentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategoriesContent extends EditRecord
{
    protected static string $resource = CategoriesContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
