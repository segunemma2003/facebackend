<?php

namespace App\Filament\Resources\FooterSettingsResource\Pages;

use App\Filament\Resources\FooterSettingsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFooterSettings extends EditRecord
{
    protected static string $resource = FooterSettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
