<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NomineeResource\Pages;
use App\Filament\Resources\NomineeResource\RelationManagers;
use App\Models\Nominee;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Illuminate\Database\Eloquent\Builder;

class NomineeResource extends Resource
{
    protected static ?string $model = Nominee::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Awards Management';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Nominee Information')
                    ->tabs([
                        Tabs\Tab::make('Basic Information')
                            ->schema([
                                Forms\Components\Select::make('category_id')
                                    ->label('Category')
                                    ->options(Category::active()->pluck('name', 'id'))
                                    ->required()
                                    ->searchable(),
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('organization')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('position')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('location')
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('description')
                                    ->required()
                                    ->rows(3)
                                    ->columnSpanFull(),
                                Forms\Components\Textarea::make('long_bio')
                                    ->label('Long Biography')
                                    ->rows(5)
                                    ->columnSpanFull(),
                                Forms\Components\Textarea::make('impact_summary')
                                    ->label('Impact Summary')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ]),
                        Tabs\Tab::make('Media & Links')
                            ->schema([
                                Forms\Components\FileUpload::make('profile_image')
                                    ->label('Profile Image')
                                    ->image()
                                    ->imageEditor()
                                    ->imageEditorAspectRatios([
                                        '1:1',
                                        '4:3',
                                        '16:9',
                                    ])
                                    ->directory('nominees/profiles')
                                    ->disk('public')
                                    ->visibility('public')
                                    ->maxSize(3072) // 3MB
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                                    ->helperText('Upload a profile photo (recommended: square 400x400px)')
                                    ->columnSpanFull(),

                                Forms\Components\FileUpload::make('cover_image')
                                    ->label('Cover Image')
                                    ->image()
                                    ->imageEditor()
                                    ->imageEditorAspectRatios([
                                        '16:9',
                                        '21:9',
                                        '4:3',
                                    ])
                                    ->directory('nominees/covers')
                                    ->disk('public')
                                    ->visibility('public')
                                    ->maxSize(5120) // 5MB
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                                    ->helperText('Upload a cover image (recommended: 1200x675px)')
                                    ->columnSpanFull(),

                                Forms\Components\FileUpload::make('gallery_images')
                                    ->label('Gallery Images')
                                    ->image()
                                    ->multiple()
                                    ->reorderable()
                                    ->maxFiles(10)
                                    ->directory('nominees/gallery')
                                    ->disk('public')
                                    ->visibility('public')
                                    ->maxSize(3072) // 3MB per image
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                                    ->helperText('Upload additional gallery images (max 10 images)')
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('image_url')
                                    ->label('Fallback Profile Image URL')
                                    ->url()
                                    ->helperText('Only used if no profile image is uploaded')
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('cover_image_url')
                                    ->label('Fallback Cover Image URL')
                                    ->url()
                                    ->helperText('Only used if no cover image is uploaded')
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('video_url')
                                    ->label('Video URL')
                                    ->url()
                                    ->helperText('YouTube, Vimeo, or direct video link')
                                    ->columnSpanFull(),

                                Forms\Components\Repeater::make('social_links')
                                    ->schema([
                                        Forms\Components\Select::make('platform')
                                            ->options([
                                                'twitter' => 'Twitter',
                                                'linkedin' => 'LinkedIn',
                                                'facebook' => 'Facebook',
                                                'instagram' => 'Instagram',
                                                'website' => 'Website',
                                                'youtube' => 'YouTube',
                                                'github' => 'GitHub',
                                            ])
                                            ->required(),
                                        Forms\Components\TextInput::make('url')
                                            ->url()
                                            ->required(),
                                    ])
                                    ->columns(2)
                                    ->addActionLabel('Add Social Link')
                                    ->columnSpanFull(),
                            ]),
                        Tabs\Tab::make('Voting & Status')
                            ->schema([
                                Forms\Components\TextInput::make('votes')
                                    ->numeric()
                                    ->default(0)
                                    ->disabled(),
                                Forms\Components\TextInput::make('voting_percentage')
                                    ->label('Voting Percentage')
                                    ->numeric()
                                    ->suffix('%')
                                    ->disabled(),
                                Forms\Components\Toggle::make('can_vote')
                                    ->label('Can Receive Votes')
                                    ->default(true),
                                Forms\Components\Toggle::make('is_winner')
                                    ->label('Is Winner')
                                    ->default(false),
                                Forms\Components\TextInput::make('year')
                                    ->numeric()
                                    ->default(date('Y'))
                                    ->required(),
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('Photo')
                    ->circular()
                    ->size(50),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('organization')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('votes')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('voting_percentage')
                    ->label('Voting %')
                    ->numeric()
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\IconColumn::make('can_vote')
                    ->label('Can Vote')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_winner')
                    ->label('Winner')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('year')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Category')
                    ->options(Category::pluck('name', 'id')),
                Tables\Filters\SelectFilter::make('year')
                    ->options(array_combine(
                        range(date('Y'), date('Y') - 5),
                        range(date('Y'), date('Y') - 5)
                    )),
                Tables\Filters\TernaryFilter::make('can_vote'),
                Tables\Filters\TernaryFilter::make('is_winner'),
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('votes', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AchievementsRelationManager::class,
            RelationManagers\TestimonialsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNominees::route('/'),
            'create' => Pages\CreateNominee::route('/create'),
            'view' => Pages\ViewNominee::route('/{record}'),
            'edit' => Pages\EditNominee::route('/{record}/edit'),
        ];
    }
}
