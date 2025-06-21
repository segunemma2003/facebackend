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
                                    'button_text' => 'Button Text'
                                ],
                                'past_winners' => [
                                    'title' => 'Section Title',
                                    'subtitle' => 'Section Subtitle',
                                    'content' => 'Section Content',
                                    'empty_state_message' => 'Empty State Message',
                                    'button_text' => 'Button Text'
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
                                    'ticket_info' => 'Ticket Information (JSON)'
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

                        // JSON content as Key-Value pairs
                        Forms\Components\KeyValue::make('json_content')
                            ->label('JSON Data')
                            ->addable(true)
                            ->deletable(true)
                            ->reorderable(true)
                            ->keyLabel('Key')
                            ->valueLabel('Value')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'json')
                            ->required(fn (Forms\Get $get) => $get('type') === 'json')
                            ->helperText(fn (Forms\Get $get) => match ($get('key')) {
                                'face_meanings' => 'Add entries like: letter=F, word=Focus, description=The unwavering commitment...',
                                'approach_items' => 'Add entries like: title=Global Reach, description=..., icon=globe',
                                'ticket_info' => 'Add entries like: type=Standard, price=$250, description=General admission...',
                                default => 'Enter key-value pairs. Complex nested data should be entered as JSON string values.'
                            })
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if (!$record) return;

                                $content = $record->getRawOriginal('content');
                                if (is_string($content)) {
                                    $decoded = json_decode($content, true);
                                    if ($decoded !== null) {
                                        // Convert JSON array to flat key-value pairs
                                        $kvPairs = static::jsonToKeyValue($decoded);
                                        $component->state($kvPairs);
                                    }
                                }
                            })
                            ->dehydrated(false),

                        // Alternative: JSON as textarea for complex structures
                        Forms\Components\Textarea::make('json_textarea_content')
                            ->label('JSON Content (Advanced)')
                            ->rows(12)
                            ->helperText('For complex nested JSON structures. Use the Key-Value editor above for simple data.')
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

    // Helper method to format JSON preview in table
    protected static function formatJsonPreview($record): string
    {
        $content = $record->getRawOriginal('content');
        if (!$content) return '📄 Empty JSON';

        $decoded = json_decode($content, true);
        if ($decoded === null) return '❌ Invalid JSON';

        if (is_array($decoded)) {
            $count = count($decoded);
            $preview = '';

            // Show first few keys for preview
            if (isset($decoded[0]) && is_array($decoded[0])) {
                // Array of objects
                $firstItem = $decoded[0];
                $keys = array_keys($firstItem);
                $preview = implode(', ', array_slice($keys, 0, 3));
                if (count($keys) > 3) $preview .= '...';
            } else {
                // Simple key-value pairs
                $keys = array_keys($decoded);
                $preview = implode(', ', array_slice($keys, 0, 3));
                if (count($keys) > 3) $preview .= '...';
            }

            return "📄 JSON ({$count} items): {$preview}";
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
        $kvContent = $get('json_content');
        $textareaContent = $get('json_textarea_content');

        // Prefer textarea content if provided (for complex structures)
        if (!empty($textareaContent)) {
            return $textareaContent;
        }

        // Otherwise convert key-value pairs to JSON
        if (!empty($kvContent) && is_array($kvContent)) {
            return static::keyValueToJson($kvContent);
        }

        return '{}';
    }
}
