<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GeneralGlobalSettingsResource\Pages;
use App\Models\GeneralGlobalSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GeneralGlobalSettingsResource extends Resource
{
    protected static ?string $model = GeneralGlobalSettings::class;
    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';
    protected static ?string $navigationGroup = 'System Settings';
    protected static ?string $navigationLabel = 'General Global Settings';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Contact Information')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('international_phone')
                            ->label('International Phone')
                            ->tel()
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Location Details')
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->label('Address')
                            ->rows(3)
                            ->maxLength(1000),
                        Forms\Components\TextInput::make('location')
                            ->label('Location')
                            ->maxLength(255),
                    ])->columns(1),

                Forms\Components\Section::make('Business Information')
                    ->schema([
                        Forms\Components\TextInput::make('office_hours')
                            ->label('Office Hours')
                            ->placeholder('e.g., Monday - Friday: 9:00 AM - 6:00 PM')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('motto')
                            ->label('Company Motto')
                            ->maxLength(255),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('international_phone')
                    ->label('Phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('location')
                    ->label('Location')
                    ->searchable(),
                Tables\Columns\TextColumn::make('office_hours')
                    ->label('Office Hours')
                    ->limit(50),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('has_email')
                    ->query(fn($query) => $query->whereNotNull('email')),
                Tables\Filters\Filter::make('has_phone')
                    ->query(fn($query) => $query->whereNotNull('international_phone')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGeneralGlobalSettings::route('/'),
            'create' => Pages\CreateGeneralGlobalSettings::route('/create'),
            'edit' => Pages\EditGeneralGlobalSettings::route('/{record}/edit'),
        ];
    }
}
