<?php

use Livewire\Volt\Component;
use App\Models\DisponibiliteMedecin;
use Illuminate\Support\Facades\Auth;

use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $form = [
        'jour_semaine' => '',
        'heure_debut' => '09:00',
        'heure_fin' => '17:00',
        'date_specifique' => null,
        'est_exception' => false,
        'raison_exception' => '',
    ];

    public $disponibiliteToDelete = null;
    public $disponibiliteToEdit = null;

    public function disponibilites()
    {
        return DisponibiliteMedecin::where('medecin_id', Auth::user()->medecin->id)
            ->orderBy('date_specifique')
            ->orderBy('jour_semaine')
            ->orderBy('heure_debut')
            ->paginate(5);
    }

    public function openCreateModal()
    {
        $this->reset(['form', 'disponibiliteToEdit']);
        $this->form = [
            'jour_semaine' => '',
            'heure_debut' => '09:00',
            'heure_fin' => '17:00',
            'date_specifique' => null,
            'est_exception' => false,
            'raison_exception' => '',
        ];

        Flux::modal('create-disponibilite')->show();
    }

    public function openEditModal($id)
    {
        $this->disponibiliteToEdit = DisponibiliteMedecin::find($id);

        if ($this->disponibiliteToEdit) {
            $this->form = [
                'jour_semaine' => $this->disponibiliteToEdit->jour_semaine,
                'heure_debut' => $this->disponibiliteToEdit->heure_debut->format('H:i'),
                'heure_fin' => $this->disponibiliteToEdit->heure_fin->format('H:i'),
                'date_specifique' => $this->disponibiliteToEdit->date_specifique?->format('Y-m-d'),
                'est_exception' => $this->disponibiliteToEdit->est_exception,
                'raison_exception' => $this->disponibiliteToEdit->raison_exception,
            ];

            Flux::modal('edit-disponibilite')->show();
        }
    }

    public function openDeleteModal($id)
    {
        $this->disponibiliteToDelete = DisponibiliteMedecin::find($id);
        Flux::modal('delete-disponibilite')->show();
    }

    public function saveDisponibilite()
    {
        $rules = [
            'form.heure_debut' => 'required|date_format:H:i',
            'form.heure_fin' => 'required|date_format:H:i|after:form.heure_debut',
            'form.est_exception' => 'boolean',
            'form.raison_exception' => 'required_if:form.est_exception,true|nullable|string|max:255',
        ];

        $messages = [
            'form.heure_debut.required' => 'L\'heure de début est obligatoire.',
            'form.heure_debut.date_format' => 'Le format de l\'heure de début est invalide.',
            'form.heure_fin.required' => 'L\'heure de fin est obligatoire.',
            'form.heure_fin.date_format' => 'Le format de l\'heure de fin est invalide.',
            'form.heure_fin.after' => 'L\'heure de fin doit être après l\'heure de début.',
            'form.est_exception.boolean' => 'Le champ exception doit être vrai ou faux.',
            'form.raison_exception.required_if' => 'La raison est obligatoire pour les exceptions.',
            'form.raison_exception.max' => 'La raison ne doit pas dépasser 255 caractères.',
        ];

        // Règles conditionnelles pour jour_semaine et date_specifique
        if ($this->form['est_exception']) {
            $rules['form.date_specifique'] = 'required|date|after_or_equal:today';
            $rules['form.jour_semaine'] = 'nullable|integer|between:1,7';

            $messages['form.date_specifique.required'] = 'La date spécifique est obligatoire pour les exceptions.';
            $messages['form.date_specifique.date'] = 'La date spécifique doit être une date valide.';
            $messages['form.date_specifique.after_or_equal'] = 'La date spécifique doit être aujourd\'hui ou une date future.';
        } else {
            $rules['form.jour_semaine'] = 'required|integer|between:1,7';
            $rules['form.date_specifique'] = 'nullable|date';

            $messages['form.jour_semaine.required'] = 'Le jour de la semaine est obligatoire.';
            $messages['form.jour_semaine.integer'] = 'Le jour de la semaine doit être un nombre entier.';
            $messages['form.jour_semaine.between'] = 'Le jour de la semaine doit être entre 1 et 7.';
        }

        $validated = $this->validate($rules, $messages);

        $data = $validated['form'];

        if ($this->form['est_exception']) {
            $data['jour_semaine'] = null;
        } else {
            $data['date_specifique'] = null;
        }

        if ($this->disponibiliteToEdit) {
            // Édition
            $this->disponibiliteToEdit->update($data);
            session()->flash('success', 'Disponibilité modifiée avec succès');
            Flux::modal('edit-disponibilite')->close();
        } else {
            // Création
            DisponibiliteMedecin::create(
                array_merge($data, [
                    'medecin_id' => Auth::user()->medecin->id,
                ]),
            );
            session()->flash('success', 'Disponibilité créée avec succès');
            Flux::modal('create-disponibilite')->close();
        }

        $this->reset(['form', 'disponibiliteToEdit', 'disponibiliteToDelete']);
    }

    public function confirmDelete()
    {
        if ($this->disponibiliteToDelete) {
            $this->disponibiliteToDelete->delete();
            session()->flash('success', 'Disponibilité supprimée avec succès');
            Flux::modal('delete-disponibilite')->close();
            $this->reset(['disponibiliteToDelete']);
        }
    }
}; ?>

