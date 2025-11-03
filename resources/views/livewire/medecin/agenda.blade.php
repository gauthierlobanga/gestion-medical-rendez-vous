<?php

use Livewire\Volt\Component;
use App\Models\Rendezvous;
use App\Models\DisponibiliteMedecin;
use App\Models\Patient;
use App\Models\Medecin;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Flux\Flux;

new class extends Component {
    public $dateSelected;
    public $rendezvous = [];
    public $currentRendezVous = null;
    public $patients = [];
    public $medecin;
    public $form = [
        'patient_id' => '',
        'date_heure' => '',
        'duree' => 30,
        'motif' => '',
        'type_consultation' => 'suivi',
        'prix_consultation' => '',
        'notes' => '',
    ];

    public function mount()
    {
        $this->medecin = $this->getMedecinProfile();

        if (!$this->medecin) {
            session()->flash('error', 'Vous n\'avez pas de profil médecin associé à votre compte.');
            return;
        }

        $this->dateSelected = now()->format('Y-m-d');
        $this->loadRendezVous();
        $this->patients = Patient::with('user')->get();
        $this->form['prix_consultation'] = $this->medecin->tarif_consultation ?? 0;
    }

    public function getMedecinProfile()
    {
        if (!Auth::user()->hasRole(['medecin', 'Medecin Chef Service', 'Super Admin'])) {
            return null;
        }
        return Medecin::where('user_id', Auth::id())->first();
    }

    public function loadRendezVous()
    {
        if (!$this->medecin) {
            $this->rendezvous = collect();
            return;
        }

        $this->rendezvous = Rendezvous::with(['patient.user', 'service'])
            ->pourMedecin($this->medecin->id)
            ->whereDate('date_heure', $this->dateSelected)
            ->orderBy('date_heure')
            ->get();
    }

    public function selectDate($date)
    {
        $this->dateSelected = $date;
        $this->loadRendezVous();
    }

    public function openCreateModal()
    {
        if (!$this->medecin) {
            session()->flash('error', 'Profil médecin non trouvé.');
            return;
        }

        $this->currentRendezVous = null;
        $this->form = [
            'patient_id' => '',
            'date_heure' => $this->dateSelected . ' 09:00',
            'duree' => 30,
            'motif' => '',
            'type_consultation' => 'suivi',
            'prix_consultation' => $this->medecin->tarif_consultation ?? 0,
            'notes' => '',
        ];

        Flux::modal('create-rendezvous')->show();
    }

    public function openEditModal($id)
    {
        $this->currentRendezVous = Rendezvous::find($id);
        $this->form = [
            'patient_id' => $this->currentRendezVous->patient_id,
            'date_heure' => $this->currentRendezVous->date_heure->format('Y-m-d\TH:i'),
            'duree' => $this->currentRendezVous->duree,
            'motif' => $this->currentRendezVous->motif,
            'type_consultation' => $this->currentRendezVous->type_consultation,
            'prix_consultation' => $this->currentRendezVous->prix_consultation,
            'notes' => $this->currentRendezVous->notes,
        ];

        Flux::modal('edit-rendezvous')->show();
    }

    public function saveRendezVous()
    {
        if (!$this->medecin) {
            session()->flash('error', 'Profil médecin non trouvé.');
            return;
        }

        $validated = $this->validate([
            'form.patient_id' => 'required|exists:patients,id',
            'form.date_heure' => 'required|date',
            'form.duree' => 'required|integer|min:15',
            'form.motif' => 'required|string|max:500',
            'form.type_consultation' => 'required|in:premiere,suivi,urgence,teleconsultation',
            'form.prix_consultation' => 'required|numeric|min:0',
            'form.notes' => 'nullable|string',
        ]);

        if ($this->currentRendezVous) {
            $this->currentRendezVous->update(
                array_merge($validated['form'], [
                    'medecin_id' => $this->medecin->id,
                    'service_id' => $this->medecin->service_id,
                ]),
            );
            session()->flash('message', 'Rendez-vous modifié avec succès');
            Flux::modal('edit-rendezvous')->close();
        } else {
            Rendezvous::create(
                array_merge($validated['form'], [
                    'medecin_id' => $this->medecin->id,
                    'service_id' => $this->medecin->service_id,
                    'statut' => 'planifie',
                ]),
            );
            session()->flash('message', 'Rendez-vous créé avec succès');
            Flux::modal('create-rendezvous')->close();
        }

        $this->reset(['form', 'currentRendezVous']);
        $this->loadRendezVous();
    }

    public function confirmerRendezVous($id)
    {
        $rdv = Rendezvous::find($id);
        $rdv->confirmer();
        $this->loadRendezVous();
        session()->flash('message', 'Rendez-vous confirmé');
    }

    public function openAnnulerModal($id)
    {
        $this->currentRendezVous = Rendezvous::find($id);
        Flux::modal('annuler-rendezvous')->show();
    }

    public function annulerRendezVous()
    {
        if ($this->currentRendezVous) {
            $this->currentRendezVous->annuler('Annulé par le médecin');
            $this->loadRendezVous();
            session()->flash('message', 'Rendez-vous annulé');
            Flux::modal('annuler-rendezvous')->close();
            $this->currentRendezVous = null;
        }
    }

    public function getDisponibilitesProperty()
    {
        if (!$this->medecin) {
            return collect();
        }

        return DisponibiliteMedecin::where('medecin_id', $this->medecin->id)
            ->where('jour_semaine', Carbon::parse($this->dateSelected)->dayOfWeekIso)
            ->get();
    }

    public function getJourSemaineProperty()
    {
        return Carbon::parse($this->dateSelected)->translatedFormat('l');
    }
}; ?>

