<?php

use Livewire\Volt\Component;
use App\Models\Rendezvous;
use App\Models\Medecin;
use Illuminate\Support\Facades\Auth;
use Flux\Flux;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $medecin;
    public $filters = [
        'statut' => '',
        'date_debut' => '',
        'date_fin' => '',
        'type_consultation' => '',
    ];

    public $rdvToCancel = null;

    public function mount()
    {
        $this->medecin = $this->getMedecinProfile();
    }

    public function getMedecinProfile()
    {
        if (!Auth::user()->hasRole(['medecin', 'Medecin Chef Service', 'Super Admin'])) {
            return null;
        }
        return Medecin::where('user_id', Auth::id())->first();
    }

    public function getRendezVousProperty()
    {
        if (!$this->medecin) {
            return collect();
        }

        $query = Rendezvous::with(['patient.user', 'service'])
            ->pourMedecin($this->medecin->id)
            ->when($this->filters['statut'], function ($query, $statut) {
                return $query->where('statut', $statut);
            })
            ->when($this->filters['type_consultation'], function ($query, $type) {
                return $query->where('type_consultation', $type);
            })
            ->when($this->filters['date_debut'], function ($query, $date) {
                return $query->whereDate('date_heure', '>=', $date);
            })
            ->when($this->filters['date_fin'], function ($query, $date) {
                return $query->whereDate('date_heure', '<=', $date);
            });

        return $query->orderBy('date_heure', 'desc')->paginate(10);
    }

    public function confirmer($id)
    {
        $rdv = Rendezvous::find($id);
        $rdv->confirmer();
        session()->flash('message', 'Rendez-vous confirmé avec succès');
        $this->dispatch('rdv-updated');
    }

    public function openAnnulerModal($id)
    {
        $this->rdvToCancel = Rendezvous::find($id);
        Flux::modal('annuler-rendezvous')->show();
    }

    public function annulerRendezVous()
    {
        if ($this->rdvToCancel) {
            $this->rdvToCancel->annuler('Annulé par le médecin');
            session()->flash('message', 'Rendez-vous annulé avec succès');
            $this->dispatch('rdv-updated');
            Flux::modal('annuler-rendezvous')->close();
            $this->rdvToCancel = null;
        }
    }

    public function terminer($id)
    {
        $rdv = Rendezvous::find($id);
        $rdv->update(['statut' => 'termine']);
        session()->flash('message', 'Rendez-vous marqué comme terminé');
        $this->dispatch('rdv-updated');
    }

    public function resetFilters()
    {
        $this->filters = [
            'statut' => '',
            'date_debut' => '',
            'date_fin' => '',
            'type_consultation' => '',
        ];
    }
}; ?>

