<?php

namespace App\Filament\Resources\Patients\Schemas;

use App\Models\User;
use App\Models\Patient;
use Filament\Schemas\Schema;
use Filament\Resources\Pages\Page;
use Spatie\Permission\Models\Role;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Unique;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class PatientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Section::make('Informations personnelles')
                            ->schema([
                                Select::make('user_id')
                                    ->label('Utilisateur')
                                    ->relationship('user', 'name')
                                    ->unique(
                                        table: 'patients',
                                        column: 'user_id',
                                        ignoreRecord: true
                                    )
                                    ->options(function (Get $get, ?Model $record): array {
                                        // Si on édite un patient, on récupère son utilisateur actuel
                                        $currentUser = null;
                                        if ($record) {
                                            $currentUser = User::find($record->user_id);
                                        }

                                        // Récupère les IDs des utilisateurs déjà assignés à un AUTRE patient
                                        $assignedUserIds = Patient::query()
                                            ->when($record, fn($query) => $query->where('id', '!=', $record->id))
                                            ->pluck('user_id');

                                        // Récupère les utilisateurs éligibles (rôle patient, pas déjà assignés)
                                        $eligibleUsers = User::role('patient')
                                            ->whereNotIn('id', $assignedUserIds)
                                            ->limit(50)
                                            ->pluck('name', 'id');

                                        // Si on a un utilisateur actuel (en mode édition), on s'assure qu'il est dans la liste
                                        if ($currentUser) {
                                            $eligibleUsers[$currentUser->id] = $currentUser->name;
                                        }

                                        return $eligibleUsers->toArray();
                                    })

                                    // 3. Logique pour la recherche (maintenant cohérente avec le chargement)
                                    ->getSearchResultsUsing(function (string $search, ?Model $record): array {
                                        // Récupère les IDs des utilisateurs déjà assignés à un AUTRE patient
                                        $assignedUserIds = Patient::query()
                                            ->when($record, fn($query) => $query->where('id', '!=', $record->id))
                                            ->pluck('user_id');

                                        // Requête sur les utilisateurs
                                        $userQuery = User::role('patient')
                                            ->whereNotIn('id', $assignedUserIds)
                                            ->where('name', 'like', "%{$search}%");

                                        // On inclut aussi l'utilisateur actuel dans la recherche s'il correspond
                                        if ($record && $record->user) {
                                            $userQuery->orWhere('id', $record->user_id);
                                        }

                                        return $userQuery
                                            ->limit(50)
                                            ->pluck('name', 'id')
                                            ->toArray();
                                    })
                                    ->getOptionLabelUsing(fn($value): ?string => User::find($value)?->name ?? '')
                                    ->required()
                                    ->searchable()
                                    ->loadingMessage('Chargement des patients...')
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
                                            ->options(Role::all()->pluck('name', 'id'))
                                            ->default(function () {
                                                $patientRole = Role::where('name', 'patient')->first();
                                                return $patientRole ? [$patientRole->id] : [];
                                            }),
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
            ]);
    }
}
