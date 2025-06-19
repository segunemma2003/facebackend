<?php
// app/Filament/Resources/PastWinnersContentResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\PastWinnersContentResource\Pages;

class PastWinnersContentResource extends BasePageContentResource
{
    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationLabel = 'Past Winners Page';

    protected static ?int $navigationSort = 4;

    protected static function getPageName(): string
    {
        return 'past_winners';
    }

    protected static function getPageSections(): array
    {
        return [
            'hero' => 'Hero Section',
            'introduction' => 'Introduction',
            'content' => 'Main Content'
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPastWinnersContents::route('/'),
            'create' => Pages\CreatePastWinnersContent::route('/create'),
            'edit' => Pages\EditPastWinnersContent::route('/{record}/edit'),
        ];
    }
}
