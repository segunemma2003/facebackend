<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HomepageContentResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;

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
                // Tables\Actions\ViewAction::make()
                //     ->modalContent(fn ($record) => view('filament.pages.content-preview', compact('record'))),

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
                                            <h4 class="font-semibold text-blue-900 mb-2">📸 How to copy image URLs from Curator:</h4>
                                            <ol class="list-decimal list-inside space-y-1 text-blue-800 text-sm">
                                                <li><strong>Open Gallery:</strong> Click "Open Curator Gallery" below</li>
                                                <li><strong>Right-click image:</strong> Right-click any image and select "Copy image address"</li>
                                                <li><strong>OR Edit image:</strong> Click edit button and copy the file URL from the form</li>
                                                <li><strong>Paste URL:</strong> Paste the full URL into image fields below</li>
                                            </ol>
                                            <p class="text-xs text-blue-600 mt-2">💡 <strong>URL format:</strong> https://your-domain.com/storage/media/filename.jpg</p>
                                        </div>
                                    ')),

                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('open_gallery')
                                        ->label('🖼️ Open Curator Gallery')
                                        ->color('primary')
                                        ->url('/admin/curator/media', shouldOpenInNewTab: true)
                                        ->icon('heroicon-o-photo'),
                                ])
                                    ->alignCenter()
                                    ->columnSpanFull(),
                            ])
                            ->visible(fn (Forms\Get $get) => $get('type') === 'json')
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

                        // Image URL input (replaced FileUpload)
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
                                    ->url('/admin/curator/media', shouldOpenInNewTab: true)
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
                            ->itemLabel(fn (array $state, Forms\Get $get): ?string =>
                                static::getJsonItemLabel($state, $get('key'))
                            )
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if (empty($state)) {
                                    $set('content', '[]');
                                    return;
                                }

                                // Convert repeater data to JSON - ensure it's always an array
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
                                            // Handle both array of objects and simple objects
                                            if (isset($decoded[0]) && is_array($decoded[0])) {
                                                // Already an array of objects
                                                $component->state($decoded);
                                            } else {
                                                // Simple object, wrap in array for repeater
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
            'index' => Pages\ListHomepageContents::route('/'),
            'create' => Pages\CreateHomepageContent::route('/create'),
            'edit' => Pages\EditHomepageContent::route('/{record}/edit'),
        ];
    }

    // Helper method to get JSON label based on key
    protected static function getJsonLabel(string $key): string
    {
        return match ($key) {
            'face_meanings' => 'FACE Meanings',
            'approach_items' => 'Approach Items',
            'ticket_info' => 'Ticket Information',
            'gallery_items' => 'Gallery Items',
            'testimonials' => 'Winner Testimonials',
            'event_schedule' => 'Event Schedule',
            'team_members' => 'Team Members',
            default => 'JSON Data'
        };
    }

    // Helper method to get JSON helper text based on key
    protected static function getJsonHelperText(string $key): string
    {
        return match ($key) {
            'face_meanings' => 'Add the meanings for each letter in FACE (Focus, Authenticity, Community, Excellence)',
            'approach_items' => 'Add items that describe your approach or methodology',
            'ticket_info' => 'Add different ticket types with pricing and descriptions',
            'gallery_items' => 'Add images for the gallery section with titles and descriptions. Use Curator gallery for image URLs.',
            'testimonials' => 'Add testimonials from past winners or participants. Use Curator gallery for profile images.',
            'event_schedule' => 'Add events with times, titles and descriptions for the ceremony schedule',
            'team_members' => 'Add organizing team members with their roles and contact information. Use Curator gallery for profile images.',
            default => 'Build your structured data by adding items with the form fields below'
        };
    }

    // Helper method to get JSON schema based on key - CURATOR URL VERSION
    protected static function getJsonSchema(string $key): array
    {
        return match ($key) {
            'face_meanings' => [
                Forms\Components\Grid::make(3)->schema([
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
                ]),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->required()
                    ->rows(2)
                    ->placeholder('The unwavering commitment to vision and purpose'),
            ],
            'approach_items' => [
                Forms\Components\Grid::make(3)->schema([
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
                ]),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->required()
                    ->rows(3)
                    ->placeholder('Recognizing excellence worldwide...'),
            ],
            'ticket_info' => [
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('type')
                        ->label('Ticket Type')
                        ->required()
                        ->placeholder('Standard Attendance'),
                    Forms\Components\TextInput::make('price')
                        ->label('Price')
                        ->required()
                        ->placeholder('$250'),
                ]),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->required()
                    ->rows(2)
                    ->placeholder('General admission with dinner'),
            ],
            'gallery_items' => [
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('Image Title')
                        ->required(),
                    Forms\Components\TextInput::make('category')
                        ->label('Category')
                        ->placeholder('Awards, Events, etc.'),
                ]),
                Forms\Components\TextInput::make('image_url')
                    ->label('Image URL')
                    ->url()
                    ->required()
                    ->placeholder('https://your-domain.com/storage/media/gallery-image.jpg')
                    ->helperText('Copy image URL from Curator gallery')
                    ->suffixAction(
                        Forms\Components\Actions\Action::make('gallery')
                            ->icon('heroicon-o-photo')
                            ->url('/admin/curator/media', shouldOpenInNewTab: true)
                            ->tooltip('Open Curator Gallery')
                    ),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->rows(2),
            ],
            'testimonials' => [
                Forms\Components\Grid::make(3)->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Name')
                        ->required(),
                    Forms\Components\TextInput::make('title')
                        ->label('Title/Position')
                        ->required(),
                    Forms\Components\TextInput::make('company')
                        ->label('Company'),
                ]),
                Forms\Components\Textarea::make('quote')
                    ->label('Quote')
                    ->required()
                    ->rows(3),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('image')
                        ->label('Profile Image URL')
                        ->url()
                        ->placeholder('https://your-domain.com/storage/media/profile.jpg')
                        ->helperText('Copy image URL from Curator gallery')
                        ->suffixAction(
                            Forms\Components\Actions\Action::make('gallery')
                                ->icon('heroicon-o-photo')
                                ->url('/admin/curator/media', shouldOpenInNewTab: true)
                                ->tooltip('Open Curator Gallery')
                        ),
                    Forms\Components\TextInput::make('rating')
                        ->label('Rating (1-5)')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(5),
                ]),
            ],
            'event_schedule' => [
                Forms\Components\Grid::make(3)->schema([
                    Forms\Components\TextInput::make('time')
                        ->label('Time')
                        ->required()
                        ->placeholder('7:00 PM'),
                    Forms\Components\TextInput::make('title')
                        ->label('Event Title')
                        ->required()
                        ->columnSpan(2),
                ]),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\Textarea::make('description')
                        ->label('Description')
                        ->rows(2),
                    Forms\Components\TextInput::make('location')
                        ->label('Location'),
                ]),
            ],
            'team_members' => [
                Forms\Components\Grid::make(3)->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Name')
                        ->required(),
                    Forms\Components\TextInput::make('position')
                        ->label('Position')->required(),
                   Forms\Components\TextInput::make('department')
                       ->label('Department'),
               ]),
               Forms\Components\Textarea::make('bio')
                   ->label('Bio')
                   ->rows(3),
               Forms\Components\Grid::make(3)->schema([
                   Forms\Components\TextInput::make('image')
                       ->label('Profile Image URL')
                       ->url()
                       ->placeholder('https://your-domain.com/storage/media/team-member.jpg')
                       ->helperText('Copy image URL from Curator gallery')
                       ->suffixAction(
                           Forms\Components\Actions\Action::make('gallery')
                               ->icon('heroicon-o-photo')
                               ->url('/admin/curator/media', shouldOpenInNewTab: true)
                               ->tooltip('Open Curator Gallery')
                       ),
                   Forms\Components\TextInput::make('email')
                       ->label('Email')
                       ->email(),
                   Forms\Components\TextInput::make('linkedin')
                       ->label('LinkedIn URL')
                       ->url(),
               ]),
           ],
           // Generic fallback for unknown keys
           default => [
               Forms\Components\Grid::make(2)->schema([
                   Forms\Components\TextInput::make('title')
                       ->label('Title')
                       ->required(),
                   Forms\Components\TextInput::make('value')
                       ->label('Value'),
               ]),
               Forms\Components\Textarea::make('description')
                   ->label('Description')
                   ->rows(2),
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
           'gallery_items' => ($state['title'] ?? 'New Gallery Item') . ($state['image_url'] ? ' 🖼️' : ''),
           'testimonials' => ($state['name'] ?? 'New Testimonial') . ($state['company'] ? ' - ' . $state['company'] : '') . ($state['image'] ? ' 🖼️' : ''),
           'event_schedule' => ($state['time'] ?? '') . ($state['title'] ? ' - ' . $state['title'] : 'New Event'),
           'team_members' => ($state['name'] ?? 'New Team Member') . ($state['position'] ? ' - ' . $state['position'] : '') . ($state['image'] ? ' 🖼️' : ''),
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
}
