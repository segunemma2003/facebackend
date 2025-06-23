<?php
// app/Filament/Resources/ProjectsContentResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectsContentResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class ProjectsContentResource extends BasePageContentResource
{
    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    protected static ?string $navigationLabel = 'Projects Content';

    protected static ?int $navigationSort = 8;

    protected static function getPageName(): string
    {
        return 'projects';
    }

    protected static function getPageSections(): array
    {
        return [
            'hero' => 'Hero Section',
            'introduction' => 'Introduction Section',
            'for_homeless' => 'For the Homeless Projects',
            'for_women' => 'For Women Projects',
            'farming_food_justice' => 'Farming & Food Justice Projects',
            'social_justice' => 'Social Justice Projects',
            'call_to_action' => 'Call to Action Section'
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
            ->emptyStateHeading('No Projects Content')
            ->emptyStateDescription('Create your first projects content item to get started.')
            ->emptyStateIcon('heroicon-o-home-modern');
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
                                    'focus_areas' => 'Focus Areas (JSON)'
                                ],
                                'introduction' => [
                                    'title' => 'Section Title',
                                    'content' => 'Section Content',
                                    'focus_cards' => 'Focus Cards (JSON)'
                                ],
                                'for_homeless' => [
                                    'title' => 'Section Title',
                                    'subtitle' => 'Section Subtitle',
                                    'projects' => 'Homeless Projects (JSON)'
                                ],
                                'for_women' => [
                                    'title' => 'Section Title',
                                    'subtitle' => 'Section Subtitle',
                                    'projects' => 'Women Projects (JSON)'
                                ],
                                'farming_food_justice' => [
                                    'title' => 'Section Title',
                                    'subtitle' => 'Section Subtitle',
                                    'projects' => 'Farming & Food Justice Projects (JSON)'
                                ],
                                'social_justice' => [
                                    'title' => 'Section Title',
                                    'subtitle' => 'Section Subtitle',
                                    'projects' => 'Social Justice Projects (JSON)'
                                ],
                                'call_to_action' => [
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
                                'json' => 'Use for structured data like project arrays and focus areas',
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
                                        ->url('/admin/media', shouldOpenInNewTab: true)
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
            'index' => Pages\ListProjectsContents::route('/'),
            'create' => Pages\CreateProjectsContent::route('/create'),
            'edit' => Pages\EditProjectsContent::route('/{record}/edit'),
        ];
    }

    // Helper method to get JSON label based on key
    protected static function getJsonLabel(string $key): string
    {
        return match ($key) {
            'focus_areas' => 'Focus Areas',
            'focus_cards' => 'Focus Cards',
            'projects' => 'Projects Data',
            default => 'JSON Data'
        };
    }

    // Helper method to get JSON helper text based on key
    protected static function getJsonHelperText(string $key): string
    {
        return match ($key) {
            'focus_areas' => 'Add focus area badges with icons and labels for the hero section',
            'focus_cards' => 'Add focus area cards with icons, titles, and descriptions',
            'projects' => 'Add project details including costs, impact metrics, and timeline information',
            default => 'Build your structured data by adding items with the form fields below'
        };
    }

    // Helper method to get JSON schema based on key - Projects specific schemas
    protected static function getJsonSchema(string $key): array
    {
        return match ($key) {
            'focus_areas' => [
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('icon')
                        ->label('Icon')
                        ->required()
                        ->placeholder('users')
                        ->helperText('Icon name (e.g., users, heart, sprout)'),
                    Forms\Components\TextInput::make('label')
                        ->label('Label')
                        ->required()
                        ->placeholder('Community Impact'),
                ]),
            ],
            'focus_cards' => [
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('icon')
                        ->label('Icon')
                        ->required()
                        ->placeholder('users')
                        ->helperText('Icon name (e.g., users, heart, sprout, scale)'),
                    Forms\Components\TextInput::make('title')
                        ->label('Title')
                        ->required()
                        ->placeholder('For the Homeless'),
                ]),
                Forms\Components\TextInput::make('description')
                    ->label('Description')
                    ->required()
                    ->placeholder('Mobile services and transitional housing'),
            ],
            'projects' => [
                Forms\Components\Grid::make(3)->schema([
                    Forms\Components\TextInput::make('id')
                        ->label('ID')
                        ->numeric()
                        ->required()
                        ->placeholder('1'),
                    Forms\Components\TextInput::make('icon')
                        ->label('Icon')
                        ->required()
                        ->placeholder('truck')
                        ->helperText('Icon name (e.g., truck, home, shield, sprout)'),
                    Forms\Components\TextInput::make('status')
                        ->label('Status')
                        ->required()
                        ->placeholder('Planning')
                        ->helperText('e.g., Planning, Active, Completed'),
                ]),
                Forms\Components\TextInput::make('title')
                    ->label('Project Title')
                    ->required()
                    ->placeholder('Mobile Shower and Hygiene Unit')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->required()
                    ->rows(3)
                    ->placeholder('A mobile truck or trailer equipped with showers, toilets, and hygiene supplies.')
                    ->columnSpanFull(),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('estimated_cost')
                        ->label('Estimated Cost')
                        ->required()
                        ->placeholder('$75,000–$120,000 (initial setup); $5,000/month (operational)'),
                    Forms\Components\TextInput::make('impact')
                        ->label('Impact')
                        ->required()
                        ->placeholder('Serves 100–200 people per week'),
                ]),
                Forms\Components\TextInput::make('timeline')
                    ->label('Timeline')
                    ->required()
                    ->placeholder('2025')
                    ->helperText('e.g., 2025, 2025-2026'),
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
            'focus_areas' => 'Add Focus Area',
            'focus_cards' => 'Add Focus Card',
            'projects' => 'Add Project',
            default => 'Add Item'
        };
    }

    protected static function getJsonItemLabel(array $state, string $key): ?string
    {
        return match ($key) {
            'focus_areas' => ($state['icon'] ?? '') . ' - ' . ($state['label'] ?? 'New Focus Area'),
            'focus_cards' => ($state['icon'] ?? '') . ' - ' . ($state['title'] ?? 'New Focus Card'),
            'projects' => 'Project ' . ($state['id'] ?? '#') . ': ' . ($state['title'] ?? 'New Project') . ' (' . ($state['status'] ?? 'Status') . ')',
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

                if (isset($firstItem['icon'], $firstItem['label'])) {
                    // Focus areas
                    $labels = array_column($decoded, 'label');
                    $preview = implode(', ', array_slice($labels, 0, 2));
                    if ($count > 2) $preview .= '...';
                    return "📄 Focus Areas ({$count}): {$preview}";
                }

                if (isset($firstItem['icon'], $firstItem['title'], $firstItem['description'])) {
                    // Focus cards or projects
                    $titles = array_column($decoded, 'title');
                    $preview = implode(', ', array_slice($titles, 0, 2));
                    if ($count > 2) $preview .= '...';

                    if (isset($firstItem['estimated_cost'])) {
                        return "📄 Projects ({$count}): {$preview}";
                    } else {
                        return "📄 Focus Cards ({$count}): {$preview}";
                    }
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
