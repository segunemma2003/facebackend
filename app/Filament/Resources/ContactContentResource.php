<?php
// app/Filament/Resources/ContactContentResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactContentResource\Pages;

class ContactContentResource extends BasePageContentResource
{
    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'Contact Page';

    protected static ?int $navigationSort = 8;

    protected static function getPageName(): string
    {
        return 'contact';
    }

    protected static function getPageSections(): array
    {
        return [
            'hero' => 'Hero Section',
            'contact_form' => 'Contact Form',
            'contact_info' => 'Contact Information',
            'map' => 'Map Section'
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactContents::route('/'),
            'create' => Pages\CreateContactContent::route('/create'),
            'edit' => Pages\EditContactContent::route('/{record}/edit'),
        ];
    }
}
