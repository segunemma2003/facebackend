<?php
// app/Filament/Resources/GalleryContentResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\GalleryContentResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class GalleryContentResource extends BasePageContentResource
{
    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = 'Gallery Page';

    protected static ?int $navigationSort = 5;

         protected static bool $shouldRegisterNavigation = false;

    protected static function getPageName(): string
    {
        return 'gallery';
    }

    protected static function getPageSections(): array
    {
        return [
            'hero' => 'Hero Section',
            'year_selector' => 'Year Selector',
            'gallery_content' => 'Gallery Content',
            'loading_states' => 'Loading States',
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
            ->emptyStateHeading('No Gallery Page Content')
            ->emptyStateDescription('Create your first gallery page content item to get started.')
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
                                'year_selector' => [
                                    'events_suffix' => 'Events Suffix Text',
                                    'select_year_text' => 'Select Year Text',
                                    'all_years_text' => 'All Years Option Text',
                                    'year_filter_label' => 'Year Filter Label',
                                    'no_years_text' => 'No Years Available Text'
                                ],
                                'gallery_content' => [
                                    'no_images_title' => 'No Images Title',
                                    'no_images_message' => 'No Images Message',
                                    'no_events_title' => 'No Events Title',
                                    'no_events_message_with_year' => 'No Events Message (With Year)',
                                    'no_events_message_general' => 'No Events Message (General)',
                                    'image_counter_text' => 'Image Counter Text',
                                    'view_image_text' => 'View Image Text',
                                    'close_image_text' => 'Close Image Text',
                                    'previous_image_text' => 'Previous Image Text',
                                    'next_image_text' => 'Next Image Text',
                                    'download_image_text' => 'Download Image Text'
                                ],
                                'loading_states' => [
                                    'loading_gallery_text' => 'Loading Gallery Text',
                                    'failed_to_load_text' => 'Failed to Load Text',
                                    'try_again_button_text' => 'Try Again Button Text',
                                    'loading_images_text' => 'Loading Images Text',
                                    'loading_events_text' => 'Loading Events Text'
                                ],
                                'call_to_action' => [
                                    'title' => 'CTA Title',
                                    'subtitle' => 'CTA Subtitle',
                                    'description' => 'CTA Description',
                                    'primary_button_text' => 'Primary Button Text',
                                    'primary_button_url' => 'Primary Button URL',
                                    'secondary_button_text' => 'Secondary Button Text',
                                    'secondary_button_url' => 'Secondary Button URL',
                                    'background_image' => 'Background Image'
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
                                            <code class="text-xs bg-white px-2 py-1 rounded border">https://your-domain.com/storage/media/gallery/event-photo.jpg</code>
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
                            ->placeholder('https://your-domain.com/storage/media/gallery/image.jpg')
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
                                    return new HtmlString('');
                                }
                                $content = $record->getRawOriginal('content');
                                if ($content) {
                                    return new HtmlString('<img src="' . htmlspecialchars($content) . '" alt="Current image" style="max-width: 200px; max-height: 150px; border-radius: 8px; border: 1px solid #e5e7eb;">');
                                }
                                return new HtmlString('<p class="text-gray-500 text-sm">No image set</p>');
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
                       Forms\Components\Section::make('Additional Properties')
                    ->schema([
                        // Meta data with user-friendly repeater
                        Forms\Components\Hidden::make('meta'),

                        Forms\Components\Repeater::make('meta_input')
                            ->label('Meta Properties')
                            ->schema([
                                Forms\Components\Select::make('property')
                                    ->label('Property Type')
                                    ->options([
                                        'alt_text' => 'Alt Text (Images)',
                                        'caption' => 'Caption',
                                        'description' => 'Description',
                                        'tooltip' => 'Tooltip Text',
                                        'aria_label' => 'Accessibility Label',
                                        'css_class' => 'CSS Class',
                                        'target' => 'Link Target',
                                        'rel' => 'Link Relationship',
                                        'custom' => 'Custom Property',
                                    ])
                                    ->required()
                                    ->live()
                                    ->searchable(),

                                Forms\Components\TextInput::make('custom_property')
                                    ->label('Custom Property Name')
                                    ->visible(fn (Forms\Get $get) => $get('property') === 'custom')
                                    ->required(fn (Forms\Get $get) => $get('property') === 'custom')
                                    ->placeholder('Enter custom property name'),

                                Forms\Components\Textarea::make('value')
                                    ->label('Value')
                                    ->required()
                                    ->rows(2)
                                    ->placeholder(fn (Forms\Get $get) => match($get('property')) {
                                        'alt_text' => 'Descriptive text for screen readers',
                                        'caption' => 'Caption text to display',
                                        'description' => 'Additional description text',
                                        'tooltip' => 'Tooltip text on hover',
                                        'aria_label' => 'Screen reader label',
                                        'css_class' => 'CSS class names (space-separated)',
                                        'target' => '_blank, _self, _parent, _top',
                                        'rel' => 'nofollow, noopener, noreferrer, etc.',
                                        default => 'Enter the property value'
                                    }),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Add Meta Property')
                            ->collapsed()
                            ->cloneable()
                            ->collapsible()
                            ->reorderable()
                            ->deletable()
                            ->itemLabel(function (array $state): ?string {
                                if (empty($state['property'])) return 'New Property';

                                $property = $state['property'] === 'custom' && !empty($state['custom_property'])
                                    ? $state['custom_property']
                                    : ($state['property'] ?? 'Unknown');

                                $value = !empty($state['value']) ? substr($state['value'], 0, 30) : '';
                                if (strlen($state['value'] ?? '') > 30) $value .= '...';

                                return $property . ($value ? ": {$value}" : '');
                            })
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if (empty($state)) {
                                    $set('meta', null);
                                    return;
                                }

                                $metaArray = [];
                                foreach ($state as $item) {
                                    if (empty($item['property']) || empty($item['value'])) continue;

                                    $key = $item['property'] === 'custom' && !empty($item['custom_property'])
                                        ? $item['custom_property']
                                        : $item['property'];

                                    $metaArray[$key] = $item['value'];
                                }

                                $set('meta', empty($metaArray) ? null : $metaArray);
                            })
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if (!$record || empty($record->meta)) {
                                    $component->state([]);
                                    return;
                                }

                                $meta = is_array($record->meta) ? $record->meta : [];
                                $items = [];

                                foreach ($meta as $key => $value) {
                                    $predefinedOptions = [
                                        'alt_text', 'caption', 'description', 'tooltip',
                                        'aria_label', 'css_class', 'target', 'rel'
                                    ];

                                    if (in_array($key, $predefinedOptions)) {
                                        $items[] = [
                                            'property' => $key,
                                            'value' => $value,
                                            'custom_property' => null,
                                        ];
                                    } else {
                                        $items[] = [
                                            'property' => 'custom',
                                            'custom_property' => $key,
                                            'value' => $value,
                                        ];
                                    }
                                }

                                $component->state($items);
                            })
                            ->helperText('Add additional properties like alt text for images, captions, accessibility labels, etc.')
                            ->dehydrated(false),
                    ])
                    ->collapsible()
                    ->collapsed(true),
                    ])
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
            'index' => Pages\ListGalleryContents::route('/'),
            'create' => Pages\CreateGalleryContent::route('/create'),
            'edit' => Pages\EditGalleryContent::route('/{record}/edit'),
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
            'stats_labels' => 'Gallery Statistics Labels',
            'gallery_events' => 'Gallery Events',
            'image_categories' => 'Image Categories',
            'event_details' => 'Event Details',
            'gallery_settings' => 'Gallery Settings',
            default => 'Data Items'
        };
    }

    protected static function getJsonHelperText(string $key): string
    {
        return match ($key) {
            'stats_labels' => 'Add statistics labels for the gallery hero section. Include key, label, and optional suffix.',
            'gallery_events' => 'Add gallery events with titles, descriptions, dates, and image collections.',
            'image_categories' => 'Add image categories for organizing gallery content.',
            'event_details' => 'Add detailed information about gallery events including location, date, and description.',
            'gallery_settings' => 'Add gallery configuration settings like image sizes, layout options, etc.',
            default => 'Add data items for this section.'
        };
    }

    // Helper method to get JSON schema based on key
    protected static function getJsonSchema(string $key): array
    {
        return match ($key) {
            'stats_labels' => [
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\TextInput::make('key')
                            ->label('Statistic Key')
                            ->required()
                            ->placeholder('e.g., events, photos, years')
                            ->helperText('Internal identifier for this statistic'),
                        Forms\Components\TextInput::make('label')
                            ->label('Display Label')
                            ->required()
                            ->placeholder('e.g., Events, Photos, Years'),
                        Forms\Components\TextInput::make('suffix')
                            ->label('Suffix (Optional)')
                            ->placeholder('e.g., +, K, M')
                            ->helperText('Text to append after the number'),
                    ]),
                Forms\Components\TextInput::make('description')
                    ->label('Description (Optional)')
                    ->placeholder('Brief description of this statistic'),
            ],
            'gallery_events' => [
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Event Title')
                            ->required()
                            ->placeholder('e.g., FACE Awards 2023 Ceremony'),
                        Forms\Components\TextInput::make('year')
                            ->label('Event Year')
                            ->numeric()
                            ->required()
                            ->placeholder('2023')
                            ->minValue(2000)
                            ->maxValue(date('Y') + 5),
                    ]),
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('location')
                            ->label('Event Location')
                            ->placeholder('e.g., Lagos, Nigeria'),
                        Forms\Components\DatePicker::make('date')
                            ->label('Event Date')
                            ->displayFormat('M d, Y'),
                    ]),
                Forms\Components\Textarea::make('description')
                    ->label('Event Description')
                    ->rows(3)
                    ->placeholder('Brief description of the event...'),
                Forms\Components\TextInput::make('featured_image')
                    ->label('Featured Image URL')
                    ->url()
                    ->placeholder('https://your-domain.com/storage/media/events/featured.jpg')
                    ->helperText('Copy image URL from Curator gallery')
                    ->suffixAction(
                        Forms\Components\Actions\Action::make('gallery')
                            ->icon('heroicon-o-photo')
                            ->url('/admin/media', shouldOpenInNewTab: true)
                            ->tooltip('Open Curator Gallery')
                    ),
                Forms\Components\TextInput::make('image_count')
                    ->label('Number of Images')
                    ->numeric()
                    ->placeholder('25')
                    ->minValue(0),
            ],
            'image_categories' => [
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Category Name')
                            ->required()
                            ->placeholder('e.g., Ceremony Highlights, Behind the Scenes'),
                        Forms\Components\TextInput::make('slug')
                            ->label('Category Slug')
                            ->placeholder('e.g., ceremony-highlights')
                            ->helperText('URL-friendly version of the name'),
                    ]),
                Forms\Components\Textarea::make('description')
                    ->label('Category Description')
                    ->rows(2)
                    ->placeholder('Description of this image category...'),
                Forms\Components\TextInput::make('icon')
                    ->label('Icon Name')
                    ->placeholder('e.g., camera, star, award'),
                Forms\Components\TextInput::make('order')
                    ->label('Display Order')
                    ->numeric()
                    ->placeholder('1, 2, 3...')
                    ->minValue(1),
            ],
            'event_details' => [
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Event Name')
                            ->required()
                            ->placeholder('e.g., FACE Awards 2023'),
                        Forms\Components\TextInput::make('theme')
                            ->label('Event Theme')
                            ->placeholder('e.g., Excellence in Innovation'),
                    ]),
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('venue')
                            ->label('Venue')
                            ->placeholder('e.g., Grand Ballroom, Lagos Hotel'),
                        Forms\Components\TextInput::make('attendees')
                            ->label('Number of Attendees')
                            ->numeric()
                            ->placeholder('150'),
                    ]),
                Forms\Components\Textarea::make('highlights')
                    ->label('Event Highlights')
                    ->rows(3)
                    ->placeholder('Key highlights and memorable moments from the event...'),
                Forms\Components\TextInput::make('photographer')
                    ->label('Photographer/Team')
                    ->placeholder('e.g., Professional Photography Ltd'),
            ],
            'gallery_settings' => [
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('images_per_page')
                            ->label('Images Per Page')
                            ->numeric()
                            ->placeholder('12')
                            ->minValue(1)
                            ->maxValue(100),
                        Forms\Components\Select::make('layout_style')
                            ->label('Gallery Layout')
                            ->options([
                                'grid' => 'Grid Layout',
                                'masonry' => 'Masonry Layout',
                                'carousel' => 'Carousel Layout',
                                'lightbox' => 'Lightbox Gallery',
                            ])
                            ->placeholder('Select layout style'),
                    ]),
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Toggle::make('enable_download')
                            ->label('Enable Image Downloads')
                            ->default(false),
                        Forms\Components\Toggle::make('show_metadata')
                            ->label('Show Image Metadata')
                            ->default(true),
                    ]),
                Forms\Components\TextInput::make('max_image_size')
                    ->label('Max Image Size (MB)')
                    ->numeric()
                    ->placeholder('5')
                    ->step(0.1)
                    ->minValue(0.1),
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
            'gallery_events' => 'Add Gallery Event',
            'image_categories' => 'Add Image Category',
            'event_details' => 'Add Event Details',
            'gallery_settings' => 'Add Gallery Setting',
            default => 'Add Item'
        };
    }

    protected static function getJsonItemLabel(array $state, string $key): ?string
    {
        return match ($key) {
            'stats_labels' => ($state['key'] ?? 'New Label') . ': ' . ($state['label'] ?? '') . ($state['suffix'] ?? ''),
            'gallery_events' => ($state['year'] ?? '') . ' - ' . ($state['title'] ?? 'New Event') . ' (' . ($state['image_count'] ?? '0') . ' images)',
            'image_categories' => ($state['order'] ?? '') . '. ' . ($state['name'] ?? 'New Category'),
            'event_details' => ($state['name'] ?? 'New Event') . ($state['venue'] ? ' - ' . $state['venue'] : ''),
            'gallery_settings' => ($state['layout_style'] ?? 'Setting') . ': ' . ($state['images_per_page'] ?? $state['max_image_size'] ?? 'New Setting'),
            default => $state['title'] ?? $state['name'] ?? 'New Item'
        };
    }

    // Helper method to format JSON preview in table - specific to gallery content
    protected static function formatJsonPreview($record): string
    {
        $content = $record->getRawOriginal('content');
        if (!$content) return '📄 Empty JSON';

        $decoded = json_decode($content, true);
        if ($decoded === null) return '❌ Invalid JSON';

        if (is_array($decoded)) {
            $count = count($decoded);

            // Handle specific known structures for Gallery page
            if (isset($decoded[0]) && is_array($decoded[0])) {
                $firstItem = $decoded[0];

                if (isset($firstItem['key'], $firstItem['label'])) {
                    // Statistics labels
                    $labels = array_slice(array_column($decoded, 'label'), 0, 2);
                    $preview = implode(', ', $labels);
                    if ($count > 2) $preview .= '...';
                    return "📄 Stats Labels ({$count}): {$preview}";
                }

                if (isset($firstItem['title'], $firstItem['year'])) {
                    // Gallery events
                    $events = array_slice(array_map(fn($item) => $item['year'] . ' - ' . $item['title'], $decoded), 0, 2);
                    $preview = implode(', ', $events);
                    if ($count > 2) $preview .= '...';
                    return "📄 Gallery Events ({$count}): {$preview}";
                }

                if (isset($firstItem['name'], $firstItem['slug'])) {
                    // Image categories
                    $categories = array_slice(array_column($decoded, 'name'), 0, 2);
                    $preview = implode(', ', $categories);
                    if ($count > 2) $preview .= '...';
                    return "📄 Categories ({$count}): {$preview}";
                }

                if (isset($firstItem['name'], $firstItem['venue'])) {
                    // Event details
                    $events = array_slice(array_map(fn($item) => $item['name'] . ' - ' . $item['venue'], $decoded), 0, 2);
                    $preview = implode(', ', $events);
                    if ($count > 2) $preview .= '...';
                    return "📄 Event Details ({$count}): {$preview}";
                }

                if (isset($firstItem['layout_style']) || isset($firstItem['images_per_page'])) {
                    // Gallery settings
                    $settings = array_slice(array_map(fn($item) => $item['layout_style'] ?? $item['images_per_page'] ?? 'Setting', $decoded), 0, 2);
                    $preview = implode(', ', $settings);
                    if ($count > 2) $preview .= '...';
                    return "📄 Gallery Settings ({$count}): {$preview}";
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
