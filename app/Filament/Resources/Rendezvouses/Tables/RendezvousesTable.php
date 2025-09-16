<?php

namespace App\Filament\Resources\Rendezvouses\Tables;

use App\Models\Medecin;
use App\Models\Rendezvous;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Filters\Filter;
use App\Mail\RendezVousConfirmeMail;
use Illuminate\Support\Facades\Mail;
use Filament\Actions\BulkActionGroup;
use App\Mail\AnnulationRendezVousMail;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class RendezvousesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('patient.user.name')
                    ->label('Patient')
                    ->searchable()
                    ->sortable(),
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
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('duree')
                    ->label('Durée (min)')
                    ->sortable(),
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
                TextColumn::make('type_consultation')
                    ->label('Type')
                    ->formatStateUsing(fn(string $state): string => __($state)),
                TextColumn::make('prix_consultation')
                    ->label('Prix')
                    ->money('EUR')
                    ->sortable(),
                IconColumn::make('est_paye')
                    ->label('Payé')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('statut')
                    ->options(Rendezvous::STATUTS),
                SelectFilter::make('medecin')
                    ->options(
                        Medecin::with('user')
                            ->active()
                            ->get()
                            ->mapWithKeys(fn($medecin) => [
                                $medecin->id => $medecin->user->name . ' - ' . $medecin->specialite
                            ])
                    )
                    ->native(false)
                    ->searchable()
                    ->preload(),
                SelectFilter::make('service')
                    ->relationship('service', 'nom')
                    ->searchable()
                    ->preload(),
                Filter::make('date_heure')
                    ->schema([
                        DatePicker::make('date_from')
                            ->native(false)
                            ->label('Du'),
                        DatePicker::make('date_to')
                            ->native(false)
                            ->label('Au'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date_heure', '>=', $date),
                            )
                            ->when(
                                $data['date_to'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date_heure', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                Action::make('confirmer')
                    ->label('Confirmer')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(function (Rendezvous $record) {
                        $record->confirmer();
                        // Envoyer l'email de confirmation
                        Mail::to($record->patient->user->email)
                            ->send(new RendezVousConfirmeMail($record));
                    })
                    ->visible(fn(Rendezvous $record): bool => $record->estPlanifie()),
                Action::make('annuler')
                    ->label('Annuler')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->schema([
                        Textarea::make('raison')
                            ->label('Raison de l\'annulation')
                            ->required(),
                    ])
                    ->action(function (Rendezvous $record, array $data) {
                        $record->annuler($data['raison']);

                        // Envoyer l'email d'annulation
                        Mail::to($record->patient->user->email)
                            ->send(new AnnulationRendezVousMail($record, 'patient', $data['raison']));
                    })
                    ->visible(fn(Rendezvous $record): bool => in_array($record->statut, ['planifie', 'confirme'])),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
