<?php

namespace App\Filament\Resources\CreneauServices\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TimePicker;

class CreneauServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Section::make('Créneaux horaires du service')
                            ->schema([
                                Select::make('service_id')
                                    ->label('Service')
                                    ->relationship('service', 'nom')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                Select::make('jour_semaine')
                                    ->label('Jour de la semaine')
                                    ->searchable()
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
                                    ->required()
                                    ->seconds(false),
                                TimePicker::make('heure_fin')
                                    ->label('Heure de fin')
                                    ->native(false)
                                    ->required()
                                    ->seconds(false),
                                TextInput::make('nombre_creneaux')
                                    ->label('Nombre de créneaux')
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(1),
                                TextInput::make('duree_creneau')
                                    ->label('Durée du créneau (minutes)')
                                    ->numeric()
                                    ->minValue(5)
                                    ->maxValue(120)
                                    ->default(30),
                            ])->columns(2),
                    ])->columnSpanFull()
            ]);
    }
}
