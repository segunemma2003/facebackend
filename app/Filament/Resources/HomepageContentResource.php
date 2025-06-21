<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HomepageContentResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
            'trending_ticker' => 'Trending Ticker',
            'about' => 'About Section',
            'approach' => 'Approach Section',
            'upcoming_categories' => 'Award Categories',
            'gallery_section' => 'Event Gallery',
            'past_winners' => 'Past Winners Section',
            'award_ceremony' => 'Award Ceremony Section'
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
                                '🖼️ ' . basename($record->getRawOriginal('content')) :
                                'No image',
                            'json' => static::formatJsonPreview($record),
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
                Tables\Actions\ViewAction::make()
                    ->modalContent(fn ($record) => view('filament.pages.content-preview', compact('record'))),

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
            ->emptyStateHeading('No Homepage Content')
            ->emptyStateDescription('Create your first homepage content item to get started.')
            ->emptyStateIcon('heroicon-o-document-text');
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
                                'hero' => [
                                    'main_title' => 'Main Title',
                                    'main_subtitle' => 'Main Subtitle',
                                    'current_highlight_subtitle' => 'Current Highlight Subtitle',
                                    'current_highlight_content' => 'Current Highlight Content',
                                    'primary_button_text' => 'Primary Button Text',
                                    'secondary_button_text' => 'Secondary Button Text'
                                ],
                                'trending_ticker' => [
                                    'enabled' => 'Ticker Enabled',
                                    'ticker_label' => 'Ticker Label',
                                    'fallback_message' => 'Fallback Message',
                                    'auto_rotate_speed' => 'Auto Rotate Speed (ms)',
                                    'show_vote_counts' => 'Show Vote Counts',
                                    'show_percentages' => 'Show Percentages',
                                    'background_color' => 'Background Color Class'
                                ],
                                'about' => [
                                    'title' => 'Section Title',
                                    'content' => 'About Content',
                                    'face_meanings' => 'FACE Meanings (JSON)',
                                    'hero_image' => 'Hero Image',
                                    'hero_image_fallback' => 'Hero Image Fallback',
                                    'image_caption' => 'Image Caption'
                                ],
                                'approach' => [
                                    'face_sub_title' => 'FACE Sub Title',
                                    'approach_title' => 'Our Approach Title',
                                    'approach_content' => 'Our Approach Content',
                                    'image_title' => 'Image Title',
                                    'approach_items' => 'Approach Items (JSON)'
                                ],
                                'upcoming_categories' => [
                                    'title' => 'Section Title',
                                    'subtitle' => 'Section Subtitle',
                                    'content' => 'Section Content'
                                ],
                                'gallery_section' => [
                                    'title' => 'Section Title',
                                    'subtitle' => 'Section Subtitle',
                                    'content' => 'Section Content',
                                    'empty_state_message' => 'Empty State Message',
                                    'button_text' => 'Button Text',
                                    'gallery_items' => 'Gallery Items (JSON)'
                                ],
                                'past_winners' => [
                                    'title' => 'Section Title',
                                    'subtitle' => 'Section Subtitle',
                                    'content' => 'Section Content',
                                    'empty_state_message' => 'Empty State Message',
                                    'button_text' => 'Button Text',
                                    'testimonials' => 'Winner Testimonials (JSON)'
                                ],
                                'award_ceremony' => [
                                    'title' => 'Ceremony Title',
                                    'subtitle' => 'Ceremony Subtitle',
                                    'content' => 'Ceremony Content',
                                    'event_date' => 'Event Date',
                                    'venue' => 'Venue Information',
                                    'description' => 'Event Description',
                                    'dress_code' => 'Dress Code',
                                    'expected_attendance' => 'Expected Attendance',
                                    'registration_open_message' => 'Registration Open Message',
                                    'registration_closed_message' => 'Registration Closed Message',
                                    'registration_button_text' => 'Registration Button Text',
                                    'ticket_info' => 'Ticket Information (JSON)',
                                    'event_schedule' => 'Event Schedule (JSON)',
                                    'team_members' => 'Organizing Team (JSON)'
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
                                'json' => 'Use for structured data like arrays and objects',
                                'boolean' => 'Use for true/false values',
                                'number' => 'Use for numeric values',
                                'image' => 'Upload images to storage',
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
                        // Text content
                        Forms\Components\Textarea::make('text_content')
                            ->label('Text Content')
                            ->rows(3)
                            ->visible(fn (Forms\Get $get) => $get('type') === 'text')
                            ->required(fn (Forms\Get $get) => $get('type') === 'text')
                            ->afterStateHydrated(fn ($component, $state, $record) =>
                                $component->state($record?->getRawOriginal('content') ?? $state))
                            ->dehydrated(false),

                        // HTML content
                        Forms\Components\RichEditor::make('html_content')
                            ->label('HTML Content')
                            ->toolbarButtons([
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'underline',
                                'undo',
                            ])
                            ->visible(fn (Forms\Get $get) => $get('type') === 'html')
                            ->required(fn (Forms\Get $get) => $get('type') === 'html')
                            ->afterStateHydrated(fn ($component, $state, $record) =>
                                $component->state($record?->getRawOriginal('content') ?? $state))
                            ->dehydrated(false),

                        // URL content
                        Forms\Components\TextInput::make('url_content')
                            ->label('URL')
                            ->url()
                            ->visible(fn (Forms\Get $get) => $get('type') === 'url')
                            ->required(fn (Forms\Get $get) => $get('type') === 'url')
                            ->afterStateHydrated(fn ($component, $state, $record) =>
                                $component->state($record?->getRawOriginal('content') ?? $state))
                            ->dehydrated(false),

                        // Number content
                        Forms\Components\TextInput::make('number_content')
                            ->label('Number')
                            ->numeric()
                            ->visible(fn (Forms\Get $get) => $get('type') === 'number')
                            ->required(fn (Forms\Get $get) => $get('type') === 'number')
                            ->afterStateHydrated(fn ($component, $state, $record) =>
                                $component->state($record?->getRawOriginal('content') ?? $state))
                            ->dehydrated(false),

                        // Boolean content
                        Forms\Components\Toggle::make('boolean_content')
                            ->label('Boolean Value')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'boolean')
                            ->afterStateHydrated(fn ($component, $state, $record) =>
                                $component->state(filter_var($record?->getRawOriginal('content') ?? $state, FILTER_VALIDATE_BOOLEAN)))
                            ->dehydrated(false),

                        // Image upload
                        Forms\Components\FileUpload::make('image_content')
                            ->label('Image')
                            ->image()
                            ->directory('homepage')
                            ->visibility('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->visible(fn (Forms\Get $get) => $get('type') === 'image')
                            ->required(fn (Forms\Get $get) => $get('type') === 'image')
                            ->afterStateHydrated(function ($component, $state, $record) {
                                $content = $record?->getRawOriginal('content');
                                if ($content && !str_starts_with($content, 'http')) {
                                    $component->state([$content]);
                                } else {
                                    $component->state([]);
                                }
                            })
                            ->dehydrated(false),

                        // JSON content - Structured Repeater
                        Forms\Components\Repeater::make('json_content')
                            ->label('JSON Data Items')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'json')
                            ->required(fn (Forms\Get $get) => $get('type') === 'json')
                            ->schema(fn (Forms\Get $get) => static::getJsonSchema($get('key')))
                            ->columns(3)
                            ->addActionLabel(fn (Forms\Get $get) => static::getJsonAddLabel($get('key')))
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state, Forms\Get $get): ?string => static::getJsonItemLabel($state, $get('key')))
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if (!$record) return;

                                $content = $record->getRawOriginal('content');
                                if (is_string($content)) {
                                    $decoded = json_decode($content, true);
                                    if ($decoded !== null && is_array($decoded) && isset($decoded[0])) {
                                        // Array of objects - use repeater
                                        $component->state($decoded);
                                        return;
                                    }
                                }
                                $component->state([]);
                            })
                            ->dehydrated(false),

                        // Simple Key-Value for flat JSON objects
                        Forms\Components\KeyValue::make('json_simple')
                            ->label('Simple JSON Data')
                            ->addable(true)
                            ->deletable(true)
                            ->reorderable(true)
                            ->keyLabel('Key')
                            ->valueLabel('Value')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'json')
                            ->helperText('For simple key-value pairs. Use the structured editor above for lists of items.')
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if (!$record) return;

                                $content = $record->getRawOriginal('content');
                                if (is_string($content)) {
                                    $decoded = json_decode($content, true);
                                    if ($decoded !== null && is_array($decoded) && !isset($decoded[0])) {
                                        // Simple key-value object - use KeyValue
                                        $component->state($decoded);
                                        return;
                                    }
                                }
                                $component->state([]);
                            })
                            ->dehydrated(false),

                        // Raw JSON textarea for complex structures
                        Forms\Components\Textarea::make('json_textarea_content')
                            ->label('Raw JSON (Advanced)')
                            ->rows(8)
                            ->helperText('For complex nested structures or when you prefer to edit JSON directly.')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'json')
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if (!$record) return;

                                $content = $record->getRawOriginal('content');
                                if (is_string($content)) {
                                    $decoded = json_decode($content, true);
                                    if ($decoded !== null) {
                                        $component->state(json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                                    } else {
                                        $component->state($content);
                                    }
                                }
                            })
                            ->dehydrated(false)
                            ->rules([
                                fn (Forms\Get $get) => $get('type') === 'json' ? 'json' : '',
                            ]),

                        // Hidden field to store the actual content
                        Forms\Components\Hidden::make('content')
                            ->afterStateHydrated(fn ($component, $state, $record) =>
                                $component->state($record?->getRawOriginal('content') ?? $state))
                            ->dehydrateStateUsing(function ($state, Forms\Get $get) {
                                return match ($get('type')) {
                                    'text' => $get('text_content'),
                                    'html' => $get('html_content'),
                                    'url' => $get('url_content'),
                                    'number' => $get('number_content'),
                                    'boolean' => $get('boolean_content') ? 'true' : 'false',
                                    'image' => $get('image_content') ? (is_array($get('image_content')) ? $get('image_content')[0] : $get('image_content')) : null,
                                    'json' => static::processJsonContent($get),
                                    default => $state
                                };
                            }),

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
            'index' => Pages\ListHomepageContents::route('/'),
            'create' => Pages\CreateHomepageContent::route('/create'),
            'edit' => Pages\EditHomepageContent::route('/{record}/edit'),
        ];
    }

    // Helper method to get JSON schema based on key
    protected static function getJsonSchema(string $key): array
    {
        return match ($key) {
            'face_meanings' => [
                Forms\Components\TextInput::make('letter')
                    ->label('Letter')
                    ->required()
                    ->maxLength(1)
                    ->placeholder('F')
                    ->columnSpan(1),
                Forms\Components\TextInput::make('word')
                    ->label('Word')
                    ->required()
                    ->placeholder('Focus')
                    ->columnSpan(2),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->required()
                    ->rows(2)
                    ->placeholder('The unwavering commitment to vision and purpose')
                    ->columnSpanFull(),
            ],
            'approach_items' => [
                Forms\Components\TextInput::make('title')
                    ->label('Title')
                    ->required()
                    ->placeholder('Global Reach, Local Impact')
                    ->columnSpan(2),
                Forms\Components\TextInput::make('icon')
                    ->label('Icon')
                    ->required()
                    ->placeholder('globe')
                    ->helperText('Icon name (e.g., globe, users, trophy)')
                    ->columnSpan(1),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->required()
                    ->rows(3)
                    ->placeholder('Recognizing excellence worldwide...')
                    ->columnSpanFull(),
            ],
            'ticket_info' => [
                Forms\Components\TextInput::make('type')
                    ->label('Ticket Type')
                    ->required()
                    ->placeholder('Standard Attendance')
                    ->columnSpan(1),
                Forms\Components\TextInput::make('price')
                    ->label('Price')
                    ->required()
                    ->placeholder('$250')
                    ->columnSpan(1),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->required()
                    ->rows(2)
                    ->placeholder('General admission with dinner')
                    ->columnSpan(2),
            ],
            'gallery_items' => [
                Forms\Components\TextInput::make('title')
                    ->label('Image Title')
                    ->required()
                    ->columnSpan(2),
                Forms\Components\TextInput::make('image_url')
                    ->label('Image URL')
                    ->url()
                    ->required()
                    ->columnSpan(1),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->rows(2)
                    ->columnSpan(2),
                Forms\Components\TextInput::make('category')
                    ->label('Category')
                    ->placeholder('Awards, Events, etc.')
                    ->columnSpan(1),
            ],
            'testimonials' => [
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->columnSpan(1),
                Forms\Components\TextInput::make('title')
                    ->label('Title/Position')
                    ->required()
                    ->columnSpan(1),
                Forms\Components\TextInput::make('company')
                    ->label('Company')
                    ->columnSpan(1),
                Forms\Components\Textarea::make('quote')
                    ->label('Quote')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('image')
                    ->label('Profile Image URL')
                    ->url()
                    ->columnSpan(2),
                Forms\Components\TextInput::make('rating')
                    ->label('Rating (1-5)')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(5)
                    ->columnSpan(1),
            ],
            'event_schedule' => [
                Forms\Components\TextInput::make('time')
                    ->label('Time')
                    ->required()
                    ->placeholder('7:00 PM')
                    ->columnSpan(1),
                Forms\Components\TextInput::make('title')
                    ->label('Event Title')
                    ->required()
                    ->columnSpan(2),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->rows(2)
                    ->columnSpan(2),
                Forms\Components\TextInput::make('location')
                    ->label('Location')
                    ->columnSpan(1),
            ],
            'team_members' => [
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->columnSpan(1),
                Forms\Components\TextInput::make('position')
                    ->label('Position')
                    ->required()
                    ->columnSpan(1),
                Forms\Components\TextInput::make('department')
                    ->label('Department')
                    ->columnSpan(1),
                Forms\Components\Textarea::make('bio')
                    ->label('Bio')
                    ->rows(3)
                    ->columnSpan(2),
                Forms\Components\TextInput::make('image')
                    ->label('Profile Image URL')
                    ->url()
                    ->columnSpan(1),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->columnSpan(1),
                Forms\Components\TextInput::make('linkedin')
                    ->label('LinkedIn URL')
                    ->url()
                    ->columnSpan(1),
            ],
            // Generic fallback for unknown keys
            default => [
                Forms\Components\TextInput::make('title')
                    ->label('Title')
                    ->required()
                    ->columnSpan(2),
                Forms\Components\TextInput::make('value')
                    ->label('Value')
                    ->columnSpan(1),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->rows(2)
                    ->columnSpanFull(),
            ]
        };
    }

    protected static function getJsonAddLabel(string $key): string
    {
        return match ($key) {
            'face_meanings' => 'Add FACE Meaning',
            'approach_items' => 'Add Approach Item',
            'ticket_info' => 'Add Ticket Type',
            'gallery_items' => 'Add Gallery Item',
            'testimonials' => 'Add Testimonial',
            'event_schedule' => 'Add Event',
            'team_members' => 'Add Team Member',
            default => 'Add Item'
        };
    }

    protected static function getJsonItemLabel(array $state, string $key): ?string
    {
        return match ($key) {
            'face_meanings' => ($state['letter'] ?? '') . ' - ' . ($state['word'] ?? 'New FACE Meaning'),
            'approach_items' => $state['title'] ?? 'New Approach Item',
            'ticket_info' => $state['type'] ?? 'New Ticket Type',
            'gallery_items' => $state['title'] ?? 'New Gallery Item',
            'testimonials' => ($state['name'] ?? 'New Testimonial') . ($state['company'] ? ' - ' . $state['company'] : ''),
            'event_schedule' => ($state['time'] ?? '') . ($state['title'] ? ' - ' . $state['title'] : 'New Event'),
            'team_members' => ($state['name'] ?? 'New Team Member') . ($state['position'] ? ' - ' . $state['position'] : ''),
            default => $state['title'] ?? $state['name'] ?? 'New Item'
        };
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

            // Handle specific known structures
            if (isset($decoded[0]) && is_array($decoded[0])) {
                // Array of objects
                $firstItem = $decoded[0];

                if (isset($firstItem['letter'], $firstItem['word'])) {
                    // FACE meanings
                    $letters = array_column($decoded, 'letter');
                    return "📄 FACE Meanings ({$count}): " . implode('', $letters);
                }

                if (isset($firstItem['title'], $firstItem['icon'])) {
                    // Approach items
                    $titles = array_slice(array_column($decoded, 'title'), 0, 2);
                    $preview = implode(', ', $titles);
                    if ($count > 2) $preview .= '...';
                    return "📄 Approach Items ({$count}): {$preview}";
                }

                if (isset($firstItem['type'], $firstItem['price'])) {
                    // Ticket info
                    $types = array_slice(array_column($decoded, 'type'), 0, 2);
                    $preview = implode(', ', $types);
                    if ($count > 2) $preview .= '...';
                    return "📄 Ticket Types ({$count}): {$preview}";
                }

                if (isset($firstItem['name'], $firstItem['quote'])) {
                    // Testimonials
                    $names = array_slice(array_column($decoded, 'name'), 0, 2);
                    $preview = implode(', ', $names);
                    if ($count > 2) $preview .= '...';
                    return "📄 Testimonials ({$count}): {$preview}";
                }

                if (isset($firstItem['time'], $firstItem['title'])) {
                    // Event schedule
                    $events = array_slice(array_column($decoded, 'title'), 0, 2);
                    $preview = implode(', ', $events);
                    if ($count > 2) $preview .= '...';
                    return "📄 Events ({$count}): {$preview}";
                }

                if (isset($firstItem['name'], $firstItem['position'])) {
                    // Team members
                    $members = array_slice(array_column($decoded, 'name'), 0, 2);
                    $preview = implode(', ', $members);
                    if ($count > 2) $preview .= '...';
                    return "📄 Team Members ({$count}): {$preview}";
                }

                if (isset($firstItem['title'], $firstItem['image_url'])) {
                    // Gallery items
                    $titles = array_slice(array_column($decoded, 'title'), 0, 2);
                    $preview = implode(', ', $titles);
                    if ($count > 2) $preview .= '...';
                    return "📄 Gallery Items ({$count}): {$preview}";
                }

                // Generic array of objects
                $keys = array_keys($firstItem);
                $preview = implode(', ', array_slice($keys, 0, 3));
                if (count($keys) > 3) $preview .= '...';
                return "📄 Structured data ({$count} items): {$preview}";
            } else {
                // Simple key-value pairs
                $keys = array_keys($decoded);
                $preview = implode(', ', array_slice($keys, 0, 3));
                if (count($keys) > 3) $preview .= '...';
                return "📄 Data ({$count} keys): {$preview}";
            }
        }

        return '📄 JSON Object';
    }

    // Helper method to convert JSON to key-value pairs for editing
    protected static function jsonToKeyValue($json): array
    {
        if (!is_array($json)) return [];

        $result = [];

        // Handle array of objects (like face_meanings, approach_items)
        if (isset($json[0]) && is_array($json[0])) {
            foreach ($json as $index => $item) {
                foreach ($item as $key => $value) {
                    $result["{$index}_{$key}"] = is_array($value) ? json_encode($value) : $value;
                }
            }
        } else {
            // Handle simple key-value pairs
            foreach ($json as $key => $value) {
                $result[$key] = is_array($value) ? json_encode($value) : $value;
            }
        }

        return $result;
    }

    // Helper method to convert key-value pairs back to JSON
    protected static function keyValueToJson($kvPairs, $structure = 'object'): string
    {
        if (empty($kvPairs)) return '{}';

        $result = [];

        // Group by index if we detect numbered keys (like 0_letter, 0_word, 1_letter, 1_word)
        $grouped = [];
        foreach ($kvPairs as $key => $value) {
            if (preg_match('/^(\d+)_(.+)$/', $key, $matches)) {
                $index = $matches[1];
                $field = $matches[2];
                $grouped[$index][$field] = $value;
            } else {
                $result[$key] = $value;
            }
        }

        // If we have grouped items, create array of objects
        if (!empty($grouped)) {
            $result = array_values($grouped);
        }

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    // Helper method to process JSON content during form submission
    protected static function processJsonContent($get): string
    {
        $repeaterContent = $get('json_content');
        $simpleContent = $get('json_simple');
        $textareaContent = $get('json_textarea_content');

        // Prefer textarea content if provided (for complex structures)
        if (!empty($textareaContent)) {
            return $textareaContent;
        }

        // Use repeater content for structured data (face_meanings, approach_items, etc.)
        if (!empty($repeaterContent) && is_array($repeaterContent)) {
            return json_encode($repeaterContent, JSON_UNESCAPED_UNICODE);
        }

        // Use simple key-value pairs for basic objects
        if (!empty($simpleContent) && is_array($simpleContent)) {
            return json_encode($simpleContent, JSON_UNESCAPED_UNICODE);
        }

        return '{}';
    }
}
