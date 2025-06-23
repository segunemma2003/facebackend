<?php
// app/Filament/Resources/ApproachContentResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\ApproachContentResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class ApproachContentResource extends BasePageContentResource
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Our Approach Page';

    protected static ?int $navigationSort = 6;

    protected static function getPageName(): string
    {
        return 'approach';
    }

    protected static function getPageSections(): array
    {
        return [
            'hero' => 'Hero Section',
            'introduction' => 'Introduction',
            'process_section' => 'Process Section',
            'process_steps' => 'Process Steps',
            'call_to_action' => 'Call to Action'
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
            ->emptyStateHeading('No Approach Page Content')
            ->emptyStateDescription('Create your first approach page content item to get started.')
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
                                    'main_title' => 'Main Title (HTML)',
                                    'subtitle' => 'Subtitle Text',
                                    'description' => 'Hero Description',
                                    'background_image' => 'Background Image',
                                    'stats_labels' => 'Statistics Labels (JSON)',
                                    'primary_button_text' => 'Primary Button Text',
                                    'primary_button_url' => 'Primary Button URL',
                                    'secondary_button_text' => 'Secondary Button Text',
                                    'secondary_button_url' => 'Secondary Button URL'
                                ],
                                'introduction' => [
                                    'title' => 'Section Title',
                                    'subtitle' => 'Section Subtitle',
                                    'content' => 'Introduction Content (HTML)',
                                    'highlight_text' => 'Highlighted Text',
                                    'image' => 'Introduction Image',
                                    'video_url' => 'Video URL',
                                    'mission_statement' => 'Mission Statement',
                                    'vision_statement' => 'Vision Statement'
                                ],
                                'process_section' => [
                                    'title' => 'Process Section Title',
                                    'subtitle' => 'Process Section Subtitle',
                                    'description' => 'Process Description',
                                    'background_image' => 'Background Image',
                                    'intro_text' => 'Introduction Text'
                                ],
                                'process_steps' => [
                                    'steps_data' => 'Process Steps Data (JSON)',
                                    'steps_title' => 'Steps Section Title',
                                    'steps_subtitle' => 'Steps Section Subtitle',
                                    'additional_info' => 'Additional Information',
                                    'process_image' => 'Process Overview Image'
                                ],
                                'call_to_action' => [
                                    'title' => 'CTA Title',
                                    'subtitle' => 'CTA Subtitle',
                                    'description' => 'CTA Description',
                                    'primary_button_text' => 'Primary Button Text',
                                    'primary_button_url' => 'Primary Button URL',
                                    'secondary_button_text' => 'Secondary Button Text',
                                    'secondary_button_url' => 'Secondary Button URL',
                                    'background_image' => 'Background Image',
                                    'contact_info' => 'Contact Information (JSON)'
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

                                Forms\Components\Placeholder::make('sample_urls')
                                    ->content(new HtmlString('
                                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                            <h5 class="font-medium text-gray-800 mb-2">🔗 Sample Image URL Format:</h5>
                                            <code class="text-xs bg-white px-2 py-1 rounded border">https://your-domain.com/storage/media/approach/process-step.jpg</code>
                                            <p class="text-xs text-gray-600 mt-2">Make sure your image URLs are publicly accessible</p>
                                        </div>
                                    ')),
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

                        // Image URL input
                        Forms\Components\TextInput::make('image_url')
                            ->label('Image URL')
                            ->url()
                            ->visible(fn (Forms\Get $get) => $get('type') === 'image')
                            ->required(fn (Forms\Get $get) => $get('type') === 'image')
                            ->placeholder('https://your-domain.com/storage/media/approach/image.jpg')
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
            'index' => Pages\ListApproachContents::route('/'),
            'create' => Pages\CreateApproachContent::route('/create'),
            'edit' => Pages\EditApproachContent::route('/{record}/edit'),
        ];
    }

    // Helper method to extract file path from various formats
    protected static function extractFilePath($content): ?string
    {
        if (is_string($content)) {
            if (str_starts_with($content, '{')) {
                $decoded = json_decode($content, true);
                if ($decoded && is_array($decoded)) {
                    return array_values($decoded)[0] ?? null;
                }
            } elseif (str_starts_with($content, '[')) {
                $decoded = json_decode($content, true);
                if ($decoded && is_array($decoded) && !empty($decoded)) {
                    return $decoded[0];
                }
            } else {
                return $content;
            }
        }
        return null;
    }

    // Helper method to get image basename for table display
    protected static function getImageBasename(string $content): string
    {
        $filePath = static::extractFilePath($content);
        return $filePath ? basename($filePath) : 'Unknown';
    }

    // User-friendly JSON helper methods
    protected static function getJsonLabel(string $key): string
    {
        return match ($key) {
            'stats_labels' => 'Statistics Labels',
            'steps_data' => 'Process Steps',
            'contact_info' => 'Contact Information',
            'awards_info' => 'Awards Information',
            'methodology_points' => 'Methodology Points',
            'values_list' => 'Core Values',
            'principles' => 'Guiding Principles',
            'features' => 'Key Features',
            default => 'Data Items'
        };
    }

    protected static function getJsonHelperText(string $key): string
    {
        return match ($key) {
            'stats_labels' => 'Add labels for the statistics shown in the hero section. Each should have a key and label.',
            'steps_data' => 'Add detailed information about each step in your approach process. Include title, subtitle, description, image, and other details.',
            'contact_info' => 'Add contact information items with type, label, and contact details.',
            'awards_info' => 'Add information about awards and recognition. Include title, year, description.',
            'methodology_points' => 'Add key points about your methodology. Include title, description, and optional icon.',
            'values_list' => 'Add your organization\'s core values with titles, descriptions, and icons.',
            'principles' => 'Add guiding principles with titles and detailed descriptions.',
            'features' => 'Add key features or highlights with titles, descriptions, and optional images.',
            default => 'Add data items for this section.'
        };
    }

    // Helper method to get JSON schema based on key
    protected static function getJsonSchema(string $key): array
    {
        return match ($key) {
            'stats_labels' => [
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('key')
                            ->label('Statistic Key')
                            ->required()
                            ->placeholder('e.g., award_categories, total_nominees')
                            ->helperText('Internal identifier for this statistic'),
                        Forms\Components\TextInput::make('label')
                            ->label('Display Label')
                            ->required()
                            ->placeholder('e.g., Award Categories, Total Nominees'),
                    ]),
                Forms\Components\TextInput::make('description')
                    ->label('Description (Optional)')
                    ->placeholder('Brief description of this statistic'),
            ],
            'steps_data' => [
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('id')
                            ->label('Step ID')
                            ->numeric()
                            ->required()
                            ->placeholder('1, 2, 3...')
                            ->minValue(1),
                        Forms\Components\TextInput::make('icon')
                            ->label('Icon Name')
                            ->placeholder('e.g., globe, users, trophy')
                            ->helperText('Choose an appropriate icon'),
                    ]),
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Step Title')
                            ->required()
                            ->placeholder('e.g., Global Reach, Local Impact'),
                        Forms\Components\TextInput::make('subtitle')
                            ->label('Step Subtitle')
                            ->required()
                            ->placeholder('e.g., Worldwide Recognition Without Boundaries'),
                    ]),
                Forms\Components\TextInput::make('image')
                    ->label('Step Image URL')
                    ->url()
                    ->placeholder('https://your-domain.com/storage/media/approach/step-1.jpg')
                    ->helperText('Copy image URL from Curator gallery')
                    ->suffixAction(
                        Forms\Components\Actions\Action::make('gallery')
                            ->icon('heroicon-o-photo')
                            ->url('/admin/media', shouldOpenInNewTab: true)
                            ->tooltip('Open Curator Gallery')
                    ),
                Forms\Components\Textarea::make('description')
                    ->label('Step Description')
                    ->required()
                    ->rows(3)
                    ->placeholder('Main description of this step in your approach...'),
                Forms\Components\Textarea::make('details')
                    ->label('Additional Details')
                    ->rows(3)
                    ->placeholder('More detailed information about this step...'),
                Forms\Components\TextInput::make('color')
                    ->label('Color Gradient Classes')
                    ->placeholder('e.g., from-face-sky-blue to-face-sky-blue-light')
                    ->helperText('Tailwind CSS gradient classes for styling'),
            ],
            'contact_info' => [
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('Contact Type')
                            ->required()
                            ->options([
                                'email' => 'Email Address',
                                'phone' => 'Phone Number',
                                'address' => 'Physical Address',
                                'social' => 'Social Media',
                                'website' => 'Website',
                                'other' => 'Other',
                            ]),
                        Forms\Components\TextInput::make('label')
                            ->label('Contact Label')
                            ->required()
                            ->placeholder('e.g., General Inquiries, Support'),
                    ]),
                Forms\Components\TextInput::make('value')
                    ->label('Contact Value')
                    ->required()
                    ->placeholder('e.g., info@company.com, +1-555-123-4567'),
                Forms\Components\TextInput::make('icon')
                    ->label('Icon Name')
                    ->placeholder('e.g., envelope, phone, map-pin'),
            ],
            'methodology_points' => [
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Point Title')
                            ->required()
                            ->placeholder('e.g., Research-Based Approach'),
                        Forms\Components\TextInput::make('icon')
                            ->label('Icon Name')
                            ->placeholder('e.g., search, chart-bar, shield-check'),
                    ]),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->required()
                    ->rows(3)
                    ->placeholder('Detailed description of this methodology point...'),
                Forms\Components\TextInput::make('order')
                    ->label('Display Order')
                    ->numeric()
                    ->placeholder('1, 2, 3...')
                    ->minValue(1),
            ],
            'values_list' => [
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Value Name')
                            ->required()
                            ->placeholder('e.g., Integrity, Innovation, Excellence'),
                        Forms\Components\TextInput::make('icon')
                            ->label('Icon Name')
                            ->placeholder('e.g., shield-check, lightbulb, star'),
                    ]),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->required()
                    ->rows(3)
                    ->placeholder('Describe what this value means to your organization...'),
                Forms\Components\TextInput::make('order')
                    ->label('Display Order')
                    ->numeric()
                    ->placeholder('1, 2, 3...')
                    ->minValue(1),
            ],
            'principles' => [
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Principle Title')
                            ->required()
                            ->placeholder('e.g., Transparency First'),
                        Forms\Components\TextInput::make('icon')
                            ->label('Icon Name (Optional)')
                            ->placeholder('e.g., eye, shield, heart'),
                    ]),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->required()
                    ->rows(4)
                    ->placeholder('Detailed explanation of this guiding principle...'),
                Forms\Components\TextInput::make('order')
                    ->label('Display Order')
                    ->numeric()
                    ->placeholder('1, 2, 3...')
                    ->minValue(1),
            ],
            'features' => [
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Feature Title')
                            ->required()
                            ->placeholder('e.g., Global Recognition'),
                        Forms\Components\TextInput::make('icon')
                            ->label('Icon Name')
                            ->placeholder('e.g., globe, award, users'),
                    ]),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->required()
                    ->rows(3)
                    ->placeholder('Describe this key feature or highlight...'),
                Forms\Components\TextInput::make('image')
                    ->label('Feature Image URL (Optional)')
                    ->url()
                    ->placeholder('https://your-domain.com/storage/media/features/feature.jpg')
                    ->helperText('Copy image URL from Curator gallery')
                    ->suffixAction(
                        Forms\Components\Actions\Action::make('gallery')
                            ->icon('heroicon-o-photo')
                            ->url('/admin/media', shouldOpenInNewTab: true)
                            ->tooltip('Open Curator Gallery')
                    ),
                Forms\Components\TextInput::make('order')
                    ->label('Display Order')
                    ->numeric()
                    ->placeholder('1, 2, 3...')
                    ->minValue(1),
            ],
            // Generic fallback for unknown keys
            default => [
                Forms\Components\TextInput::make('title')
                    ->label('Title')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->rows(3),
                Forms\Components\TextInput::make('order')
                    ->label('Display Order')
                    ->numeric()
                    ->placeholder('1, 2, 3...')
                    ->minValue(1),
            ]
        };
    }

    protected static function getJsonAddLabel(string $key): string
    {
        return match ($key) {
            'stats_labels' => 'Add Statistics Label',
            'steps_data' => 'Add Process Step',
            'contact_info' => 'Add Contact Info',
            'awards_info' => 'Add Award Info',
            'methodology_points' => 'Add Methodology Point',
            'values_list' => 'Add Core Value',
            'principles' => 'Add Guiding Principle',
            'features' => 'Add Key Feature',
            default => 'Add Item'
        };
    }

    protected static function getJsonItemLabel(array $state, string $key): ?string
    {
        return match ($key) {
            'stats_labels' => ($state['key'] ?? 'New Label') . ': ' . ($state['label'] ?? ''),
            'steps_data' => 'Step ' . ($state['id'] ?? '?') . ': ' . ($state['title'] ?? 'New Step') . ($state['image'] ? ' 🖼️' : ''),
            'contact_info' => ($state['type'] ?? 'New Contact') . ': ' . ($state['value'] ?? ''),
            'awards_info' => ($state['year'] ?? '') . ' - ' . ($state['title'] ?? 'New Award'),
            'methodology_points' => ($state['order'] ?? '') . '. ' . ($state['title'] ?? 'New Point'),
            'values_list' => ($state['order'] ?? '') . '. ' . ($state['title'] ?? 'New Value'),
            'principles' => ($state['order'] ?? '') . '. ' . ($state['title'] ?? 'New Principle'),
            'features' => ($state['order'] ?? '') . '. ' . ($state['title'] ?? 'New Feature') . ($state['image'] ? ' 🖼️' : ''),
            default => $state['title'] ?? $state['name'] ?? 'New Item'
        };
    }

    // Helper method to format JSON preview in table - specific to approach content
    protected static function formatJsonPreview($record): string
    {
        $content = $record->getRawOriginal('content');
        if (!$content) return '📄 Empty JSON';

        $decoded = json_decode($content, true);
        if ($decoded === null) return '❌ Invalid JSON';

        if (is_array($decoded)) {
            $count = count($decoded);

            // Handle specific known structures for Approach page
            if (isset($decoded[0]) && is_array($decoded[0])) {
                $firstItem = $decoded[0];

                if (isset($firstItem['key'], $firstItem['label'])) {
                    // Statistics labels
                    $labels = array_slice(array_column($decoded, 'label'), 0, 2);
                    $preview = implode(', ', $labels);
                    if ($count > 2) $preview .= '...';
                    return "📄 Stats Labels ({$count}): {$preview}";
                }

                if (isset($firstItem['id'], $firstItem['title'], $firstItem['description'])) {
                    // Process steps
                    $titles = array_slice(array_column($decoded, 'title'), 0, 2);
                    $preview = implode(', ', $titles);
                    if ($count > 2) $preview .= '...';
                    return "📄 Process Steps ({$count}): {$preview}";
                }

                if (isset($firstItem['type'], $firstItem['value'])) {
                    // Contact info
                    $contacts = array_slice(array_map(fn($item) => $item['type'] . ': ' . $item['value'], $decoded), 0, 2);
                    $preview = implode(', ', $contacts);
                    if ($count > 2) $preview .= '...';
                    return "📄 Contact Info ({$count}): {$preview}";
                }

                if (isset($firstItem['title'], $firstItem['description']) && isset($firstItem['order'])) {
                    // Methodology points, values, principles, or features
                    $titles = array_slice(array_column($decoded, 'title'), 0, 2);
                    $preview = implode(', ', $titles);
                    if ($count > 2) $preview .= '...';
                    return "📄 Structured Items ({$count}): {$preview}";
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
