<?php
// app/Filament/Resources/GlobalSettingsResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\GlobalSettingsResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class GlobalSettingsResource extends BasePageContentResource
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Global Settings';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 10;

    protected static function getPageName(): string
    {
        return 'global_settings';
    }

    protected static function getPageSections(): array
    {
        return [
            'social_media' => 'Social Media Links',
            'contact_info' => 'Contact Information',
            'footer' => 'Footer Content',
            'company_info' => 'Company Information'
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->byPage(static::getPageName())->orderedBySort())
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'text' => 'gray',
                        'html' => 'info',
                        'image' => 'success',
                        'url' => 'warning',
                        'json' => 'danger',
                        'boolean' => 'primary',
                        'number' => 'secondary',
                        default => 'gray'
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('content_preview')
                    ->label('Content')
                    ->limit(80)
                    ->wrap()
                    ->tooltip(fn ($record) => $record->content_preview)
                    ->formatStateUsing(function ($state, $record) {
                        return match ($record->type) {
                            'image' => $record->getRawOriginal('content') ?
                                '🖼️ ' . self::getImageBasename($record->getRawOriginal('content')) :
                                'No image',
                            'json' => self::formatJsonPreview($record),
                            'boolean' => $record->formatted_content ? '✅ True' : '❌ False',
                            'url' => '🔗 ' . ($record->getRawOriginal('content') ?: 'No URL'),
                            'html' => '📝 ' . strip_tags($record->getRawOriginal('content') ?: ''),
                            default => $record->getRawOriginal('content') ?: 'No content'
                        };
                    }),

                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable()
                    ->alignCenter()
                    ->size('sm'),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->since()
                    ->size('sm'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('section')
                    ->options(static::getPageSections())
                    ->multiple(),

                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'text' => 'Text',
                        'html' => 'HTML',
                        'image' => 'Image',
                        'url' => 'URL',
                        'json' => 'JSON Data',
                        'boolean' => 'Boolean',
                        'number' => 'Number',
                    ])
                    ->multiple(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->actions([


                Tables\Actions\EditAction::make(),

                Tables\Actions\ReplicateAction::make()
                    ->form([
                        Forms\Components\TextInput::make('key')
                            ->required()
                            ->helperText('New unique key for the duplicated content'),
                        Forms\Components\Select::make('section')
                            ->options(static::getPageSections())
                            ->required(),
                    ])
                    ->beforeReplicaSaved(function (array $data, $replica): void {
                        $replica->key = $data['key'];
                        $replica->section = $data['section'];
                    }),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('toggleActive')
                        ->label('Toggle Active')
                        ->icon('heroicon-o-eye')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['is_active' => !$record->is_active]);
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->groups([
                Tables\Grouping\Group::make('section')
                    ->label('Section')
                    ->titlePrefixedWithLabel(false)
                    ->getTitleFromRecordUsing(fn ($record) => static::getPageSections()[$record->section] ?? $record->section)
                    ->collapsible()
                    ->orderQueryUsing(fn (Builder $query, string $direction) =>
                        $query->orderByRaw("FIELD(section, '" . implode("','", array_keys(static::getPageSections())) . "') " . $direction)
                    ),
            ])
            ->defaultGroup('section')
            ->striped()
            ->paginated([25, 50, 100])
            ->defaultSort('sort_order')
            ->recordUrl(null)
            ->emptyStateHeading('No Global Settings')
            ->emptyStateDescription('Create your first global setting to get started.')
            ->emptyStateIcon('heroicon-o-cog-6-tooth');
    }

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
                            ->live()
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('key', null)),

                        Forms\Components\Select::make('key')
                            ->options(fn (Forms\Get $get) => match ($get('section')) {
                                'social_media' => [
                                    'facebook_url' => 'Facebook URL',
                                    'twitter_url' => 'Twitter URL',
                                    'instagram_url' => 'Instagram URL',
                                    'linkedin_url' => 'LinkedIn URL',
                                    'youtube_url' => 'YouTube URL',
                                    'tiktok_url' => 'TikTok URL',
                                    'social_links_json' => 'All Social Links (JSON)'
                                ],
                                'contact_info' => [
                                    'primary_email' => 'Primary Email',
                                    'support_email' => 'Support Email',
                                    'nominations_email' => 'Nominations Email',
                                    'phone_international' => 'International Phone',
                                    'phone_toll_free' => 'Toll Free Phone',
                                    'address' => 'Physical Address',
                                    'full_address' => 'Full Address (HTML)',
                                    'city' => 'City',
                                    'state' => 'State',
                                    'country' => 'Country',
                                    'postal_code' => 'Postal Code',
                                    'office_hours' => 'Office Hours',
                                    'response_time' => 'Response Time',
                                    'google_maps_embed_url' => 'Google Maps Embed URL'
                                ],
                                'footer' => [
                                    'footer_note' => 'Footer Note',
                                    'copyright_text' => 'Copyright Text',
                                    'privacy_policy_url' => 'Privacy Policy URL',
                                    'terms_of_service_url' => 'Terms of Service URL',
                                    'footer_links' => 'Footer Links (JSON)',
                                    'footer_sections' => 'Footer Sections (JSON)'
                                ],
                                'company_info' => [
                                    'company_name' => 'Company Name',
                                    'company_motto' => 'Company Motto',
                                    'company_description' => 'Company Description',
                                    'founded_year' => 'Founded Year',
                                    'company_logo' => 'Company Logo',
                                    'company_logo_dark' => 'Company Logo (Dark)',
                                    'favicon' => 'Favicon',
                                    'company_size' => 'Company Size',
                                    'headquarters' => 'Headquarters Location'
                                ],
                                default => []
                            })
                            ->required()
                            ->live()
                            ->searchable(),

                        Forms\Components\Select::make('type')
                            ->options([
                                'text' => 'Text',
                                'html' => 'HTML/Rich Text',
                                'image' => 'Image',
                                'url' => 'URL',
                                'json' => 'JSON Data',
                                'boolean' => 'Boolean',
                                'number' => 'Number',
                            ])
                            ->required()
                            ->live()
                            ->helperText(fn (Forms\Get $get) => match ($get('type')) {
                                'json' => 'Use for structured data like social links, footer sections',
                                'boolean' => 'Use for true/false values',
                                'number' => 'Use for numeric values like years',
                                'image' => 'Enter image URL from Curator gallery',
                                'url' => 'For external links and URLs',
                                'html' => 'Rich text with formatting',
                                'text' => 'Plain text content',
                                default => 'Select the appropriate content type'
                            }),

                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Order of display within the section (lower numbers first)')
                            ->step(1),

                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->helperText('Uncheck to hide this content from the frontend'),
                    ]),

                Forms\Components\Section::make('Content')
                    ->schema([
                        // Hidden field that actually stores the data
                        Forms\Components\Hidden::make('content'),

                        // Curator Gallery Helper Section (for JSON repeaters)
                        Forms\Components\Section::make('🖼️ Curator Gallery Helper')
                            ->schema([
                                Forms\Components\Placeholder::make('gallery_instructions')
                                    ->content(new HtmlString('
                                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                            <h4 class="font-semibold text-blue-900 mb-2">📸 How to use images from Curator gallery:</h4>
                                            <ol class="list-decimal list-inside space-y-1 text-blue-800 text-sm">
                                                <li><strong>Open Gallery:</strong> Click the "Open Curator Gallery" button below</li>
                                                <li><strong>Browse & Select:</strong> Choose images from your media gallery</li>
                                                <li><strong>Copy URLs:</strong> Copy the image URLs from the gallery</li>
                                                <li><strong>Paste:</strong> Paste URLs into the image fields in your forms below</li>
                                            </ol>
                                            <p class="text-xs text-blue-600 mt-2">💡 <strong>Tip:</strong> Upload new images to Curator first, then use them here!</p>
                                        </div>
                                    ')),

                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('open_gallery')
                                        ->label('🖼️ Open Curator Gallery')
                                        ->color('primary')
                                        ->url('/admin/media', shouldOpenInNewTab: true)
                                        ->icon('heroicon-o-photo'),
                                ])
                                    ->alignCenter()
                                    ->columnSpanFull(),
                            ])
                            ->visible(fn (Forms\Get $get) => in_array($get('type'), ['json', 'image']))
                            ->collapsible()
                            ->collapsed(true)
                            ->description('Quick access to your Curator gallery for copying URLs'),

                        // Text content
                        Forms\Components\Textarea::make('text_input')
                            ->label('Text Content')
                            ->rows(3)
                            ->visible(fn (Forms\Get $get) => $get('type') === 'text')
                            ->required(fn (Forms\Get $get) => $get('type') === 'text')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('content', $state))
                            ->afterStateHydrated(fn ($component, $state, $record) =>
                                $component->state($record && $record->type === 'text' ? $record->getRawOriginal('content') : null))
                            ->dehydrated(false),

                        // HTML content
                        Forms\Components\RichEditor::make('html_input')
                            ->label('HTML Content')
                            ->toolbarButtons([
                                'blockquote', 'bold', 'bulletList', 'codeBlock', 'h2', 'h3',
                                'italic', 'link', 'orderedList', 'redo', 'strike', 'underline', 'undo',
                            ])
                            ->visible(fn (Forms\Get $get) => $get('type') === 'html')
                            ->required(fn (Forms\Get $get) => $get('type') === 'html')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('content', $state))
                            ->afterStateHydrated(fn ($component, $state, $record) =>
                                $component->state($record && $record->type === 'html' ? $record->getRawOriginal('content') : null))
                            ->dehydrated(false),

                        // URL content
                        Forms\Components\TextInput::make('url_input')
                            ->label('URL')
                            ->url()
                            ->visible(fn (Forms\Get $get) => $get('type') === 'url')
                            ->required(fn (Forms\Get $get) => $get('type') === 'url')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('content', $state))
                            ->afterStateHydrated(fn ($component, $state, $record) =>
                                $component->state($record && $record->type === 'url' ? $record->getRawOriginal('content') : null))
                            ->dehydrated(false),

                        // Number content
                        Forms\Components\TextInput::make('number_input')
                            ->label('Number')
                            ->numeric()
                            ->visible(fn (Forms\Get $get) => $get('type') === 'number')
                            ->required(fn (Forms\Get $get) => $get('type') === 'number')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('content', $state))
                            ->afterStateHydrated(fn ($component, $state, $record) =>
                                $component->state($record && $record->type === 'number' ? $record->getRawOriginal('content') : null))
                            ->dehydrated(false),

                        // Boolean content
                        Forms\Components\Toggle::make('boolean_input')
                            ->label('Boolean Value')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'boolean')
                            ->live()
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('content', $state ? 'true' : 'false'))
                            ->afterStateHydrated(fn ($component, $state, $record) =>
                                $component->state($record && $record->type === 'boolean' ?
                                    filter_var($record->getRawOriginal('content'), FILTER_VALIDATE_BOOLEAN) : false))
                            ->dehydrated(false),

                        // Image URL input
                        Forms\Components\TextInput::make('image_url')
                            ->label('Image URL')
                            ->url()
                            ->visible(fn (Forms\Get $get) => $get('type') === 'image')
                            ->required(fn (Forms\Get $get) => $get('type') === 'image')
                            ->placeholder('https://your-domain.com/storage/media/image.jpg')
                            ->helperText('Copy image URL from Curator gallery and paste it here')
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('open_gallery_for_image')
                                    ->icon('heroicon-o-photo')
                                    ->url('/admin/media', shouldOpenInNewTab: true)
                                    ->tooltip('Open Curator Gallery')
                            )
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('content', $state))
                            ->afterStateHydrated(fn ($component, $state, $record) =>
                                $component->state($record && $record->type === 'image' ? $record->getRawOriginal('content') : null))
                            ->dehydrated(false),

                        // Show current image preview for existing records
                        Forms\Components\Placeholder::make('current_image_preview')
                            ->label('Current Image Preview')
                            ->content(function (Forms\Get $get, $record) {
                                if (!$record || $record->type !== 'image') {
                                    return '';
                                }
                                $content = $record->getRawOriginal('content');
                                if ($content) {
                                    return '<img src="' . $content . '" alt="Current image" style="max-width: 200px; max-height: 150px; border-radius: 8px; border: 1px solid #e5e7eb;">';
                                }
                                return 'No image set';
                            })
                            ->visible(fn (Forms\Get $get, $record) => $get('type') === 'image' && $record),

                        // JSON content with dynamic repeater based on key
                        Forms\Components\Repeater::make('json_input')
                            ->label(fn (Forms\Get $get) => static::getJsonLabel($get('key')))
                            ->visible(fn (Forms\Get $get) => $get('type') === 'json')
                            ->required(fn (Forms\Get $get) => $get('type') === 'json')
                            ->schema(fn (Forms\Get $get) => static::getJsonSchema($get('key')))
                            ->defaultItems(0)
                            ->addActionLabel(fn (Forms\Get $get) => static::getJsonAddLabel($get('key')))
                            ->collapsed()
                            ->cloneable()
                            ->collapsible()
                            ->reorderable()
                            ->deletable()
                            ->itemLabel(fn (array $state, Forms\Get $get): ?string =>
                                static::getJsonItemLabel($state, $get('key'))
                            )
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if (empty($state)) {
                                    $set('content', '[]');
                                    return;
                                }
                                $set('content', json_encode(array_values($state), JSON_UNESCAPED_UNICODE));
                            })
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if (!$record || $record->type !== 'json') {
                                    $component->state([]);
                                    return;
                                }

                                $content = $record->getRawOriginal('content');
                                if (empty($content)) {
                                    $component->state([]);
                                    return;
                                }

                                if (is_string($content)) {
                                    $decoded = json_decode($content, true);
                                    if ($decoded !== null) {
                                        if (is_array($decoded)) {
                                            if (isset($decoded[0]) && is_array($decoded[0])) {
                                                $component->state($decoded);
                                            } else {
                                                $component->state([$decoded]);
                                            }
                                        } else {
                                            $component->state([]);
                                        }
                                    } else {
                                        $component->state([]);
                                    }
                                } else {
                                    $component->state([]);
                                }
                            })
                            ->helperText(fn (Forms\Get $get) => static::getJsonHelperText($get('key')))
                            ->dehydrated(false),

                        // Meta data
                        Forms\Components\KeyValue::make('meta')
                            ->helperText('Additional metadata (alt text for images, captions, etc.)')
                            ->addable(true)
                            ->deletable(true)
                            ->keyLabel('Property')
                            ->valueLabel('Value'),
                    ]),
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::byPage(static::getPageName())->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'primary';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGlobalSettings::route('/'),
            'create' => Pages\CreateGlobalSettings::route('/create'),
            'edit' => Pages\EditGlobalSettings::route('/{record}/edit'),
        ];
    }

    // Helper methods for JSON handling
    protected static function getJsonLabel(string $key): string
    {
        return match ($key) {
            'social_links_json' => 'Social Media Links',
            'footer_links' => 'Footer Links',
            'footer_sections' => 'Footer Sections',
            default => 'Data Items'
        };
    }

    protected static function getJsonHelperText(string $key): string
    {
        return match ($key) {
            'social_links_json' => 'Add all your social media links. Include platform name, URL, and icon.',
            'footer_links' => 'Add footer navigation links. Include label, URL, and optional description.',
            'footer_sections' => 'Add footer sections with grouped links and content.',
            default => 'Add data items for this section.'
        };
    }

    protected static function getJsonSchema(string $key): array
    {
        return match ($key) {
            'social_links_json' => [
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\TextInput::make('platform')
                            ->label('Platform Name')
                            ->required()
                            ->placeholder('Facebook, Twitter, Instagram, etc.'),
                        Forms\Components\TextInput::make('url')
                            ->label('URL')
                            ->url()
                            ->required()
                            ->placeholder('https://facebook.com/yourpage'),
                        Forms\Components\TextInput::make('icon')
                            ->label('Icon Name')
                            ->required()
                            ->placeholder('facebook, twitter, instagram, etc.')
                            ->helperText('Icon name for display'),
                    ]),
            ],
            'footer_links' => [
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('label')
                            ->label('Link Label')
                            ->required()
                            ->placeholder('Privacy Policy, Terms, etc.'),
                        Forms\Components\TextInput::make('url')
                            ->label('URL')
                            ->url()
                            ->required()
                            ->placeholder('https://yoursite.com/privacy'),
                    ]),
                Forms\Components\TextInput::make('description')
                    ->label('Description')
                    ->placeholder('Optional description for the link'),
            ],
            'footer_sections' => [
                Forms\Components\TextInput::make('title')
                    ->label('Section Title')
                    ->required()
                    ->placeholder('Company, Legal, Support, etc.'),
                Forms\Components\Repeater::make('links')
                    ->label('Section Links')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('label')
                                    ->label('Link Label')
                                    ->required(),
                                Forms\Components\TextInput::make('url')
                                    ->label('URL')
                                    ->url()
                                    ->required(),
                            ]),
                    ])
                    ->defaultItems(0)
                    ->addActionLabel('Add Link')
                    ->collapsible(),
            ],
            default => [
                Forms\Components\TextInput::make('title')
                    ->label('Title')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->rows(3),
            ]
            };
    }

    protected static function getJsonAddLabel(string $key): string
    {
        return match ($key) {
            'social_links_json' => 'Add Social Link',
            'footer_links' => 'Add Footer Link',
            'footer_sections' => 'Add Footer Section',
            default => 'Add Item'
        };
    }

    protected static function getJsonItemLabel(array $state, string $key): ?string
    {
        return match ($key) {
            'social_links_json' => ($state['platform'] ?? 'New Social Link') . ': ' . ($state['url'] ?? ''),
            'footer_links' => ($state['label'] ?? 'New Link') . ': ' . ($state['url'] ?? ''),
            'footer_sections' => $state['title'] ?? 'New Section',
            default => $state['title'] ?? $state['name'] ?? 'New Item'
        };
    }

    // Helper method to get image basename for table display
    protected static function getImageBasename(string $content): string
    {
        return basename($content);
    }

    // Helper method to format JSON preview in table
    protected static function formatJsonPreview($record): string
    {
        $content = $record->getRawOriginal('content');
        if (!$content) return '📄 Empty JSON';

        $decoded = json_decode($content, true);
        if ($decoded === null) return '❌ Invalid JSON';

        if (is_array($decoded)) {
            $count = count($decoded);

            if (isset($decoded[0]) && is_array($decoded[0])) {
                $firstItem = $decoded[0];

                if (isset($firstItem['platform'], $firstItem['url'])) {
                    $platforms = array_slice(array_column($decoded, 'platform'), 0, 2);
                    $preview = implode(', ', $platforms);
                    if ($count > 2) $preview .= '...';
                    return "📄 Social Links ({$count}): {$preview}";
                }

                if (isset($firstItem['label'], $firstItem['url'])) {
                    $labels = array_slice(array_column($decoded, 'label'), 0, 2);
                    $preview = implode(', ', $labels);
                    if ($count > 2) $preview .= '...';
                    return "📄 Links ({$count}): {$preview}";
                }

                if (isset($firstItem['title'])) {
                    $titles = array_slice(array_column($decoded, 'title'), 0, 2);
                    $preview = implode(', ', $titles);
                    if ($count > 2) $preview .= '...';
                    return "📄 Sections ({$count}): {$preview}";
                }

                $keys = array_keys($firstItem);
                $preview = implode(', ', array_slice($keys, 0, 3));
                if (count($keys) > 3) $preview .= '...';
                return "📄 Structured data ({$count} items): {$preview}";
            } else {
                $keys = array_keys($decoded);
                $preview = implode(', ', array_slice($keys, 0, 3));
                if (count($keys) > 3) $preview .= '...';
                return "📄 Data ({$count} keys): {$preview}";
            }
        }

        return '📄 JSON Object';
    }
}
