<?php

namespace App\Filament\Resources\DisponibiliteMedecins\Schemas;

use App\Models\Medecin;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;

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
                                    ->required()
                                    ->seconds(false),
                                TimePicker::make('heure_fin')
                                    ->label('Heure de fin')
                                    ->required()
                                    ->seconds(false),

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
                                    ->reactive(),
                            ])->columns(2),
                    ])->columnSpanFull()
            ]);
    }
}
