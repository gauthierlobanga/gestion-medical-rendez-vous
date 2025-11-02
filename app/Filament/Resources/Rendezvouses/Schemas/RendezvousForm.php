<?php

namespace App\Filament\Resources\Rendezvouses\Schemas;

use App\Models\Medecin;
use App\Models\Patient;
use App\Models\Rendezvous;
use Filament\Schemas\Schema;
use Filament\Resources\Pages\Page;
use Spatie\Permission\Models\Role;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class RendezvousForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Section::make('Informations du rendez-vous')
                            ->schema([
                                Select::make('patient_id')
                                    ->label('Patient')
                                    ->relationship(
                                        name: 'patient',
                                        titleAttribute: 'user.name',
                                        modifyQueryUsing: fn($query) => $query->with('user')
                                    )
                                    ->getOptionLabelFromRecordUsing(fn(Patient $record) => $record->user->name)
                                    ->searchable(/*['user.name', 'user.email']*/)
                                    ->preload()
                                    ->required()
                                    ->createOptionForm([
                                        Section::make()
                                            ->schema([
                                                Section::make('Informations personnelles')
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
                                                        TextInput::make('numero_securite_sociale')
                                                            ->label('Numéro de sécurité sociale')
                                                            ->required()
                                                            ->unique(ignoreRecord: true)
                                                            ->maxLength(255),
                                                        TextInput::make('mutuelle')
                                                            ->label('Mutuelle')
                                                            ->maxLength(255),
                                                        TextInput::make('numero_mutuelle')
                                                            ->label('Numéro de mutuelle')
                                                            ->maxLength(255),
                                                    ])->columns(2),

                                                Section::make('Informations médicales')
                                                    ->schema([
                                                        Textarea::make('antecedents_medicaux')
                                                            ->label('Antécédents médicaux'),
                                                        Textarea::make('allergies')
                                                            ->label('Allergies'),
                                                        Textarea::make('traitements_chroniques')
                                                            ->label('Traitements chroniques'),
                                                        Textarea::make('informations_urgence')
                                                            ->label('Informations d\'urgence'),
                                                    ])->columns(2),
                                            ])->columnSpanFull()
                                    ]),

                                Select::make('medecin_id')
                                    ->label('Médecin')
                                    ->options(
                                        Medecin::with('user')
                                            ->active()
                                            ->get()
                                            ->mapWithKeys(fn($medecin) => [
                                                $medecin->id => $medecin->user->name . ' - ' . $medecin->specialite
                                            ])
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Select::make('service_id')
                                    ->label('Service')
                                    ->relationship('service', 'nom')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                DateTimePicker::make('date_heure')
                                    ->label('Date et heure')
                                    ->required()
                                    ->native(false)
                                    ->closeOnDateSelection()
                                    ->minutesStep(15)
                                    ->displayFormat('Y-m-d H:i:s')
                                    ->minDate(now()),

                                TextInput::make('duree')
                                    ->label('Durée (minutes)')
                                    ->numeric()
                                    ->default(30)
                                    ->minValue(5)
                                    ->maxValue(240),

                                Select::make('statut')
                                    ->label('Statut')
                                    ->native(false)
                                    ->options(Rendezvous::STATUTS)
                                    ->default('planifie')
                                    ->required(),

                                Select::make('type_consultation')
                                    ->label('Type de consultation')
                                    ->native(false)
                                    ->options(Rendezvous::TYPES_CONSULTATION)
                                    ->default('premiere')
                                    ->required(),

                                TextInput::make('prix_consultation')
                                    ->label('Prix consultation')
                                    ->numeric()
                                    ->required()
                                    ->prefix('€'),

                                Select::make('mode_paiement')
                                    ->label('Mode de paiement')
                                    ->native(false)
                                    ->options(Rendezvous::MODES_PAIEMENT)
                                    ->nullable(),

                            ])->columns(3),
                        Textarea::make('motif')
                            ->label('Motif de consultation')
                            ->required()
                            ->maxLength(65535)
                            ->columnSpanFull(),

                        Textarea::make('notes')
                            ->label('Notes')
                            ->maxLength(65535)
                            ->columnSpanFull(),

                        Toggle::make('est_paye')
                            ->label('Payé')
                            ->default(false),
                    ])->columnSpanFull()
            ]);
    }
}
