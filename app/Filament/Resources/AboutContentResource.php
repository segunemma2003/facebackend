<?php
// app/Filament/Resources/AboutContentResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\AboutContentResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

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
                                            <code class="text-xs bg-white px-2 py-1 rounded border">https://your-domain.com/storage/media/team/john-doe.jpg</code>
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

                                // Convert repeater data to JSON
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
    protected static function getImageBasename(string $content): string
    {
        $filePath = static::extractFilePath($content);
        return $filePath ? basename($filePath) : 'Unknown';
    }

    // User-friendly JSON helper methods
    protected static function getJsonLabel(string $key): string
    {
        return match ($key) {
            'values' => 'Company Core Values',
            'milestones' => 'Company Milestones',
            'stories' => 'Success Stories',
            'stats' => 'Company Statistics',
            'testimonials' => 'Customer Testimonials',
            'case_studies' => 'Case Studies',
            'achievements' => 'Company Achievements',
            'team_members' => 'Team Members',
            'core_team' => 'Core Team Members',
            'advisors' => 'Company Advisors',
            'advisory_board' => 'Advisory Board',
            'departments' => 'Company Departments',
            'contact_info' => 'Contact Information',
            default => 'Data Items'
        };
    }

    protected static function getJsonHelperText(string $key): string
    {
        return match ($key) {
            'values' => 'Add your company\'s core values. Each value should have a title, icon, and description.',
            'milestones' => 'Add important milestones in your company history. Include the year, title, and description.',
            'stories' => 'Add success stories to showcase achievements. Include title, description, and image URL from Curator gallery.',
            'stats' => 'Add company statistics to highlight your achievements. Include label, value, and optional description.',
            'testimonials' => 'Add customer testimonials. Include name, title, company, quote, and optional image URL.',
            'case_studies' => 'Add detailed case studies. Include title, client, challenge, solution, and results.',
            'achievements' => 'Add company achievements and awards. Include title, year, description, and award type.',
            'team_members' => 'Add team member profiles. Include name, title, description, and profile image URL from Curator gallery.',
            'core_team' => 'Add core team member profiles. Include name, title, description, and profile image URL from Curator gallery.',
            'advisors' => 'Add advisory board members. Include name, region, expertise, and profile image URL from Curator gallery.',
            'advisory_board' => 'Add advisory board members. Include name, region, expertise, and profile image URL from Curator gallery.',
            'departments' => 'Add company departments. Include name, head, description, and team size.',
            'contact_info' => 'Add contact information. Include type (email, phone, address), label, and contact details.',
            default => 'Add data items for this section.'
        };
    }

    // Helper method to get JSON schema based on key - CURATOR URL VERSION
    protected static function getJsonSchema(string $key): array
    {
        return match ($key) {
            'values' => [
                Forms\Components\TextInput::make('title')
                    ->label('Value Name')
                    ->required()
                    ->placeholder('e.g., Integrity, Innovation, Excellence'),
                Forms\Components\TextInput::make('icon')
                    ->label('Icon Name')
                    ->placeholder('e.g., shield-check, lightbulb, star')
                    ->helperText('Choose an icon that represents this value'),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->required()
                    ->rows(3)
                    ->placeholder('Describe what this value means to your company...'),
            ],
            'milestones' => [
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('year')
                            ->label('Year')
                            ->required()
                            ->numeric()
                            ->placeholder('2020')
                            ->minValue(1900)
                            ->maxValue(date('Y') + 5),
                        Forms\Components\TextInput::make('title')
                            ->label('Milestone Title')
                            ->required()
                            ->placeholder('e.g., Company Founded, First Award Won'),
                    ]),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->required()
                    ->rows(3)
                    ->placeholder('Describe this important milestone in your company history...'),
            ],
            'stories' => [
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Story Title')
                            ->required()
                            ->placeholder('e.g., EcoTech Solutions Success'),
                        Forms\Components\TextInput::make('award')
                            ->label('Award Name')
                            ->required()
                            ->placeholder('e.g., Technology Innovation Award, 2022'),
                    ]),
                Forms\Components\Textarea::make('description')
                    ->label('Story Description')
                    ->required()
                    ->rows(4)
                    ->placeholder('Tell the complete success story...'),
                Forms\Components\TextInput::make('image')
                    ->label('Story Image URL')
                    ->url()
                    ->placeholder('https://your-domain.com/storage/media/story.jpg')
                    ->helperText('Copy image URL from Curator gallery and paste here')
                    ->suffixAction(
                        Forms\Components\Actions\Action::make('gallery')
                            ->icon('heroicon-o-photo')
                            ->url('/admin/curator/media', shouldOpenInNewTab: true)
                            ->tooltip('Open Curator Gallery')
                    ),
                Forms\Components\TextInput::make('alt')
                    ->label('Image Alt Text')
                    ->placeholder('Alt text for accessibility')
                    ->helperText('Describe the image for screen readers'),
            ],
            'stats' => [
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('label')
                            ->label('Statistic Name')
                            ->required()
                            ->placeholder('e.g., Projects Completed, Happy Clients'),
                        Forms\Components\TextInput::make('value')
                            ->label('Number/Value')
                            ->required()
                            ->placeholder('e.g., 500+, 98%'),
                    ]),Forms\Components\Grid::make(2)
                   ->schema([
                       Forms\Components\TextInput::make('description')
                           ->label('Additional Info')
                           ->placeholder('e.g., Since 2020, This year'),
                       Forms\Components\TextInput::make('icon')
                           ->label('Icon Name')
                           ->placeholder('e.g., trophy, users, chart-bar'),
                   ]),
           ],
           'testimonials' => [
               Forms\Components\Grid::make(3)
                   ->schema([
                       Forms\Components\TextInput::make('name')
                           ->label('Customer Name')
                           ->required()
                           ->placeholder('John Smith'),
                       Forms\Components\TextInput::make('title')
                           ->label('Job Title')
                           ->required()
                           ->placeholder('CEO, Marketing Director'),
                       Forms\Components\TextInput::make('company')
                           ->label('Company Name')
                           ->placeholder('ABC Corporation'),
                   ]),
               Forms\Components\Textarea::make('quote')
                   ->label('Testimonial Quote')
                   ->required()
                   ->rows(4)
                   ->placeholder('What did they say about your company or service?'),
               Forms\Components\Grid::make(2)
                   ->schema([
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
                       Forms\Components\Select::make('rating')
                           ->label('Rating')
                           ->options([
                               '5' => '5 Stars ⭐⭐⭐⭐⭐',
                               '4' => '4 Stars ⭐⭐⭐⭐',
                               '3' => '3 Stars ⭐⭐⭐',
                               '2' => '2 Stars ⭐⭐',
                               '1' => '1 Star ⭐',
                           ])
                           ->default('5'),
                   ]),
           ],
           'case_studies' => [
               Forms\Components\Grid::make(2)
                   ->schema([
                       Forms\Components\TextInput::make('title')
                           ->label('Case Study Title')
                           ->required()
                           ->placeholder('e.g., How We Transformed ABC Corp'),
                       Forms\Components\TextInput::make('client')
                           ->label('Client/Company')
                           ->required()
                           ->placeholder('ABC Corporation'),
                   ]),
               Forms\Components\Textarea::make('challenge')
                   ->label('Challenge')
                   ->required()
                   ->rows(2)
                   ->placeholder('What challenge did the client face?'),
               Forms\Components\Textarea::make('solution')
                   ->label('Solution')
                   ->required()
                   ->rows(2)
                   ->placeholder('How did you solve their problem?'),
               Forms\Components\Textarea::make('results')
                   ->label('Results')
                   ->required()
                   ->rows(2)
                   ->placeholder('What were the outcomes and results?'),
               Forms\Components\TextInput::make('image')
                   ->label('Case Study Image URL')
                   ->url()
                   ->placeholder('https://your-domain.com/storage/media/case-study.jpg')
                   ->helperText('Copy image URL from Curator gallery')
                   ->suffixAction(
                       Forms\Components\Actions\Action::make('gallery')
                           ->icon('heroicon-o-photo')
                           ->url('/admin/curator/media', shouldOpenInNewTab: true)
                           ->tooltip('Open Curator Gallery')
                   ),
           ],
           'achievements' => [
               Forms\Components\Grid::make(2)
                   ->schema([
                       Forms\Components\TextInput::make('title')
                           ->label('Achievement Title')
                           ->required()
                           ->placeholder('e.g., Industry Excellence Award'),
                       Forms\Components\TextInput::make('year')
                           ->label('Year')
                           ->numeric()
                           ->placeholder('2023')
                           ->minValue(1900)
                           ->maxValue(date('Y') + 5),
                   ]),
               Forms\Components\Textarea::make('description')
                   ->label('Description')
                   ->required()
                   ->rows(2)
                   ->placeholder('Describe this achievement and its significance...'),
               Forms\Components\TextInput::make('award_type')
                   ->label('Award Type')
                   ->placeholder('e.g., Industry Recognition, Certification, etc.'),
           ],
           'team_members' => [
               Forms\Components\Grid::make(2)
                   ->schema([
                       Forms\Components\TextInput::make('name')
                           ->label('Full Name')
                           ->required()
                           ->placeholder('Thompson Alade'),
                       Forms\Components\TextInput::make('title')
                           ->label('Job Title')
                           ->required()
                           ->placeholder('Founder & Chairman'),
                   ]),
               Forms\Components\Textarea::make('description')
                   ->label('Description')
                   ->required()
                   ->rows(3)
                   ->placeholder('Leadership expert with 20+ years experience...'),
               Forms\Components\TextInput::make('image')
                   ->label('Profile Photo URL')
                   ->url()
                   ->placeholder('https://your-domain.com/storage/media/team/thompson.jpg')
                   ->helperText('Copy image URL from Curator gallery and paste here')
                   ->suffixAction(
                       Forms\Components\Actions\Action::make('gallery')
                           ->icon('heroicon-o-photo')
                           ->url('/admin/media', shouldOpenInNewTab: true)
                           ->tooltip('Open Curator Gallery')
                   ),
               Forms\Components\TextInput::make('alt')
                   ->label('Image Alt Text')
                   ->placeholder('Thompson Alade - Founder & Chairman')
                   ->helperText('Alt text for accessibility'),
           ],
           'core_team' => [
               Forms\Components\Grid::make(2)
                   ->schema([
                       Forms\Components\TextInput::make('name')
                           ->label('Full Name')
                           ->required()
                           ->placeholder('Thompson Alade'),
                       Forms\Components\TextInput::make('title')
                           ->label('Job Title')
                           ->required()
                           ->placeholder('Founder & Chairman'),
                   ]),
               Forms\Components\Textarea::make('description')
                   ->label('Description')
                   ->required()
                   ->rows(3)
                   ->placeholder('Leadership expert with 20+ years experience...'),
               Forms\Components\TextInput::make('image')
                   ->label('Profile Photo URL')
                   ->url()
                   ->placeholder('https://your-domain.com/storage/media/team/thompson.jpg')
                   ->helperText('Copy image URL from Curator gallery and paste here')
                   ->suffixAction(
                       Forms\Components\Actions\Action::make('gallery')
                           ->icon('heroicon-o-photo')
                           ->url('/admin/curator/media', shouldOpenInNewTab: true)
                           ->tooltip('Open Curator Gallery')
                   ),
               Forms\Components\TextInput::make('alt')
                   ->label('Image Alt Text')
                   ->placeholder('Thompson Alade - Founder & Chairman')
                   ->helperText('Alt text for accessibility'),
           ],
           'advisors' => [
               Forms\Components\Grid::make(2)
                   ->schema([
                       Forms\Components\TextInput::make('name')
                           ->label('Name')
                           ->required()
                           ->placeholder('Dr. Cheng Wei'),
                       Forms\Components\TextInput::make('region')
                           ->label('Region')
                           ->required()
                           ->placeholder('Asia-Pacific Region'),
                   ]),
               Forms\Components\Textarea::make('expertise')
                   ->label('Area of Expertise')
                   ->required()
                   ->rows(2)
                   ->placeholder('Technology Innovation Expert'),
               Forms\Components\TextInput::make('image')
                   ->label('Profile Photo URL')
                   ->url()
                   ->placeholder('https://your-domain.com/storage/media/advisors/cheng-wei.jpg')
                   ->helperText('Copy image URL from Curator gallery and paste here')
                   ->suffixAction(
                       Forms\Components\Actions\Action::make('gallery')
                           ->icon('heroicon-o-photo')
                           ->url('/admin/curator/media', shouldOpenInNewTab: true)
                           ->tooltip('Open Curator Gallery')
                   ),
               Forms\Components\TextInput::make('alt')
                   ->label('Image Alt Text')
                   ->placeholder('Dr. Cheng Wei - Technology Innovation Expert')
                   ->helperText('Alt text for accessibility'),
           ],
           'advisory_board' => [
               Forms\Components\Grid::make(2)
                   ->schema([
                       Forms\Components\TextInput::make('name')
                           ->label('Name')
                           ->required()
                           ->placeholder('Dr. Cheng Wei'),
                       Forms\Components\TextInput::make('region')
                           ->label('Region')
                           ->required()
                           ->placeholder('Asia-Pacific Region'),
                   ]),
               Forms\Components\Textarea::make('expertise')
                   ->label('Area of Expertise')
                   ->required()
                   ->rows(2)
                   ->placeholder('Technology Innovation Expert'),
               Forms\Components\TextInput::make('image')
                   ->label('Profile Photo URL')
                   ->url()
                   ->placeholder('https://your-domain.com/storage/media/advisors/cheng-wei.jpg')
                   ->helperText('Copy image URL from Curator gallery and paste here')
                   ->suffixAction(
                       Forms\Components\Actions\Action::make('gallery')
                           ->icon('heroicon-o-photo')
                           ->url('/admin/curator/media', shouldOpenInNewTab: true)
                           ->tooltip('Open Curator Gallery')
                   ),
               Forms\Components\TextInput::make('alt')
                   ->label('Image Alt Text')
                   ->placeholder('Dr. Cheng Wei - Technology Innovation Expert')
                   ->helperText('Alt text for accessibility'),
           ],
           'departments' => [
               Forms\Components\Grid::make(2)
                   ->schema([
                       Forms\Components\TextInput::make('name')
                           ->label('Department Name')
                           ->required()
                           ->placeholder('e.g., Engineering, Marketing, Sales'),
                       Forms\Components\TextInput::make('head')
                           ->label('Department Head')
                           ->placeholder('Jane Smith'),
                   ]),
               Forms\Components\Textarea::make('description')
                   ->label('Description')
                   ->required()
                   ->rows(2)
                   ->placeholder('What does this department do?'),
               Forms\Components\TextInput::make('team_size')
                   ->label('Team Size')
                   ->numeric()
                   ->placeholder('15')
                   ->minValue(1),
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
                               'other' => 'Other',
                           ]),
                       Forms\Components\TextInput::make('label')
                           ->label('Label')
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
           // Generic fallback for unknown keys
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
           'values' => 'Add Core Value',
           'milestones' => 'Add Milestone',
           'stories' => 'Add Success Story',
           'stats' => 'Add Statistic',
           'testimonials' => 'Add Testimonial',
           'case_studies' => 'Add Case Study',
           'achievements' => 'Add Achievement',
           'team_members' => 'Add Team Member',
           'core_team' => 'Add Core Team Member',
           'advisors' => 'Add Advisor',
           'advisory_board' => 'Add Advisory Board Member',
           'departments' => 'Add Department',
           'contact_info' => 'Add Contact Info',
           default => 'Add Item'
         };
   }

   protected static function getJsonItemLabel(array $state, string $key): ?string
   {
       return match ($key) {
           'values' => $state['title'] ?? 'New Core Value',
           'milestones' => ($state['year'] ?? '') . ' - ' . ($state['title'] ?? 'New Milestone'),
           'stories' => ($state['title'] ?? 'New Success Story') . ($state['image'] ? ' 🖼️' : ''),
           'stats' => ($state['label'] ?? 'New Statistic') . ': ' . ($state['value'] ?? ''),
           'testimonials' => ($state['name'] ?? 'New Testimonial') . ($state['company'] ? ' - ' . $state['company'] : '') . ($state['image'] ? ' 🖼️' : ''),
           'case_studies' => ($state['title'] ?? 'New Case Study') . ($state['client'] ? ' - ' . $state['client'] : '') . ($state['image'] ? ' 🖼️' : ''),
           'achievements' => ($state['year'] ?? '') . ' - ' . ($state['title'] ?? 'New Achievement'),
           'team_members' => ($state['name'] ?? 'New Team Member') . ($state['title'] ? ' - ' . $state['title'] : '') . ($state['image'] ? ' 🖼️' : ''),
           'core_team' => ($state['name'] ?? 'New Team Member') . ($state['title'] ? ' - ' . $state['title'] : '') . ($state['image'] ? ' 🖼️' : ''),
           'advisors' => ($state['name'] ?? 'New Advisor') . ($state['region'] ? ' - ' . $state['region'] : '') . ($state['image'] ? ' 🖼️' : ''),
           'advisory_board' => ($state['name'] ?? 'New Advisor') . ($state['region'] ? ' - ' . $state['region'] : '') . ($state['image'] ? ' 🖼️' : ''),
           'departments' => ($state['name'] ?? 'New Department') . ($state['head'] ? ' - ' . $state['head'] : ''),
           'contact_info' => ($state['type'] ?? 'New Contact') . ': ' . ($state['value'] ?? ''),
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

           // Handle specific known structures for About page
           if (isset($decoded[0]) && is_array($decoded[0])) {
               $firstItem = $decoded[0];

               if (isset($firstItem['title'], $firstItem['icon'])) {
                   // Core values
                   $titles = array_slice(array_column($decoded, 'title'), 0, 2);
                   $preview = implode(', ', $titles);
                   if ($count > 2) $preview .= '...';
                   return "📄 Core Values ({$count}): {$preview}";
               }

               if (isset($firstItem['year'], $firstItem['title'])) {
                   // Milestones or achievements
                   $items = array_slice(array_map(fn($item) => $item['year'] . ' - ' . $item['title'], $decoded), 0, 2);
                   $preview = implode(', ', $items);
                   if ($count > 2) $preview .= '...';
                   return "📄 Timeline ({$count}): {$preview}";
               }

               if (isset($firstItem['name'], $firstItem['title'], $firstItem['description'])) {
                   // Team members (core_team)
                   $names = array_slice(array_column($decoded, 'name'), 0, 2);
                   $preview = implode(', ', $names);
                   if ($count > 2) $preview .= '...';
                   return "📄 Team Members ({$count}): {$preview}";
               }

               if (isset($firstItem['name'], $firstItem['region'], $firstItem['expertise'])) {
                   // Advisory board
                   $names = array_slice(array_column($decoded, 'name'), 0, 2);
                   $preview = implode(', ', $names);
                   if ($count > 2) $preview .= '...';
                   return "📄 Advisors ({$count}): {$preview}";
               }

               if (isset($firstItem['title'], $firstItem['award'])) {
                   // Success stories with award field
                   $titles = array_slice(array_column($decoded, 'title'), 0, 2);
                   $preview = implode(', ', $titles);
                   if ($count > 2) $preview .= '...';
                   return "📄 Success Stories ({$count}): {$preview}";
               }

               if (isset($firstItem['label'], $firstItem['value'])) {
                   // Statistics
                   $stats = array_slice(array_map(fn($item) => $item['label'] . ': ' . $item['value'], $decoded), 0, 2);
                   $preview = implode(', ', $stats);
                   if ($count > 2) $preview .= '...';
                   return "📄 Statistics ({$count}): {$preview}";
               }

               if (isset($firstItem['name'], $firstItem['quote'])) {
                   // Testimonials
                   $names = array_slice(array_column($decoded, 'name'), 0, 2);
                   $preview = implode(', ', $names);
                   if ($count > 2) $preview .= '...';
                   return "📄 Testimonials ({$count}): {$preview}";
               }

               if (isset($firstItem['title'], $firstItem['client'])) {
                   // Case studies
                   $titles = array_slice(array_column($decoded, 'title'), 0, 2);
                   $preview = implode(', ', $titles);
                   if ($count > 2) $preview .= '...';
                   return "📄 Case Studies ({$count}): {$preview}";
               }

               if (isset($firstItem['name'], $firstItem['head'])) {
                   // Departments
                   $names = array_slice(array_column($decoded, 'name'), 0, 2);
                   $preview = implode(', ', $names);
                   if ($count > 2) $preview .= '...';
                   return "📄 Departments ({$count}): {$preview}";
               }

               if (isset($firstItem['type'], $firstItem['value'])) {
                   // Contact info
                   $contacts = array_slice(array_map(fn($item) => $item['type'] . ': ' . $item['value'], $decoded), 0, 2);
                   $preview = implode(', ', $contacts);
                   if ($count > 2) $preview .= '...';
                   return "📄 Contact Info ({$count}): {$preview}";
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
