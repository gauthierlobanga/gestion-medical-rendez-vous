<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Schemas\Schema;
use Filament\Resources\Pages\Page;
use Spatie\Permission\Models\Role;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Schemas\Components\Section;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([

                        Section::make()
                            ->schema([
                                TextInput::make('name')
                                    ->required(),
                                TextInput::make('email')
                                    ->label('Email Address')
                                    ->unique(ignoreRecord: true)
                                    ->email()
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('password')
                                    ->label('Mot de passe')
                                    ->password()
                                    ->required(fn(string $operation): bool => $operation === 'create')
                                    ->minLength(8)
                                    ->revealable()
                                    ->same('passwordConfirmation')
                                    ->dehydrated(fn($state) => filled($state)),
                                TextInput::make('passwordConfirmation')
                                    ->label('Confirmation du mot de passe')
                                    ->password()
                                    ->revealable()
                                    ->required(fn(string $operation): bool => $operation === 'create')
                                    ->minLength(8)
                                    ->dehydrated(false),
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

                            ])->columns(2),
                        Section::make()
                            ->schema([
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
                            ])

                    ])->columnSpanFull()
            ]);
    }
}
