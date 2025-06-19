<?php
// app/Filament/Resources/NomineesContentResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\NomineesContentResource\Pages;

class NomineesContentResource extends BasePageContentResource
{
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Current Nominees Page';

    protected static ?int $navigationSort = 2;

    protected static function getPageName(): string
    {
        return 'nominees';
    }

    protected static function getPageSections(): array
    {
        return [
            'hero' => 'Hero Section',
            'filters' => 'Filter Section',
            'content' => 'Main Content',
            'voting_info' => 'Voting Information'
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNomineesContents::route('/'),
            'create' => Pages\CreateNomineesContent::route('/create'),
            'edit' => Pages\EditNomineesContent::route('/{record}/edit'),
        ];
    }
}
