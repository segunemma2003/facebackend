<?php

namespace App\Filament\Resources\NomineesContentResource\Pages;

use App\Filament\Resources\NomineesContentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNomineesContent extends EditRecord
{
    protected static string $resource = NomineesContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
