<x-layouts.app :title="__('Tableau de Bord Médecin')">
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- En-tête -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Tableau de Bord</h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Aperçu de votre activité médicale
                </p>
            </div>

            <!-- Widgets de statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Rendez-vous aujourd'hui -->
                <div
                    class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl border border-blue-200 dark:border-blue-800 p-6 hover:shadow-lg transition-all duration-200">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 dark:bg-blue-800/50 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Rendez-vous aujourd'hui</p>
                            <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ $rdvAujourdhui }}</p>
                        </div>
                    </div>
                    <div class="mt-3 text-xs text-blue-600 dark:text-blue-400 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                        Planifiés pour aujourd'hui
                    </div>
                </div>

                <!-- Rendez-vous de la semaine -->
                <div
                    class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-xl border border-green-200 dark:border-green-800 p-6 hover:shadow-lg transition-all duration-200">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 dark:bg-green-800/50 rounded-lg">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-green-600 dark:text-green-400">Cette semaine</p>
                            <p class="text-2xl font-bold text-green-900 dark:text-green-100">{{ $rdvSemaine }}</p>
                        </div>
                    </div>
                    <div class="mt-3 text-xs text-green-600 dark:text-green-400 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                        </svg>
                        Sur les 7 prochains jours
                    </div>
                </div>

                <!-- Patients totaux -->
                <div
                    class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-xl border border-purple-200 dark:border-purple-800 p-6 hover:shadow-lg transition-all duration-200">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 dark:bg-purple-800/50 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-purple-600 dark:text-purple-400">Patients totaux</p>
                            <p class="text-2xl font-bold text-purple-900 dark:text-purple-100">{{ $totalPatients }}</p>
                        </div>
                    </div>
                    <div class="mt-3 text-xs text-purple-600 dark:text-purple-400 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Dans votre patientèle
                    </div>
                </div>

                <!-- Revenus du mois -->
                <div
                    class="bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 rounded-xl border border-orange-200 dark:border-orange-800 p-6 hover:shadow-lg transition-all duration-200">
                    <div class="flex items-center">
                        <div class="p-3 bg-orange-100 dark:bg-orange-800/50 rounded-lg">
                            <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-orange-600 dark:text-orange-400">Revenus du mois</p>
                            <p class="text-2xl font-bold text-orange-900 dark:text-orange-100">
                                {{ number_format($revenusMois, 2, ',', ' ') }} €
                            </p>
                        </div>
                    </div>
                    <div class="mt-3 text-xs text-orange-600 dark:text-orange-400 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Consultations payées
                    </div>
                </div>
            </div>

            <!-- Graphiques et tableaux -->
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 mb-8">
                <!-- Prochains rendez-vous -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Prochains rendez-vous
                        </h3>
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $prochainsRendezVous->count() }} à venir
                        </span>
                    </div>
                    <div class="space-y-4">
                        @forelse($prochainsRendezVous as $rdv)
                            <div
                                class="bg-gradient-to-br from-gray-50 to-white dark:from-gray-700/50 dark:to-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 p-4 hover:shadow-md transition-all duration-200">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-2">
                                            <h4 class="font-semibold text-gray-900 dark:text-white">
                                                {{ $rdv->patient->full_name }}
                                            </h4>
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
                                        <div class="grid grid-cols-2 gap-4 text-sm text-gray-600 dark:text-gray-400">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <span>{{ $rdv->date }}</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span>{{ $rdv->heure }}</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                </svg>
                                                <span>{{ \App\Models\Rendezvous::TYPES_CONSULTATION[$rdv->type_consultation] ?? $rdv->type_consultation }}</span>
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
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Aucun rendez-vous à
                                    venir</h3>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    Vous n'avez pas de rendez-vous programmé pour les prochains jours.
                                </p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Statistiques des rendez-vous -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Statistiques des rendez-vous
                        </h3>
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            Total: {{ $totalRendezVous }}
                        </span>
                    </div>
                    <div class="space-y-4">
                        @foreach ($statsRendezVous as $statut => $count)
                            @php
                                $percentage = $totalRendezVous > 0 ? ($count / $totalRendezVous) * 100 : 0;
                                $colors = [
                                    'planifie' => ['bg' => 'bg-blue-500', 'text' => 'text-blue-600 dark:text-blue-400'],
                                    'confirme' => [
                                        'bg' => 'bg-green-500',
                                        'text' => 'text-green-600 dark:text-green-400',
                                    ],
                                    'annule' => ['bg' => 'bg-red-500', 'text' => 'text-red-600 dark:text-red-400'],
                                    'termine' => ['bg' => 'bg-gray-500', 'text' => 'text-gray-600 dark:text-gray-400'],
                                    'absent' => [
                                        'bg' => 'bg-yellow-500',
                                        'text' => 'text-yellow-600 dark:text-yellow-400',
                                    ],
                                ];
                                $color = $colors[$statut] ?? [
                                    'bg' => 'bg-gray-500',
                                    'text' => 'text-gray-600 dark:text-gray-400',
                                ];
                            @endphp
                            <div class="flex items-center justify-between group">
                                <div class="flex items-center gap-3 flex-1">
                                    <div class="w-3 h-3 {{ $color['bg'] }} rounded-full"></div>
                                    <span
                                        class="text-sm font-medium text-gray-700 dark:text-gray-300 capitalize min-w-24">
                                        {{ \App\Models\Rendezvous::STATUTS[$statut] ?? $statut }}
                                    </span>
                                    <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="h-2 rounded-full {{ $color['bg'] }} transition-all duration-500 ease-out"
                                            style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 ml-4">
                                    <span
                                        class="text-sm font-semibold {{ $color['text'] }}">{{ $count }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 w-12 text-right">
                                        {{ number_format($percentage, 1) }}%
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Légende -->
                    <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex flex-wrap gap-4 text-xs text-gray-500 dark:text-gray-400">
                            @foreach ($statsRendezVous as $statut => $count)
                                @php
                                    $color = $colors[$statut] ?? ['bg' => 'bg-gray-500'];
                                @endphp
                                <div class="flex items-center gap-1">
                                    <div class="w-2 h-2 {{ $color['bg'] }} rounded-full"></div>
                                    <span>{{ \App\Models\Rendezvous::STATUTS[$statut] ?? $statut }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Actions rapides
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('medecin.agenda') }}"
                        class="group bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 border border-blue-200 dark:border-blue-800 rounded-xl p-6 text-center hover:shadow-lg hover:scale-105 transition-all duration-200">
                        <div
                            class="w-12 h-12 bg-blue-100 dark:bg-blue-800 rounded-lg flex items-center justify-center mx-auto mb-3 group-hover:bg-blue-200 dark:group-hover:bg-blue-700 transition-colors">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h4 class="font-semibold text-blue-900 dark:text-blue-100 mb-1">Voir l'agenda</h4>
                        <p class="text-sm text-blue-600 dark:text-blue-400">Consulter votre planning</p>
                    </a>

                    <a href="{{ route('medecin.rendezvous') }}"
                        class="group bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 border border-green-200 dark:border-green-800 rounded-xl p-6 text-center hover:shadow-lg hover:scale-105 transition-all duration-200">
                        <div
                            class="w-12 h-12 bg-green-100 dark:bg-green-800 rounded-lg flex items-center justify-center mx-auto mb-3 group-hover:bg-green-200 dark:group-hover:bg-green-700 transition-colors">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h4 class="font-semibold text-green-900 dark:text-green-100 mb-1">Gérer les rendez-vous</h4>
                        <p class="text-sm text-green-600 dark:text-green-400">Tous vos rendez-vous</p>
                    </a>

                    <a href="{{ route('medecin.patients') }}"
                        class="group bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 border border-purple-200 dark:border-purple-800 rounded-xl p-6 text-center hover:shadow-lg hover:scale-105 transition-all duration-200">
                        <div
                            class="w-12 h-12 bg-purple-100 dark:bg-purple-800 rounded-lg flex items-center justify-center mx-auto mb-3 group-hover:bg-purple-200 dark:group-hover:bg-purple-700 transition-colors">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h4 class="font-semibold text-purple-900 dark:text-purple-100 mb-1">Voir les patients</h4>
                        <p class="text-sm text-purple-600 dark:text-purple-400">Votre patientèle</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
