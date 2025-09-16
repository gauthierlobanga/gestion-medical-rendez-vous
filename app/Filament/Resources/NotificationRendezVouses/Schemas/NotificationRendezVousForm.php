<?php

namespace App\Filament\Resources\NotificationRendezVouses\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class NotificationRendezVousForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('rendezvous_id')
                    ->relationship('rendezvous', 'id')
                    ->required(),
                Select::make('type_notification')
                    ->options([
                        'confirmation' => 'Confirmation',
                        'rappel_24h' => 'Rappel 24h',
                        'rappel_1h' => 'Rappel 1h',
                        'annulation' => 'Annulation',
                        'modification' => 'Modification',
                    ])
                    ->required(),
                TextInput::make('destinataire')
                    ->required(),
                TextInput::make('sujet')
                    ->required(),
                Textarea::make('contenu')
                    ->required()
                    ->columnSpanFull(),
                DateTimePicker::make('date_envoi'),
                Select::make('statut')
                    ->options(['en_attente' => 'En attente', 'envoye' => 'Envoye', 'erreur' => 'Erreur'])
                    ->default('en_attente')
                    ->required(),
                Textarea::make('erreur')
                    ->columnSpanFull(),
            ]);
    }
}
