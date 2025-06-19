<?php

namespace App\Filament\Resources\HomepageContentResource\Pages;

use App\Filament\Resources\HomepageContentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHomepageContent extends EditRecord
{
    protected static string $resource = HomepageContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