<div>
    <div class="py-8" x-cloak x-data="{}">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- En-tête -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Mes Disponibilités</h1>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            Gérez vos créneaux de disponibilité pour les rendez-vous
                        </p>
                    </div>
                    <flux:button variant="primary" icon="plus" wire:click="openCreateModal"
                        class="mt-4 cursor-pointer sm:mt-0 inline-flex items-center px-4 py-2 bg-gradient-to-br from-blue-600 to-blue-700 hover:from-blue-600 hover:to-blue-500 border border-transparent rounded-lg font-semibold text-white transition-all duration-200 focus:outline-none">
                        Ajouter disponibilite
                    </flux:button>
                </div>
            </div>
            <!-- Alertes de succès -->
            @if (session()->has('success'))
                <div x-cloak x-data="{ show: true }" x-show="show" x-init="setTimeout(() => { show = false }, 5000)"
                    class="mb-6 p-4 rounded-lg transition-all duration-500 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800"
                    x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 translate-y-3 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-8">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-3" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-green-800 dark:text-green-300 font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif
            <!-- Tableau des disponibilités -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                @if ($this->disponibilites()->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Date/Jour
                                    </th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Horaires
                                    </th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Type
                                    </th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($this->disponibilites() as $dispo)
                                    <tr x-cloak x-data="{}"
                                        class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div
                                                    class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center mr-3">
                                                    <span class="text-sm font-medium text-blue-600 dark:text-blue-400">
                                                        @if ($dispo->est_exception && $dispo->date_specifique)
                                                            {{ $dispo->date_specifique->format('d') }}
                                                        @else
                                                            {{ substr($dispo->jour_semaine_name, 0, 1) }}
                                                        @endif
                                                    </span>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                                        @if ($dispo->est_exception && $dispo->date_specifique)
                                                            {{ $dispo->date_specifique->format('d/m/Y') }}
                                                        @else
                                                            {{ $dispo->jour_semaine_name }}
                                                        @endif
                                                    </div>
                                                    @if ($dispo->est_exception && $dispo->date_specifique)
                                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                                            {{ $dispo->date_specifique->translatedFormat('l') }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div
                                                class="text-sm text-gray-900 dark:text-white font-mono bg-gray-50 dark:bg-gray-700 rounded px-2 py-1 inline-block">
                                                {{ $dispo->heure_debut->format('H:i') }} -
                                                {{ $dispo->heure_fin->format('H:i') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($dispo->est_exception)
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                                    </svg>
                                                    Exception
                                                </span>
                                                <div class="text-xs text-orange-600 dark:text-orange-400 mt-1">
                                                    {{ $dispo->raison_exception }}
                                                </div>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Régulière
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2 gap-1">
                                                <flux:button x-cloak x-data="{}" variant="primary"
                                                    icon="pencil-square" wire:click="openEditModal({{ $dispo->id }})"
                                                    class="cursor-pointer inline-flex items-center px-3 py-1.5 border border-indigo-300 dark:border-indigo-700 rounded-md text-sm font-medium text-indigo-600 dark:text-indigo-400 bg-white dark:bg-zinc-800 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors focus:outline-none" />

                                                <flux:button x-cloak x-data="{}" variant="primary"
                                                    icon="trash" wire:click="openDeleteModal({{ $dispo->id }})"
                                                    class="cursor-pointer inline-flex items-center px-3 py-1.5 border border-red-300 dark:border-red-700 rounded-md text-sm font-medium text-red-600 dark:text-red-400 bg-white dark:bg-zinc-800 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors focus:outline-none" />
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $this->disponibilites()->links() }}
                    </div>
                @else
                    <!-- État vide -->
                    <div class="text-center py-12">
                        <div
                            class="mx-auto w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-10 h-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Aucune disponibilité</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-6 max-w-md mx-auto">
                            Vous n'avez pas encore configuré vos disponibilités. Ajoutez vos créneaux pour permettre la
                            prise de rendez-vous.
                        </p>
                        <flux:button variant="primary" wire:click="openCreateModal" class="cursor-pointer">
                            Ajouter ma première disponibilité
                        </flux:button>
                    </div>
                @endif
            </div>
            <!-- Informations -->
            @if ($this->disponibilites()->count() > 0)
                <div
                    class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 mr-3 flex-shrink-0" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-sm text-blue-800 dark:text-blue-300">
                            <p class="font-medium">Comment ça marche ?</p>
                            <p class="mt-1">
                                Les <span class="font-semibold">disponibilités régulières</span> définissent vos
                                créneaux standards récurrents chaque semaine.
                                Les <span class="font-semibold">exceptions</span> sont utilisées pour les
                                disponibilités
                                ou indisponibilités spécifiques à une date précise (congés, formations, etc.).
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal de suppression --}}
    <flux:modal name="delete-disponibilite" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Suppression de disponibilité</flux:heading>
                <flux:text class="mt-2">
                    Êtes-vous sûr de vouloir supprimer cette disponibilité ? Cette action est irréversible.
                </flux:text>
            </div>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost" class="cursor-pointer">
                        Annuler
                    </flux:button>
                </flux:modal.close>
                <flux:button variant="primary" icon="trash" wire:click="confirmDelete"
                    class="cursor-pointer bg-red-600 hover:bg-red-700 text-white">
                    Supprimer
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Modal d'édition --}}
    <flux:modal name="edit-disponibilite" class="md:w-900">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Modifier la disponibilité</flux:heading>
                <flux:text class="mt-2">Modifiez les détails de votre créneau de disponibilité.</flux:text>
            </div>

            <flux:field variant="inline">
                <flux:checkbox wire:model="form.est_exception" />
                <flux:label>Exception (disponibilité spécifique à une date)</flux:label>
            </flux:field>

            <div x-cloak x-data="{ isException: @entangle('form.est_exception') }">
                <!-- Champ jour de la semaine (visible seulement pour les disponibilités régulières) -->
                <div x-show="!isException" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform -translate-y-2">
                    <flux:select wire:model="form.jour_semaine" label="Jour de la semaine"
                        placeholder="Sélectionner un jour...">
                        <flux:select.option value="1">Lundi</flux:select.option>
                        <flux:select.option value="2">Mardi</flux:select.option>
                        <flux:select.option value="3">Mercredi</flux:select.option>
                        <flux:select.option value="4">Jeudi</flux:select.option>
                        <flux:select.option value="5">Vendredi</flux:select.option>
                        <flux:select.option value="6">Samedi</flux:select.option>
                        <flux:select.option value="7">Dimanche</flux:select.option>
                    </flux:select>
                </div>

                <!-- Champs pour les exceptions (date spécifique + raison) -->
                <div x-show="isException" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform -translate-y-2" class="space-y-6">

                    <flux:input type="date" wire:model="form.date_specifique" label="Date spécifique"
                        min="{{ now()->format('Y-m-d') }}" />

                    <flux:input label="Raison de l'exception" wire:model="form.raison_exception"
                        placeholder="Ex: Congé annuel, Formation, Réunion..." />
                </div>
            </div>

            <flux:input type="time" wire:model="form.heure_debut" label="Heure de début" />

            <flux:input type="time" wire:model="form.heure_fin" label="Heure de fin" />

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost" class="cursor-pointer">
                        Annuler
                    </flux:button>
                </flux:modal.close>
                <flux:button variant="primary" wire:click="saveDisponibilite" class="cursor-pointer">
                    Enregistrer les modifications
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Modal de création --}}
    <flux:modal name="create-disponibilite" class="md:w-900">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Nouvelle disponibilité</flux:heading>
                <flux:text class="mt-2">Ajoutez un nouveau créneau de disponibilité pour les rendez-vous.</flux:text>
            </div>

            <flux:field variant="inline">
                <flux:checkbox wire:model="form.est_exception" />
                <flux:label>Exception (disponibilité spécifique à une date)</flux:label>
            </flux:field>

            <div x-cloak x-data="{ isException: @entangle('form.est_exception') }">
                <!-- Champ jour de la semaine (visible seulement pour les disponibilités régulières) -->
                <div x-show="!isException" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform -translate-y-2">
                    <flux:select wire:model="form.jour_semaine" label="Jour de la semaine"
                        placeholder="Sélectionner un jour...">
                        <flux:select.option value="1">Lundi</flux:select.option>
                        <flux:select.option value="2">Mardi</flux:select.option>
                        <flux:select.option value="3">Mercredi</flux:select.option>
                        <flux:select.option value="4">Jeudi</flux:select.option>
                        <flux:select.option value="5">Vendredi</flux:select.option>
                        <flux:select.option value="6">Samedi</flux:select.option>
                        <flux:select.option value="7">Dimanche</flux:select.option>
                    </flux:select>
                </div>

                <!-- Champs pour les exceptions (date spécifique + raison) -->
                <div x-show="isException" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform -translate-y-2" class="space-y-6">

                    <flux:input type="date" wire:model="form.date_specifique" label="Date spécifique"
                        min="{{ now()->format('Y-m-d') }}" />

                    <flux:input label="Raison de l'exception" wire:model="form.raison_exception"
                        placeholder="Ex: Congé annuel, Formation, Réunion..." />
                </div>
            </div>

            <flux:input type="time" wire:model="form.heure_debut" label="Heure de début" />

            <flux:input type="time" wire:model="form.heure_fin" label="Heure de fin" />

            <div class="flex gap-2">
                <flux:spacer />

                <flux:modal.close>
                    <flux:button variant="ghost" class="cursor-pointer">
                        Annuler
                    </flux:button>
                </flux:modal.close>

                <flux:button variant="primary" wire:click="saveDisponibilite" class="cursor-pointer">
                    Créer la disponibilité
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
