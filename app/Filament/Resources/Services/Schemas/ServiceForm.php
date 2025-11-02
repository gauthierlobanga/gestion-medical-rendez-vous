<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Models\User;
use App\Models\Medecin;
use App\Models\Service;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Utilities\Get;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations du service')
                    ->schema([
                        Section::make()
                            ->schema([
                                // Select::make('responsable_id')
                                //     ->label('Responsable du service')
                                //     ->relationship('responsable', 'user.name')
                                //     ->options(function (Get $get, ?Model $record): array {
                                //         $assignedUserIds = Service::query()
                                //             ->when($record, fn($query) => $query->where('id', '!=', $record->id))
                                //             ->pluck('responsable_id');

                                //         $query = User::role('Medecin Chef Service')
                                //             ->whereNotIn('id', $assignedUserIds);

                                //         // Inclure le responsable actuel lors de l'édition
                                //         if ($record && $record->responsable_id) {
                                //             $query->orWhere('id', $record->responsable_id);
                                //         }

                                //         return $query->pluck('name', 'id')->toArray();
                                //     })
                                //     ->getSearchResultsUsing(function (string $search, ?Model $record): array {
                                //         $assignedUserIds = Service::query()
                                //             ->when($record, fn($query) => $query->where('id', '!=', $record->id))
                                //             ->pluck('responsable_id');

                                //         $query = User::role('Medecin Chef Service')
                                //             ->whereNotIn('id', $assignedUserIds)
                                //             ->where('name', 'like', "%{$search}%");

                                //         if ($record && $record->responsable_id) {
                                //             $query->orWhere('id', $record->responsable_id);
                                //         }

                                //         return $query->pluck('name', 'id')->toArray();
                                //     })
                                //     ->getOptionLabelUsing(fn($value): ?string => User::find($value)?->name ?? '')
                                //     ->required()
                                //     ->searchable()
                                //     ->preload()
                                //     ->helperText('Sélectionnez le médecin chef de service (un seul par service).'),
                                Select::make('responsable_id')
                                    ->label('Responsable du service')
                                    ->relationship('responsable', 'nom')
                                    ->options(function (?Model $record) {
                                        $query = Medecin::query()
                                            ->whereHas('user.roles', fn($q) => $q->where('name', 'Medecin Chef Service'));

                                        // On inclut le médecin actuel si on édite un enregistrement
                                        if ($record && $record->responsable_id) {
                                            $query->orWhere('id', $record->responsable_id);
                                        }

                                        return $query->get()
                                            ->pluck('nom', 'id')
                                            ->toArray();
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->helperText('Sélectionnez un médecin ayant le rôle de Chef de service'),

                                // Select::make('responsable_id')
                                //     ->label('Responsable du service')
                                //     ->relationship('responsable', 'nom')
                                //     ->options(function (?Model $record) {
                                //         // Cas 1 : mode ÉDITION → on ne montre que le responsable actuel
                                //         if ($record && $record->responsable_id) {
                                //             $responsable = Medecin::with('user')
                                //                 ->where('id', $record->responsable_id)
                                //                 ->first();

                                //             return $responsable
                                //                 ? [$responsable->id => $responsable->user->name ?? $responsable->nom]
                                //                 : [];
                                //         }

                                //         // Cas 2 : mode CRÉATION → on montre tous les médecins chef de service
                                //         return Medecin::query()
                                //             ->whereHas('user.roles', fn($q) => $q->where('name', 'Medecin Chef Service'))
                                //             ->get()
                                //             ->mapWithKeys(fn($m) => [$m->id => $m->user->name ?? $m->nom])
                                //             ->toArray();
                                //     })
                                //     ->searchable()
                                //     ->preload()
                                //     ->required()
                                //     ->helperText('Sélectionnez le médecin chef de service'),

                                TextInput::make('nom')
                                    ->label('Nom du service')
                                    ->required()
                                    ->maxLength(255),

                                ColorPicker::make('couleur')
                                    ->label('Couleur')
                                    ->default('#3b82f6'),

                                TextInput::make('duree_rendezvous')
                                    ->label('Durée des rendez-vous (minutes)')
                                    ->numeric()
                                    ->minValue(5)
                                    ->maxValue(120)
                                    ->default(30),
                            ])->columns(2),

                        RichEditor::make('description')
                            ->label('Description')
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->label('Service actif')
                            ->onIcon(Heroicon::CheckBadge)
                            ->offIcon(Heroicon::XCircle)
                            ->onColor('success')
                            ->offColor('danger')
                            ->default(true),
                    ])->columnSpanFull(),
            ]);
    }
}
