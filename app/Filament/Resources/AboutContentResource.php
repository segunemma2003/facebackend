<?php
// app/Filament/Resources/AboutContentResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\AboutContentResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AboutContentResource extends BasePageContentResource
{
    protected static ?string $navigationIcon = 'heroicon-o-information-circle';

    protected static ?string $navigationLabel = 'About Page';

    protected static ?int $navigationSort = 7;

    protected static function getPageName(): string
    {
        return 'about';
    }

    protected static function getPageSections(): array
    {
        return [
            'hero' => 'Hero Section',
            'our_story' => 'Our Story',
            'success_stories' => 'Success Stories',
            'team' => 'Team Section',
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
                                '🖼️ ' . static::getImageBasename($record->getRawOriginal('content')) :
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
            ->emptyStateHeading('No About Page Content')
            ->emptyStateDescription('Create your first about page content item to get started.')
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
                                    'title' => 'Page Title',
                                    'subtitle' => 'Page Subtitle',
                                    'description' => 'Hero Description',
                                    'background_image' => 'Background Image',
                                    'primary_button_text' => 'Primary Button Text',
                                    'primary_button_url' => 'Primary Button URL',
                                    'secondary_button_text' => 'Secondary Button Text',
                                    'secondary_button_url' => 'Secondary Button URL'
                                ],
                                'our_story' => [
                                    'section_title' => 'Section Title',
                                    'section_subtitle' => 'Section Subtitle',
                                    'story_content' => 'Story Content',
                                    'founding_year' => 'Founding Year',
                                    'mission_statement' => 'Mission Statement',
                                    'vision_statement' => 'Vision Statement',
                                    'values' => 'Core Values (JSON)',
                                    'milestones' => 'Key Milestones (JSON)',
                                    'story_image' => 'Story Image',
                                    'founder_image' => 'Founder Image',
                                    'founder_message' => 'Founder Message',
                                    'founder_name' => 'Founder Name',
                                    'founder_title' => 'Founder Title'
                                ],
                                'success_stories' => [
                                    'section_title' => 'Section Title',
                                    'section_subtitle' => 'Section Subtitle',
                                    'section_description' => 'Section Description',
                                    'stories' => 'Success Stories (JSON)',
                                    'stats' => 'Statistics (JSON)',
                                    'testimonials' => 'Testimonials (JSON)',
                                    'case_studies' => 'Case Studies (JSON)',
                                    'achievements' => 'Achievements (JSON)'
                                ],
                                'team' => [
                                    'section_title' => 'Section Title',
                                    'section_subtitle' => 'Section Subtitle',
                                    'section_description' => 'Section Description',
                                    'team_members' => 'Team Members (JSON)',
                                    'advisors' => 'Advisors (JSON)',
                                    'departments' => 'Departments (JSON)',
                                    'leadership_message' => 'Leadership Message',
                                    'culture_description' => 'Culture Description',
                                    'join_team_cta' => 'Join Team CTA'
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
                        // Hidden field that actually stores the data
                        Forms\Components\Hidden::make('content'),

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

                        // Image upload - using a different field name
                        Forms\Components\FileUpload::make('image_file')
                            ->label('Image Upload')
                            ->image()
                            ->directory('about')
                            ->disk('public')
                            ->visibility('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios(['16:9', '4:3', '1:1'])
                            ->visible(fn (Forms\Get $get) => $get('type') === 'image')
                            ->required(fn (Forms\Get $get) => $get('type') === 'image')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                            ->maxSize(5120)
                            ->multiple(false)
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $filePath = is_array($state) ? $state[0] : $state;
                                    $set('content', $filePath);
                                } else {
                                    $set('content', null);
                                }
                            })
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if (!$record || $record->type !== 'image') {
                                    $component->state([]);
                                    return;
                                }

                                $content = $record->getRawOriginal('content');

                                if (!$content) {
                                    $component->state([]);
                                    return;
                                }

                                $filePath = static::extractFilePath($content);
                                $component->state($filePath ? [$filePath] : []);
                            })
                            ->dehydrated(false),

                        // JSON content
                        Forms\Components\Textarea::make('json_input')
                            ->label('JSON Content')
                            ->rows(8)
                            ->visible(fn (Forms\Get $get) => $get('type') === 'json')
                            ->required(fn (Forms\Get $get) => $get('type') === 'json')
                            ->helperText('Enter valid JSON data')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('content', $state))
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if (!$record || $record->type !== 'json') {
                                    return;
                                }

                                $content = $record->getRawOriginal('content');
                                if (is_string($content)) {
                                    $decoded = json_decode($content, true);
                                    if ($decoded !== null) {
                                        $component->state(json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                                    } else {
                                        $component->state($content);
                                    }
                                } else {
                                    $component->state('{}');
                                }
                            })
                            ->rules([
                                fn (Forms\Get $get) => $get('type') === 'json' ? function ($attribute, $value, $fail) {
                                    if (!empty($value)) {
                                        json_decode($value);
                                        if (json_last_error() !== JSON_ERROR_NONE) {
                                            $fail('The content must be valid JSON: ' . json_last_error_msg());
                                        }
                                    }
                                } : '',
                            ])
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
            'index' => Pages\ListAboutContents::route('/'),
            'create' => Pages\CreateAboutContent::route('/create'),
            'edit' => Pages\EditAboutContent::route('/{record}/edit'),
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
    private static function getImageBasename(string $content): string
    {
        $filePath = static::extractFilePath($content);
        return $filePath ? basename($filePath) : 'Unknown';
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

            // Handle specific known structures for About page
            if (isset($decoded[0]) && is_array($decoded[0])) {
                $firstItem = $decoded[0];

                if (isset($firstItem['name'], $firstItem['position'])) {
                    // Team members
                    $names = array_slice(array_column($decoded, 'name'), 0, 2);
                    $preview = implode(', ', $names);
                    if ($count > 2) $preview .= '...';
                    return "📄 Team Members ({$count}): {$preview}";
                }

                if (isset($firstItem['title'], $firstItem['content'])) {
                    // Stories/testimonials
                    $titles = array_slice(array_column($decoded, 'title'), 0, 2);
                    $preview = implode(', ', $titles);
                    if ($count > 2) $preview .= '...';
                    return "📄 Stories ({$count}): {$preview}";
                }

                if (isset($firstItem['year'], $firstItem['achievement'])) {
                    // Milestones
                    $years = array_slice(array_column($decoded, 'year'), 0, 3);
                    $preview = implode(', ', $years);
                    if ($count > 3) $preview .= '...';
                    return "📄 Milestones ({$count}): {$preview}";
                }

                if (isset($firstItem['label'], $firstItem['value'])) {
                    // Statistics
                    $labels = array_slice(array_column($decoded, 'label'), 0, 2);
                    $preview = implode(', ', $labels);
                    if ($count > 2) $preview .= '...';
                    return "📄 Statistics ({$count}): {$preview}";
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
