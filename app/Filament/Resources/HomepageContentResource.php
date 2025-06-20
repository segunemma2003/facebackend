<?php


namespace App\Filament\Resources;

use App\Filament\Resources\HomepageContentResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;

class HomepageContentResource extends BasePageContentResource
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Homepage Content';

    protected static ?int $navigationSort = 1;

    protected static function getPageName(): string
    {
        return 'homepage';
    }

    protected static function getPageSections(): array
    {
        return [
            'hero' => 'Hero Section',
            'about' => 'About Section',
            'approach' => 'Approach Section',
            'upcoming_categories' => 'Upcoming Categories',
            'award_ceremony' => 'Award Ceremony Section',
            'past_winners' => 'Past Winners Section',
            'gallery' => 'Gallery Section'
        ];
    }

    // Override the form to add custom key options based on section
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('page')
                    ->default(static::getPageName()),

                Forms\Components\Section::make('Content Details')
                    ->schema([
                        Forms\Components\Select::make('section')
                            ->options(static::getPageSections())
                            ->required()
                            ->live(),

                        Forms\Components\Select::make('key')
                            ->options(fn (Forms\Get $get) => match ($get('section')) {
                                'hero' => [
                                    'main_title' => 'Main Title',
                                    'main_subtitle' => 'Main Subtitle',
                                    'current_highlight_subtitle' => 'Current Highlight Subtitle',
                                    'current_highlight_content' => 'Current Highlight Content',
                                    'background_image' => 'Background Image',
                                    'primary_button_text' => 'Primary Button Text',
                                    'secondary_button_text' => 'Secondary Button Text'
                                ],
                                'about' => [
                                    'title' => 'Section Title',
                                    'logo' => 'FACE Logo',
                                    'content' => 'About Content',
                                    'face_meanings' => 'FACE Meanings (JSON)'
                                ],
                                'approach' => [
                                    'face_sub_title' => 'FACE Sub Title',
                                    'approach_title' => 'Our Approach Title',
                                    'approach_content' => 'Our Approach Content',
                                    'large_image' => 'Large Image',
                                    'image_title' => 'Image Title',
                                    'approach_items' => 'Approach Items (JSON)'
                                ],
                                'upcoming_categories' => [
                                    'subtitle' => 'Section Subtitle',
                                    'content' => 'Section Content'
                                ],
                                'award_ceremony' => [
                                    'title' => 'Ceremony Title',
                                    'subtitle' => 'Ceremony Subtitle',
                                    'content' => 'Ceremony Content',
                                    'event_date' => 'Event Date',
                                    'venue' => 'Venue Information',
                                    'ticket_info' => 'Ticket Information (JSON)'
                                ],
                                'past_winners' => [
                                    'title' => 'Section Title',
                                    'subtitle' => 'Section Subtitle'
                                ],
                                'gallery' => [
                                    'title' => 'Section Title',
                                    'subtitle' => 'Section Subtitle'
                                ],
                                default => []
                            })
                            ->required()
                            ->live(),

                        Forms\Components\Select::make('type')
                            ->options([
                                'text' => 'Text',
                                'html' => 'HTML/Rich Text',
                                'image' => 'Image',
                                'json' => 'JSON Data',
                            ])
                            ->required()
                            ->live(),

                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Order of display (lower numbers first)'),

                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                    ]),

                Forms\Components\Section::make('Content')
                    ->schema([
                        // Text content
                        Forms\Components\Textarea::make('content')
                            ->rows(3)
                            ->visible(fn (Forms\Get $get) => $get('type') === 'text')
                            ->required(fn (Forms\Get $get) => $get('type') === 'text'),

                        // HTML content
                        Forms\Components\RichEditor::make('content')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'html')
                            ->required(fn (Forms\Get $get) => $get('type') === 'html'),

                        // Image upload
                        Forms\Components\FileUpload::make('content')
                            ->image()
                            ->directory('homepage')
                            ->visibility('public')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'image')
                            ->required(fn (Forms\Get $get) => $get('type') === 'image'),

                        // JSON content with examples
                        Forms\Components\Textarea::make('content')
                            ->rows(8)
                            ->helperText(fn (Forms\Get $get) => match ($get('key')) {
                                'face_meanings' => 'Format: [{"letter":"F","word":"Focus","description":"..."},...]',
                                'approach_items' => 'Format: [{"title":"...","description":"...","icon":"..."},...]',
                                'ticket_info' => 'Format: [{"type":"Standard","price":"$250","description":"..."},...]',
                                default => 'Enter valid JSON data'
                            })
                            ->visible(fn (Forms\Get $get) => $get('type') === 'json')
                            ->required(fn (Forms\Get $get) => $get('type') === 'json'),

                        // Meta data
                        Forms\Components\KeyValue::make('meta')
                            ->helperText('Additional metadata (alt text, captions, etc.)'),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHomepageContents::route('/'),
            'create' => Pages\CreateHomepageContent::route('/create'),
            'edit' => Pages\EditHomepageContent::route('/{record}/edit'),
        ];
    }
}
