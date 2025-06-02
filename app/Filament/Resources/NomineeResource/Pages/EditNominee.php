<?php

namespace App\Filament\Resources\NomineeResource\Pages;

use App\Filament\Resources\NomineeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNominee extends EditRecord
{
    protected static string $resource = NomineeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