<div>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (!$this->medecin)
                <!-- Erreur profil médecin -->
                <div
                    class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-6 text-center">
                    <svg class="mx-auto h-12 w-12 text-red-600 dark:text-red-500" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-red-800 dark:text-red-400">Profil médecin non trouvé</h3>
                    <p class="mt-2 text-red-700 dark:text-red-300">
                        Votre compte utilisateur n'est pas associé à un profil médecin.
                    </p>
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">
                        Veuillez contacter l'administrateur pour résoudre ce problème.
                    </p>
                </div>
            @else
                <!-- En-tête -->
                <div class="mb-8">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Mon Agenda</h1>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                Dr {{ $this->medecin->user->name }} - Gérez vos rendez-vous au quotidien
                            </p>
                        </div>
                        <flux:button variant="primary" icon="plus" wire:click="openCreateModal"
                            class="cursor-pointer inline-flex items-center px-4 py-2 bg-gradient-to-br from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 border border-transparent rounded-lg font-semibold text-white shadow-sm transition-all duration-200 focus:outline-none">
                            Nouveau Rendez-vous
                        </flux:button>
                    </div>
                </div>

                <!-- Alertes -->
                @if (session()->has('message'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => { show = false }, 5000)"
                        x-transition:enter="transition ease-out duration-500"
                        x-transition:enter-start="opacity-0 translate-y-3 scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-8"
                        class="mb-6 p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-3" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-green-800 dark:text-green-300 font-medium">{{ session('message') }}</span>
                        </div>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => { show = false }, 5000)"
                        x-transition:enter="transition ease-out duration-500"
                        x-transition:enter-start="opacity-0 translate-y-3 scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-8"
                        class="mb-6 p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-3" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                            <span class="text-red-800 dark:text-red-300 font-medium">{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                <!-- Sélecteur de date -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                Date sélectionnée :
                            </label>
                            <input type="date" wire:model="dateSelected" wire:change="loadRendezVous"
                                class="block rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 py-2 px-3 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:text-white text-sm">
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            {{ \Carbon\Carbon::parse($dateSelected)->translatedFormat('l d F Y') }}
                        </div>
                    </div>
                </div>

                <!-- Grille Agenda -->
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                    <!-- Rendez-vous du jour -->
                    <div class="xl:col-span-2">
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Rendez-vous du jour
                                </h2>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                    {{ $rendezvous->count() }} RDV
                                </span>
                            </div>

                            @if ($rendezvous->count() > 0)
                                <div class="space-y-4">
                                    @foreach ($rendezvous as $rdv)
                                        <div
                                            class="bg-gradient-to-br from-gray-50 to-white dark:from-gray-700/50 dark:to-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 p-4 hover:shadow-md transition-all duration-200">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <div class="flex items-center justify-between mb-2">
                                                        <h3 class="font-semibold text-gray-900 dark:text-white text-lg">
                                                            {{ $rdv->patient->full_name }}
                                                        </h3>
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                            {{ $rdv->statut === 'confirme'
                                                                ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
                                                                : ($rdv->statut === 'planifie'
                                                                    ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300'
                                                                    : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300') }}">
                                                            {{ $rdv->statut }}
                                                        </span>
                                                    </div>

                                                    <div
                                                        class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-gray-600 dark:text-gray-400">
                                                        <div class="flex items-center gap-2">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                            <span class="font-mono">{{ $rdv->heure }}</span>
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                            </svg>
                                                            <span>{{ $rdv->motif }}</span>
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                            </svg>
                                                            <span>{{ \App\Models\Rendezvous::TYPES_CONSULTATION[$rdv->type_consultation] }}</span>
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                                            </svg>
                                                            <span>{{ number_format($rdv->prix_consultation, 2, ',', ' ') }}
                                                                €</span>
                                                        </div>
                                                    </div>

                                                    @if ($rdv->notes)
                                                        <div
                                                            class="mt-3 p-2 bg-yellow-50 dark:bg-yellow-900/20 rounded border border-yellow-200 dark:border-yellow-800">
                                                            <p class="text-xs text-yellow-800 dark:text-yellow-300">
                                                                {{ $rdv->notes }}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div
                                                class="flex items-center justify-end gap-2 mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                                                @if ($rdv->estPlanifie())
                                                    <button wire:click="confirmerRendezVous({{ $rdv->id }})"
                                                        class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-green-700 dark:text-green-300 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/50 transition-colors">
                                                        <svg class="w-4 h-4 mr-1" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        Confirmer
                                                    </button>
                                                @endif
                                                <flux:button variant="primary" icon="pencil-square"
                                                    wire:click="openEditModal({{ $rdv->id }})"
                                                    class="cursor-pointer inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                                                    Modifier
                                                </flux:button>
                                                <flux:button variant="primary" icon="x-mark"
                                                    wire:click="openAnnulerModal({{ $rdv->id }})"
                                                    class="cursor-pointer inline-flex items-center px-3 py-1.5 text-sm font-medium text-red-700 dark:text-red-300 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors">
                                                    Annuler
                                                </flux:button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Aucun
                                        rendez-vous</h3>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                        Aucun rendez-vous n'est programmé pour cette date.
                                    </p>
                                    <flux:button variant="primary" wire:click="openCreateModal"
                                        class="cursor-pointer mt-4">
                                        Planifier un rendez-vous
                                    </flux:button>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Disponibilités -->
                    <div class="xl:col-span-1">
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h2
                                    class="text-xl font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Disponibilités
                                </h2>
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $this->jourSemaine }}
                                </span>
                            </div>

                            @if ($this->disponibilites->count() > 0)
                                <div class="space-y-3">
                                    @foreach ($this->disponibilites as $dispo)
                                        <div
                                            class="bg-gradient-to-br from-green-50 to-white dark:from-green-900/20 dark:to-gray-800 rounded-lg border border-green-200 dark:border-green-800 p-4">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                                    <span
                                                        class="font-mono text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $dispo->heure_debut->format('H:i') }} -
                                                        {{ $dispo->heure_fin->format('H:i') }}
                                                    </span>
                                                </div>
                                                @if ($dispo->est_exception)
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300">
                                                        Exception
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                        Disponible
                                                    </span>
                                                @endif
                                            </div>
                                            @if ($dispo->est_exception)
                                                <p class="mt-2 text-xs text-orange-600 dark:text-orange-400">
                                                    {{ $dispo->raison_exception }}
                                                </p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-8 w-8 text-gray-400 dark:text-gray-500" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                        Aucune disponibilité définie pour ce jour.
                                    </p>
                                </div>
                            @endif

                            <!-- Résumé de la journée -->
                            <div
                                class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                <h4 class="text-sm font-medium text-blue-900 dark:text-blue-100 mb-2">Résumé du jour
                                </h4>
                                <div class="space-y-2 text-sm text-blue-800 dark:text-blue-300">
                                    <div class="flex justify-between">
                                        <span>Rendez-vous planifiés :</span>
                                        <span
                                            class="font-medium">{{ $rendezvous->where('statut', 'planifie')->count() }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Rendez-vous confirmés :</span>
                                        <span
                                            class="font-medium">{{ $rendezvous->where('statut', 'confirme')->count() }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Total des créneaux :</span>
                                        <span class="font-medium">{{ $this->disponibilites->count() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de création de rendez-vous -->
    <flux:modal name="create-rendezvous" class="md:w-900">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Nouveau rendez-vous</flux:heading>
                <flux:text class="mt-2">Planifier un nouveau rendez-vous avec un patient.</flux:text>
            </div>

            <flux:select wire:model="form.patient_id" label="Patient" placeholder="Sélectionner un patient...">
                @foreach ($patients as $patient)
                    <flux:select.option value="{{ $patient->id }}">{{ $patient->full_name }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:input type="datetime-local" wire:model="form.date_heure" label="Date et heure" />

            <flux:input type="number" wire:model="form.duree" label="Durée (minutes)" min="15"
                step="15" />

            <flux:textarea wire:model="form.motif" label="Motif de la consultation" rows="3" />

            <flux:select wire:model="form.type_consultation" label="Type de consultation">
                @foreach (\App\Models\Rendezvous::TYPES_CONSULTATION as $value => $label)
                    <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:input type="number" wire:model="form.prix_consultation" label="Prix de la consultation (€)"
                step="0.01" />

            <flux:textarea wire:model="form.notes" label="Notes supplémentaires" rows="" />

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost" class="cursor-pointer">
                        Annuler
                    </flux:button>
                </flux:modal.close>
                <flux:button variant="primary" wire:click="saveRendezVous" class="cursor-pointer">
                    Créer le rendez-vous
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Modal d'édition de rendez-vous -->
    <flux:modal name="edit-rendezvous" class="md:w-900">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Modifier le rendez-vous</flux:heading>
                <flux:text class="mt-2">Modifier les détails du rendez-vous.</flux:text>
            </div>

            <flux:select wire:model="form.patient_id" label="Patient" placeholder="Sélectionner un patient...">
                @foreach ($patients as $patient)
                    <flux:select.option value="{{ $patient->id }}">
                        {{ $patient->full_name }}
                    </flux:select.option>
                @endforeach
            </flux:select>

            <flux:input type="datetime-local" wire:model="form.date_heure" label="Date et heure" />

            <flux:input type="number" wire:model="form.duree" label="Durée (minutes)" min="15"
                step="15" />

            <flux:textarea wire:model="form.motif" label="Motif de la consultation" rows="3" />

            <flux:select wire:model="form.type_consultation" label="Type de consultation">
                @foreach (\App\Models\Rendezvous::TYPES_CONSULTATION as $value => $label)
                    <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:input type="number" wire:model="form.prix_consultation" label="Prix de la consultation (€)"
                step="0.01" />

            <flux:textarea wire:model="form.notes" label="Notes supplémentaires" rows="3" />

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost" class="cursor-pointer">
                        Annuler
                    </flux:button>
                </flux:modal.close>
                <flux:button variant="primary" wire:click="saveRendezVous" class="cursor-pointer">
                    Enregistrer les modifications
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Modal d'annulation de rendez-vous -->
    <flux:modal name="annuler-rendezvous" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Annulation du rendez-vous</flux:heading>
                <flux:text class="mt-2">
                    Êtes-vous sûr de vouloir annuler ce rendez-vous ? Cette action enverra une notification au patient.
                </flux:text>

                @if ($currentRendezVous)
                    <div
                        class="mt-4 p-4 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg">
                        <div class="text-sm text-orange-800 dark:text-orange-300">
                            <div class="font-semibold">Détails du rendez-vous :</div>
                            <div class="mt-1">
                                <strong>Patient:</strong> {{ $currentRendezVous->patient->full_name }}<br>
                                <strong>Date:</strong> {{ $currentRendezVous->date_heure->format('d/m/Y H:i') }}<br>
                                <strong>Motif:</strong> {{ $currentRendezVous->motif }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost" class="cursor-pointer">
                        Retour
                    </flux:button>
                </flux:modal.close>
                <flux:button variant="primary" icon="x-mark" wire:click="annulerRendezVous"
                    class="cursor-pointer bg-orange-600 hover:bg-orange-700 text-white">
                    Confirmer l'annulation
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
