<?php


namespace App\Filament\Resources;

use App\Filament\Resources\PageContentResource\Pages;
use App\Models\PageContent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PageContentResource extends Resource
{
    protected static ?string $model = PageContent::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'All Page Content';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = 10; // Put it at the end

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Content Details')
                    ->schema([
                        Forms\Components\Select::make('page')
                            ->options([
                                'homepage' => 'Homepage',
                                'about' => 'About Page',
                                'approach' => 'Our Approach Page',
                                'categories' => 'Categories Page',
                                'nominees' => 'Current Nominees Page',
                                'past_winners' => 'Past Winners Page',
                                'gallery' => 'Gallery Page',
                                'contact' => 'Contact Page',
                                'footer' => 'Footer Content',
                            ])
                            ->required()
                            ->live(),

                        Forms\Components\Select::make('section')
                            ->options(fn (Forms\Get $get) => match ($get('page')) {
                                'homepage' => [
                                    'hero' => 'Hero Section',
                                    'about' => 'About Section',
                                    'approach' => 'Approach Section',
                                    'upcoming_categories' => 'Upcoming Categories',
                                    'award_ceremony' => 'Award Ceremony',
                                    'past_winners' => 'Past Winners',
                                    'gallery' => 'Gallery'
                                ],
                                'about' => [
                                    'hero' => 'Hero Section',
                                    'story' => 'Our Story',
                                    'team' => 'Team Section',
                                    'mission' => 'Mission & Vision',
                                    'contact_info' => 'Contact Information'
                                ],
                                'approach' => [
                                    'hero' => 'Hero Section',
                                    'methodology' => 'Our Methodology',
                                    'process' => 'Our Process',
                                    'values' => 'Our Values'
                                ],
                                'categories' => [
                                    'hero' => 'Hero Section',
                                    'introduction' => 'Introduction',
                                    'content' => 'Main Content'
                                ],
                                'nominees' => [
                                    'hero' => 'Hero Section',
                                    'filters' => 'Filter Section',
                                    'content' => 'Main Content',
                                    'voting_info' => 'Voting Information'
                                ],
                                'past_winners' => [
                                    'hero' => 'Hero Section',
                                    'introduction' => 'Introduction',
                                    'content' => 'Main Content'
                                ],
                                'gallery' => [
                                    'hero' => 'Hero Section',
                                    'introduction' => 'Introduction',
                                    'content' => 'Main Content'
                                ],
                                'contact' => [
                                    'hero' => 'Hero Section',
                                    'contact_form' => 'Contact Form',
                                    'contact_info' => 'Contact Information',
                                    'map' => 'Map Section'
                                ],
                                'footer' => [
                                    'links' => 'Footer Links',
                                    'social' => 'Social Media',
                                    'contact' => 'Contact Information',
                                    'copyright' => 'Copyright & Legal'
                                ],
                                default => []
                            })
                            ->required()
                            ->live(),

                        Forms\Components\TextInput::make('key')
                            ->required()
                            ->helperText('Unique identifier for this content'),

                        Forms\Components\Select::make('type')
                            ->options([
                                'text' => 'Text',
                                'html' => 'HTML',
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
                            ->visible(fn (Forms\Get $get) => in_array($get('type'), ['text']))
                            ->required(fn (Forms\Get $get) => $get('type') === 'text'),

                        // HTML content
                        Forms\Components\RichEditor::make('content')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'html')
                            ->required(fn (Forms\Get $get) => $get('type') === 'html'),

                        // Image upload
                        Forms\Components\FileUpload::make('content')
                            ->image()
                            ->directory('page-content')
                            ->visibility('public')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'image')
                            ->required(fn (Forms\Get $get) => $get('type') === 'image'),

                        // JSON content
                        Forms\Components\Textarea::make('content')
                            ->rows(8)
                            ->helperText('Enter valid JSON data')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'json')
                            ->required(fn (Forms\Get $get) => $get('type') === 'json'),

                        // Meta data for additional info
                        Forms\Components\KeyValue::make('meta')
                            ->helperText('Additional metadata (alt text, captions, etc.)'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('page')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'homepage' => 'success',
                        'about' => 'info',
                        'approach' => 'warning',
                        'categories' => 'danger',
                        'nominees' => 'primary',
                        'past_winners' => 'gray',
                        'gallery' => 'success',
                        'contact' => 'warning',
                        'footer' => 'gray',
                        default => 'gray'
                    }),

                Tables\Columns\TextColumn::make('section')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('key')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'text' => 'gray',
                        'html' => 'info',
                        'image' => 'success',
                        'json' => 'warning',
                        default => 'gray'
                    }),

                Tables\Columns\TextColumn::make('content')
                    ->limit(50)
                    ->wrap()
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->type === 'image') {
                            return 'Image: ' . basename($state);
                        }
                        return $state;
                    }),

                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('page')
                    ->options([
                        'homepage' => 'Homepage',
                        'about' => 'About Page',
                        'approach' => 'Our Approach Page',
                        'categories' => 'Categories Page',
                        'nominees' => 'Current Nominees Page',
                        'past_winners' => 'Past Winners Page',
                        'gallery' => 'Gallery Page',
                        'contact' => 'Contact Page',
                        'footer' => 'Footer Content',
                    ]),

                Tables\Filters\SelectFilter::make('section'),

                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'text' => 'Text',
                        'html' => 'HTML',
                        'image' => 'Image',
                        'json' => 'JSON Data',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('page')
            ->groupedBulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPageContents::route('/'),
            'create' => Pages\CreatePageContent::route('/create'),
            'edit' => Pages\EditPageContent::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['key', 'content'];
    }
}
