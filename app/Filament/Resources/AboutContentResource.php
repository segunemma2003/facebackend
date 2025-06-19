<?php
// app/Filament/Resources/AboutContentResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\AboutContentResource\Pages;

class AboutContentResource extends BasePageContentResource
{
    protected static ?string $navigationIcon = 'heroicon-o-information-circle';

    protected static ?string $navigationLabel = 'About Page';

    protected static ?int $navigationSort = 7;

    protected static function getPageName(): string
    {
        return 'about';
    }

    protected static function getPageSections(): array
    {
        return [
            'hero' => 'Hero Section',
            'story' => 'Our Story',
            'team' => 'Team Section',
            'mission' => 'Mission & Vision',
            'contact_info' => 'Contact Information'
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAboutContents::route('/'),
            'create' => Pages\CreateAboutContent::route('/create'),
            'edit' => Pages\EditAboutContent::route('/{record}/edit'),
        ];
    }
}