<div>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- En-tête -->
            <div class="mb-8">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Gestion des Rendez-vous</h1>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            Gérez tous vos rendez-vous en un seul endroit
                        </p>
                    </div>
                    <div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                        <span class="flex items-center gap-1">
                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                            Total: {{ $this->rendezVous->total() }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Alertes -->
            @if (session()->has('message'))
                <div x-cloak x-data="{ show: true }" x-show="show" x-init="setTimeout(() => { show = false }, 5000)"
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

            <!-- Filtres -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 flex-1">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Statut</label>
                            <select wire:model.live="filters.statut"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 py-2 px-3 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:text-white text-sm">
                                <option value="">Tous les statuts</option>
                                @foreach (\App\Models\Rendezvous::STATUTS as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type</label>
                            <select wire:model.live="filters.type_consultation"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 py-2 px-3 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:text-white text-sm">
                                <option value="">Tous les types</option>
                                @foreach (\App\Models\Rendezvous::TYPES_CONSULTATION as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date
                                début</label>
                            <input type="date" wire:model.live="filters.date_debut"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 py-2 px-3 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:text-white text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date
                                fin</label>
                            <input type="date" wire:model.live="filters.date_fin"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 py-2 px-3 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:text-white text-sm">
                        </div>

                        <div class="flex items-end">
                            <flux:button wire:click="resetFilters"
                                class="cursor-pointer w-full py-2 px-4 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors focus:outline-none">
                                Réinitialiser
                            </flux:button>
                        </div>
                    </div>
                </div>

                <!-- Indicateurs de filtres actifs -->
                @if (array_filter($filters))
                    <div class="mt-4 flex flex-wrap gap-2">
                        <span class="text-xs text-gray-500 dark:text-gray-400">Filtres actifs:</span>
                        @if ($filters['statut'])
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                Statut: {{ \App\Models\Rendezvous::STATUTS[$filters['statut']] ?? $filters['statut'] }}
                            </span>
                        @endif
                        @if ($filters['type_consultation'])
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                Type:
                                {{ \App\Models\Rendezvous::TYPES_CONSULTATION[$filters['type_consultation']] ?? $filters['type_consultation'] }}
                            </span>
                        @endif
                        @if ($filters['date_debut'])
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300">
                                À partir du: {{ \Carbon\Carbon::parse($filters['date_debut'])->format('d/m/Y') }}
                            </span>
                        @endif
                        @if ($filters['date_fin'])
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300">
                                Jusqu'au: {{ \Carbon\Carbon::parse($filters['date_fin'])->format('d/m/Y') }}
                            </span>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Tableau des rendez-vous -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                @if ($this->rendezVous->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Patient
                                    </th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Date & Heure
                                    </th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Type
                                    </th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Statut
                                    </th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Prix
                                    </th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($this->rendezVous as $rdv)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div
                                                    class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                                    {{ substr($rdv->patient->full_name, 0, 1) }}
                                                </div>
                                                <div class="ml-4">
                                                    <div
                                                        class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                                        {{ $rdv->patient->full_name }}
                                                    </div>
                                                    <div
                                                        class="text-sm text-gray-500 dark:text-gray-400 line-clamp-1 max-w-xs">
                                                        {!! \Illuminate\Support\Str::limit($rdv->motif, 20) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-white font-medium">
                                                {{ $rdv->date_heure->format('d/m/Y') }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400 font-mono">
                                                {{ $rdv->date_heure->format('H:i') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                                {{ \App\Models\Rendezvous::TYPES_CONSULTATION[$rdv->type_consultation] ?? $rdv->type_consultation }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusColors = [
                                                    'planifie' =>
                                                        'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                                    'confirme' =>
                                                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                                    'annule' =>
                                                        'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                                    'termine' =>
                                                        'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                                    'absent' =>
                                                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                                ];
                                                $statusColor =
                                                    $statusColors[$rdv->statut] ??
                                                    'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                                            @endphp
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                                                {{ \App\Models\Rendezvous::STATUTS[$rdv->statut] ?? $rdv->statut }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-white font-medium">
                                                {{ number_format($rdv->prix_consultation, 2, ',', ' ') }} €
                                            </div>
                                            <div
                                                class="text-xs {{ $rdv->est_paye ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                {{ $rdv->est_paye ? '✓ Payé' : '✗ En attente' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                @if ($rdv->estPlanifie())
                                                    <button wire:click="confirmer({{ $rdv->id }})"
                                                        wire:loading.attr="disabled"
                                                        class="inline-flex items-center px-3 py-1.5 border border-green-300 dark:border-green-700 rounded-md text-sm font-medium text-green-700 dark:text-green-300 bg-white dark:bg-gray-700 hover:bg-green-50 dark:hover:bg-green-900/20 transition-colors focus:outline-none">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        Confirmer
                                                    </button>
                                                @endif

                                                @if ($rdv->estPlanifie() || $rdv->estConfirme())
                                                    <button wire:click="openAnnulerModal({{ $rdv->id }})"
                                                        class="inline-flex items-center px-3 py-1.5 border border-red-300 dark:border-red-700 rounded-md text-sm font-medium text-red-700 dark:text-red-300 bg-white dark:bg-gray-700 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors focus:outline-none">
                                                        <svg class="w-4 h-4 mr-1" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                        Annuler
                                                    </button>
                                                @endif

                                                @if ($rdv->estConfirme())
                                                    <button wire:click="terminer({{ $rdv->id }})"
                                                        wire:loading.attr="disabled"
                                                        class="inline-flex items-center px-3 py-1.5 border border-blue-300 dark:border-blue-700 rounded-md text-sm font-medium text-blue-700 dark:text-blue-300 bg-white dark:bg-gray-700 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors focus:outline-none">
                                                        <svg class="w-4 h-4 mr-1" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        Terminer
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <!-- État vide -->
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Aucun rendez-vous trouvé
                        </h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            @if (array_filter($filters))
                                Aucun rendez-vous ne correspond à vos critères de recherche.
                            @else
                                Vous n'avez pas encore de rendez-vous programmé.
                            @endif
                        </p>
                        @if (array_filter($filters))
                            <button wire:click="resetFilters"
                                class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 border border-transparent rounded-lg font-semibold text-white shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Réinitialiser les filtres
                            </button>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Pagination -->
            @if ($this->rendezVous->hasPages())
                <div class="mt-6">
                    {{ $this->rendezVous->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Modal d'annulation de rendez-vous --}}
    <flux:modal name="annuler-rendezvous" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Annulation du rendez-vous</flux:heading>
                <flux:text class="mt-2">
                    Êtes-vous sûr de vouloir annuler ce rendez-vous ? Cette action enverra une notification au patient.
                </flux:text>

                @if ($rdvToCancel)
                    <div
                        class="mt-4 p-4 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg">
                        <div class="text-sm text-orange-800 dark:text-orange-300">
                            <div class="font-semibold">Détails du rendez-vous :</div>
                            <div class="mt-1">
                                <strong>Patient:</strong> {{ $rdvToCancel->patient->full_name }}<br>
                                <strong>Date:</strong> {{ $rdvToCancel->date_heure->format('d/m/Y H:i') }}<br>
                                <strong>Motif:</strong> {{ $rdvToCancel->motif }}
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
