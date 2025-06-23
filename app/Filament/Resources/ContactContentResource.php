<?php
// app/Filament/Resources/ContactContentResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactContentResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

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
            'contact_information' => 'Contact Information',
            'form_messages' => 'Form Messages',
            'map_section' => 'Map Section',
            'faq_cta' => 'FAQ Call to Action'
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
            ->emptyStateHeading('No Contact Page Content')
            ->emptyStateDescription('Create your first contact page content item to get started.')
            ->emptyStateIcon('heroicon-o-envelope');
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
                                    'subtitle' => 'Subtitle',
                                    'background_image' => 'Background Image',
                                    'stats_badges' => 'Stats Badges (JSON)'
                                ],
                                'contact_form' => [
                                    'form_title' => 'Form Title',
                                    'form_subtitle' => 'Form Subtitle',
                                    'first_name_label' => 'First Name Label',
                                    'last_name_label' => 'Last Name Label',
                                    'email_label' => 'Email Label',
                                    'subject_label' => 'Subject Label',
                                    'message_label' => 'Message Label',
                                    'first_name_placeholder' => 'First Name Placeholder',
                                    'last_name_placeholder' => 'Last Name Placeholder',
                                    'email_placeholder' => 'Email Placeholder',
                                    'subject_placeholder' => 'Subject Placeholder',
                                    'message_placeholder' => 'Message Placeholder',
                                    'submit_button_text' => 'Submit Button Text',
                                    'sending_button_text' => 'Sending Button Text'
                                ],
                                'contact_information' => [
                                    'info_title' => 'Information Title',
                                    'info_subtitle' => 'Information Subtitle',
                                    'email_section_title' => 'Email Section Title',
                                    'email_general_label' => 'General Email Label',
                                    'email_nominations_label' => 'Nominations Email Label',
                                    'phone_section_title' => 'Phone Section Title',
                                    'phone_international_label' => 'International Phone Label',
                                    'phone_toll_free_label' => 'Toll Free Phone Label',
                                    'address_section_title' => 'Address Section Title',
                                    'office_hours_section_title' => 'Office Hours Section Title',
                                    'response_time_label' => 'Response Time Label'
                                ],
                                'form_messages' => [
                                    'validation_error_title' => 'Validation Error Title',
                                    'validation_error_message' => 'Validation Error Message',
                                    'success_title' => 'Success Title',
                                    'success_message' => 'Success Message',
                                    'error_title' => 'Error Title',
                                    'error_message' => 'Error Message'
                                ],
                                'map_section' => [
                                    'title' => 'Map Section Title'
                                ],
                                'faq_cta' => [
                                    'title' => 'CTA Title',
                                    'subtitle' => 'CTA Subtitle',
                                    'primary_button_text' => 'Primary Button Text',
                                    'secondary_button_text' => 'Secondary Button Text'
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
                                'json' => 'Use for structured data like stats badges',
                                'boolean' => 'Use for true/false values',
                                'number' => 'Use for numeric values',
                                'image' => 'Enter image URL from Curator gallery',
                                'url' => 'For external links and URLs',
                                'html' => 'Rich text with formatting and spans',
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

                        // Curator Gallery Helper Section
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
                    ]),

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
            'index' => Pages\ListContactContents::route('/'),
            'create' => Pages\CreateContactContent::route('/create'),
            'edit' => Pages\EditContactContent::route('/{record}/edit'),
        ];
    }

    // Helper methods for JSON handling
    protected static function getJsonLabel(string $key): string
    {
        return match ($key) {
            'stats_badges' => 'Stats Badges',
            default => 'Data Items'
        };
    }

    protected static function getJsonHelperText(string $key): string
    {
        return match ($key) {
            'stats_badges' => 'Add stats badges for the hero section. Include icon name and display text.',
            default => 'Add data items for this section.'
        };
    }

    protected static function getJsonSchema(string $key): array
    {
        return match ($key) {
            'stats_badges' => [
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('icon')
                            ->label('Icon Name')
                            ->required()
                            ->placeholder('clock, phone, map-pin, etc.')
                            ->helperText('Heroicon name for the stats badge'),
                        Forms\Components\TextInput::make('text')
                            ->label('Display Text')
                            ->required()
                            ->placeholder('24-48 Hour Response'),
                    ]),
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
            'stats_badges' => 'Add Stats Badge',
            default => 'Add Item'
        };
    }

    protected static function getJsonItemLabel(array $state, string $key): ?string
    {
        return match ($key) {
            'stats_badges' => ($state['icon'] ?? 'New Badge') . ': ' . ($state['text'] ?? ''),
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

                if (isset($firstItem['icon'], $firstItem['text'])) {
                    // Stats badges
                    $badges = array_slice(array_map(fn($item) => $item['icon'] . ': ' . $item['text'], $decoded), 0, 2);
                    $preview = implode(', ', $badges);
                    if ($count > 2) $preview .= '...';
                    return "📄 Stats Badges ({$count}): {$preview}";
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
