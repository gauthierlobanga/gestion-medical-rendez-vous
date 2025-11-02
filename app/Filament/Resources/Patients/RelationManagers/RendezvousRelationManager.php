<?php

namespace App\Filament\Resources\Patients\RelationManagers;

use App\Models\Medecin;
use App\Models\Rendezvous;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use App\Mail\RappelRendezVousMail;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Illuminate\Support\Facades\Mail;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Actions\DissociateBulkAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Resources\RelationManagers\RelationManager;

class RendezvousRelationManager extends RelationManager
{
    protected static string $relationship = 'rendezvous';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations du rendez-vous')
                    ->schema([
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
                    ]),
                Section::make()
                    ->schema([
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
                            ->prefix('CDF'),

                        Select::make('mode_paiement')
                            ->label('Mode de paiement')
                            ->native(false)
                            ->options(Rendezvous::MODES_PAIEMENT)
                            ->nullable(),
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
                    ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('numero_mutuelle')
            ->columns([
                TextColumn::make('numero_mutuelle')
                    ->searchable(),
                TextColumn::make('medecin.user.name')
                    ->label('Médecin')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('service.nom')
                    ->label('Service')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('date_heure')
                    ->label('Date et heure')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('duree')
                    ->label('Durée (min)'),
                TextColumn::make('statut')
                    ->label('Statut')
                    ->badge()
                    ->colors([
                        'warning' => 'planifie',
                        'success' => 'confirme',
                        'danger' => 'annule',
                        'primary' => 'termine',
                        'secondary' => 'absent',
                    ])
                    ->formatStateUsing(fn(string $state): string => __($state)),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->after(function ($record) {
                        // Envoyer un email après la création du rendez-vous
                        Mail::to($record->patient->user->email)
                            ->send(new RappelRendezVousMail($record, 'patient'));
                    }),
                AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->after(function ($record) {
                        // Envoyer un email après la modification du rendez-vous
                        Mail::to($record->patient->user->email)
                            ->send(new RappelRendezVousMail($record, 'patient'));
                    }),
                DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
