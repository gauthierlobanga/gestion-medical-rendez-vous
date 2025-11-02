<?php

namespace App\Filament\Resources\Medecins\RelationManagers;

use Carbon\Carbon;
use App\Models\Medecin;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Actions\DissociateBulkAction;
use Filament\Resources\RelationManagers\RelationManager;

class DisponibilitesRelationManager extends RelationManager
{
    protected static string $relationship = 'disponibilites';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Section::make('Disponibilité du médecin')
                            ->schema([
                                Select::make('medecin_id')
                                    ->label('Médecin')
                                    ->options(
                                        Medecin::with('user')
                                            ->get()
                                            ->mapWithKeys(fn($medecin) => [
                                                $medecin->id => $medecin->user->name
                                            ])
                                    )
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                Select::make('jour_semaine')
                                    ->label('Jour de la semaine')
                                    ->native(false)
                                    ->options([
                                        1 => 'Lundi',
                                        2 => 'Mardi',
                                        3 => 'Mercredi',
                                        4 => 'Jeudi',
                                        5 => 'Vendredi',
                                        6 => 'Samedi',
                                        7 => 'Dimanche',
                                    ])
                                    ->required(),
                                TimePicker::make('heure_debut')
                                    ->label('Heure de début')
                                    ->native(false)
                                    ->seconds(false)
                                    ->default('08:00')
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, $get) {
                                        if (!$state) return;

                                        try {
                                            $heureDebut = Carbon::parse($state);
                                            $nouvelleHeureFin = $heureDebut->copy()->addHours(8)->format('H:i');

                                            // Toujours mettre à jour l'heure de fin
                                            $set('heure_fin', $nouvelleHeureFin);
                                        } catch (\Exception $e) {
                                            // Fallback en cas d'erreur
                                            $set('heure_fin', '16:00');
                                        }
                                    })
                                    ->helperText('Heure à laquelle commence la disponibilité du médecin.'),

                                TimePicker::make('heure_fin')
                                    ->label('Heure de fin')
                                    ->native(false)
                                    ->seconds(false)
                                    ->default('16:00')
                                    ->required()
                                    ->helperText('Heure calculée automatiquement (+8h) mais modifiable.'),
                                DatePicker::make('date_specifique')
                                    ->label('Date spécifique')
                                    ->native(false)
                                    ->closeOnDateSelection()
                                    ->default(now())
                                    ->visible(fn(callable $get) => $get('est_exception'))
                                    ->required(fn(callable $get) => $get('est_exception')),

                                TextInput::make('raison_exception')
                                    ->label('Raison de l\'exception')
                                    ->visible(fn(callable $get) => $get('est_exception'))
                                    ->maxLength(255),
                                Toggle::make('est_exception')
                                    ->label('Exception')
                                    ->default(false)
                                    ->onIcon(Heroicon::CheckBadge)
                                    ->offIcon(Heroicon::NoSymbol)
                                    ->onColor('success')
                                    ->offColor('danger')
                                    ->reactive(),
                            ])->columns(2),
                    ])->columnSpanFull()
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('jour_semaine')
            ->columns([
                TextColumn::make('medecin.user.name')
                    ->label('Médecin')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jour_semaine')
                    ->label('Jour')
                    ->formatStateUsing(fn($state) => [
                        1 => 'Lundi',
                        2 => 'Mardi',
                        3 => 'Mercredi',
                        4 => 'Jeudi',
                        5 => 'Vendredi',
                        6 => 'Samedi',
                        7 => 'Dimanche',
                    ][$state] ?? 'Inconnu')
                    ->sortable(),
                TextColumn::make('heure_debut')
                    ->label('Début')
                    ->time('H:i'),
                TextColumn::make('heure_fin')
                    ->label('Fin')
                    ->time('H:i'),
                IconColumn::make('est_exception')
                    ->label('Exception')
                    ->boolean(),
                TextColumn::make('date_specifique')
                    ->label('Date spécifique')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
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
