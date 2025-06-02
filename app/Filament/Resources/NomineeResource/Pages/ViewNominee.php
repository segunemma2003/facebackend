<?php

namespace App\Filament\Resources\NomineeResource\Pages;

use App\Filament\Resources\NomineeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewNominee extends ViewRecord
{
    protected static string $resource = NomineeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
