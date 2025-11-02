<?php

use Livewire\Volt\Component;
use App\Models\Patient;
use App\Models\Rendezvous;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $search = '';

    public function getPatientsProperty()
    {
        return Patient::with([
            'user',
            'rendezvous' => function ($query) {
                $query->pourMedecin(Auth::user()->medecin->id);
            },
        ])
            ->whereHas('user', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->paginate(10);
    }

    public function getStatsProperty()
    {
        return [
            'total' => Patient::whereHas('rendezvous', function ($query) {
                $query->pourMedecin(Auth::user()->medecin->id);
            })->count(),
            'nouveaux_mois' => Patient::whereHas('rendezvous', function ($query) {
                $query->pourMedecin(Auth::user()->medecin->id)->whereMonth('created_at', now()->month);
            })->count(),
        ];
    }
}; ?>

<div>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- En-tête -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Mes Patients</h1>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            Gestion de votre liste de patients
                        </p>
                    </div>
                    <div class="mt-4 sm:mt-0">
                        <div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                            <span class="flex items-center gap-1">
                                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                Total: {{ $this->stats['total'] }}
                            </span>
                            <span class="flex items-center gap-1">
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                Nouveaux: {{ $this->stats['nouveaux_mois'] }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cartes de statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div
                    class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl border border-blue-200 dark:border-blue-800 p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 dark:bg-blue-800/50 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Total des patients</p>
                            <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ $this->stats['total'] }}
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-xl border border-green-200 dark:border-green-800 p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 dark:bg-green-800/50 rounded-lg">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-green-600 dark:text-green-400">Nouveaux patients ce mois
                            </p>
                            <p class="text-2xl font-bold text-green-900 dark:text-green-100">
                                {{ $this->stats['nouveaux_mois'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Barre de recherche et filtres -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" wire:model.live="search"
                                placeholder="Rechercher un patient par nom ou email..."
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 transition-colors">
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $this->patients->total() }} patient(s) trouvé(s)
                    </div>
                </div>
            </div>

            <!-- Tableau des patients -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
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
                                    Contact
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Informations médicales
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Dernier RDV
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Total RDV
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($this->patients as $patient)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                                {{ substr($patient->full_name, 0, 1) }}
                                            </div>
                                            <div class="ml-4">
                                                <div
                                                    class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                                    {{ $patient->full_name }}
                                                </div>
                                                <div
                                                    class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-1 mt-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    @if ($patient->age)
                                                        {{ $patient->age }} ans
                                                    @else
                                                        Âge non renseigné
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">{{ $patient->user->email }}
                                        </div>
                                        <div
                                            class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-1 mt-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                            {{ $patient->user->phone ?? 'Non renseigné' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="max-w-xs">
                                            <div class="text-sm text-gray-900 dark:text-white line-clamp-2">
                                                {{ $patient->antecedents_medicaux ?: 'Aucun antécédent' }}
                                            </div>
                                            @if ($patient->allergies)
                                                <div
                                                    class="mt-1 flex items-center gap-1 text-xs text-red-600 dark:text-red-400">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                                    </svg>
                                                    Allergies: {{ $patient->allergies }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $dernierRdv = $patient->rendezvous
                                                ->where('medecin_id', Auth::user()->medecin->id)
                                                ->sortByDesc('date_heure')
                                                ->first();
                                        @endphp
                                        @if ($dernierRdv)
                                            <div class="text-sm text-gray-900 dark:text-white font-medium">
                                                {{ $dernierRdv->date_heure->format('d/m/Y') }}
                                            </div>
                                            <div
                                                class="text-xs {{ $dernierRdv->estTermine() ? 'text-green-600 dark:text-green-400' : 'text-blue-600 dark:text-blue-400' }} font-medium">
                                                {{ \App\Models\Rendezvous::STATUTS[$dernierRdv->statut] }}
                                            </div>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                Aucun RDV
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-center">
                                            <span
                                                class="inline-flex items-center justify-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                                {{ $patient->rendezvous->where('medecin_id', Auth::user()->medecin->id)->count() }}
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- État vide -->
                @if ($this->patients->count() === 0)
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Aucun patient trouvé</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            @if ($search)
                                Aucun patient ne correspond à votre recherche "{{ $search }}".
                            @else
                                Vous n'avez pas encore de patients dans votre liste.
                            @endif
                        </p>
                    </div>
                @endif
            </div>

            <!-- Pagination -->
            @if ($this->patients->hasPages())
                <div class="mt-6">
                    {{ $this->patients->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
