<?php

namespace App\Filament\Resources\Medecins\Schemas;

use Filament\Schemas\Schema;
use Filament\Resources\Pages\Page;
use Spatie\Permission\Models\Role;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class MedecinForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Section::make('Informations professionnelles')
                            ->schema([
                                Select::make('user_id')
                                    ->label('Utilisateur')
                                    ->relationship('user', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->required(),
                                        TextInput::make('email')
                                            ->label('Email Address')
                                            ->unique(ignoreRecord: true)
                                            ->email()
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('password')
                                            ->label('Password')
                                            ->password()
                                            ->revealable()
                                            ->dehydrated(fn($state) => filled($state))
                                            ->required(fn(Page $livewire): bool => $livewire instanceof CreateRecord),
                                        DateTimePicker::make('email_verified_at')
                                            ->label('Email Verified At')
                                            ->native(false)
                                            ->closeOnDateSelection()
                                            ->required()
                                            ->default(now()),
                                        Select::make('roles')
                                            ->multiple()
                                            ->relationship('roles', 'name')
                                            ->preload()
                                            ->reactive()
                                            ->live(onBlur: true)
                                            ->searchable()
                                            ->options(Role::all()->pluck('name', 'id')),
                                        SpatieMediaLibraryFileUpload::make('avatar')
                                            ->collection('avatars')
                                            ->conversion('thumb')
                                            ->image()
                                            ->imagePreviewHeight('250')
                                            ->imageEditor()
                                            ->preserveFilenames()
                                            ->responsiveImages()
                                            ->acceptedFileTypes(['image/jpg', 'image/jpeg', 'image/png', 'image/webp'])
                                            ->required()
                                            ->columnSpanFull(),
                                        Toggle::make('has_email_authentication')
                                            ->required(),
                                        Toggle::make('is_active')
                                            ->required(),
                                    ]),
                                Select::make('service_id')
                                    ->label('Service')
                                    ->relationship('service', 'nom')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                TextInput::make('specialite')
                                    ->label('Spécialité')
                                    ->required()
                                    ->maxLength(255),
                            ])->columns(3),

                        Section::make()
                            ->schema([
                                TextInput::make('numero_ordre')
                                    ->label('Numéro d\'ordre')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                                TextInput::make('tarif_consultation')
                                    ->label('Tarif consultation')
                                    ->numeric()
                                    ->required()
                                    ->prefix('€'),

                            ])->columns(2),

                        Textarea::make('diplomes')
                            ->label('Diplômes')
                            ->columnSpanFull(),
                        Toggle::make('is_active')
                            ->label('Médecin actif')
                            ->default(true),
                    ])->columnSpanFull(),
            ]);
    }
}
