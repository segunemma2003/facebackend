<?php
// app/Filament/Resources/GlobalSettingsResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\GlobalSettingsResource\Pages;
use App\Models\PageContent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GlobalSettingsResource extends Resource
{
    protected static ?string $model = PageContent::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Global Settings';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('page')
                    ->default('global_settings'),

                Forms\Components\Section::make('Content Details')
                    ->schema([
                        Forms\Components\Select::make('section')
                            ->options([
                                'social_media' => 'Social Media Links',
                                'contact_info' => 'Contact Information',
                                'footer' => 'Footer Content',
                                'company_info' => 'Company Information'
                            ])
                            ->required()
                            ->live(),

                        Forms\Components\Select::make('key')
                            ->options(fn (Forms\Get $get) => match ($get('section')) {
                                'social_media' => [
                                    'facebook_url' => 'Facebook URL',
                                    'twitter_url' => 'Twitter URL',
                                    'instagram_url' => 'Instagram URL',
                                    'linkedin_url' => 'LinkedIn URL',
                                    'youtube_url' => 'YouTube URL',
                                    'social_links_json' => 'All Social Links (JSON)'
                                ],
                                'contact_info' => [
                                    'primary_email' => 'Primary Email',
                                    'support_email' => 'Support Email',
                                    'phone_number' => 'Phone Number',
                                    'address' => 'Physical Address',
                                    'city' => 'City',
                                    'state' => 'State',
                                    'country' => 'Country',
                                    'postal_code' => 'Postal Code'
                                ],
                                'footer' => [
                                    'footer_note' => 'Footer Note',
                                    'copyright_text' => 'Copyright Text',
                                    'privacy_policy_url' => 'Privacy Policy URL',
                                    'terms_of_service_url' => 'Terms of Service URL'
                                ],
                                'company_info' => [
                                    'company_name' => 'Company Name',
                                    'company_motto' => 'Company Motto',
                                    'company_description' => 'Company Description',
                                    'founded_year' => 'Founded Year',
                                    'company_logo' => 'Company Logo'
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
                                'url' => 'URL'
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
                        Forms\Components\TextInput::make('content')
                            ->visible(fn (Forms\Get $get) => in_array($get('type'), ['text', 'url']))
                            ->required(fn (Forms\Get $get) => in_array($get('type'), ['text', 'url']))
                            ->url(fn (Forms\Get $get) => $get('type') === 'url'),

                        // HTML content
                        Forms\Components\RichEditor::make('content')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'html')
                            ->required(fn (Forms\Get $get) => $get('type') === 'html'),

                        // Image upload
                        Forms\Components\FileUpload::make('content')
                            ->image()
                            ->directory('global-settings')
                            ->visibility('public')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'image')
                            ->required(fn (Forms\Get $get) => $get('type') === 'image'),

                        // JSON content with examples
                        Forms\Components\Textarea::make('content')
                            ->rows(8)
                            ->helperText(fn (Forms\Get $get) => match ($get('key')) {
                                'social_links_json' => 'Format: [{"platform":"Facebook","url":"https://...","icon":"facebook"},...]',
                                default => 'Enter valid JSON data'
                            })
                            ->visible(fn (Forms\Get $get) => $get('type') === 'json')
                            ->required(fn (Forms\Get $get) => $get('type') === 'json'),

                        // Meta data
                        Forms\Components\KeyValue::make('meta')
                            ->helperText('Additional metadata (alt text, descriptions, etc.)'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->where('page', 'global_settings'))
            ->columns([
                Tables\Columns\TextColumn::make('section')
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'social_media' => 'Social Media',
                        'contact_info' => 'Contact Info',
                        'footer' => 'Footer',
                        'company_info' => 'Company Info',
                        default => $state
                    }),

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
                        'url' => 'purple',
                        default => 'gray'
                    }),

                Tables\Columns\TextColumn::make('content')
                    ->limit(50)
                    ->wrap()
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->type === 'image') {
                            return 'Image: ' . basename($state);
                        }
                        if ($record->type === 'url') {
                            return 'URL: ' . $state;
                        }
                        return $state;
                    }),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('section')
                    ->options([
                        'social_media' => 'Social Media',
                        'contact_info' => 'Contact Info',
                        'footer' => 'Footer',
                        'company_info' => 'Company Info'
                    ]),

                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'text' => 'Text',
                        'html' => 'HTML',
                        'image' => 'Image',
                        'json' => 'JSON',
                        'url' => 'URL'
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
            ->defaultSort('section');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGlobalSettings::route('/'),
            'create' => Pages\CreateGlobalSettings::route('/create'),
            'edit' => Pages\EditGlobalSettings::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['key', 'content'];
    }
}
