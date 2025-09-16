<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Models\Medecin;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\ColorPicker;
use Illuminate\Database\Eloquent\Builder;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations du service')
                    ->schema([
                        Select::make('responsable_id')
                            ->label('Responsable')
                            ->options(
                                Medecin::with('user')
                                    ->get()
                                    ->mapWithKeys(fn($medecin) => [
                                        $medecin->id => $medecin->user->name
                                    ])
                            )
                            ->searchable()
                            ->preload(),

                        TextInput::make('nom')
                            ->label('Nom du service')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('description')
                            ->label('Description')
                            ->columnSpanFull(),

                        ColorPicker::make('couleur')
                            ->label('Couleur')
                            ->default('#3b82f6'),

                        TextInput::make('duree_rendezvous')
                            ->label('Durée des rendez-vous (minutes)')
                            ->numeric()
                            ->minValue(5)
                            ->maxValue(120)
                            ->default(30),

                        Toggle::make('is_active')
                            ->label('Service actif')
                            ->default(true),
                    ]),
            ]);
    }
}
