<?php

namespace App\Filament\Resources\DisponibiliteMedecins\Schemas;

use Carbon\Carbon;
use App\Models\Medecin;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Support\Icons\Heroicon;

class DisponibiliteMedecinForm
{
    public static function configure(Schema $schema): Schema
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
    protected function isHeureFinCalculee($heureDebut, $heureFin): bool
    {
        try {
            $fin = Carbon::parse($heureFin);
            $finCalculee = $heureDebut->copy()->addHours(8);
            return $fin->format('H:i') === $finCalculee->format('H:i');
        } catch (\Exception $e) {
            return false;
        }
    }
}
