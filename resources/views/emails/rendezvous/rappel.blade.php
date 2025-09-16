<!DOCTYPE html>
<html>

<head>
    <title>{{ $subject }}</title>
    @include('partials.head-emails')
</head>

<body class="bg-gray-50">
    <div class="email-container">
        <!-- Header -->
        <div class="email-header bg-primary">
            <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold">Rappel de Rendez-vous</h1>
            <p class="text-white opacity-90 mt-2">
                @if ($typeRappel === '24h')
                    Dans 24 heures
                @else
                    Dans 1 heure
                @endif
            </p>
        </div>

        <!-- Content -->
        <div class="email-content">
            <div class="text-center mb-6">
                <p class="text-gray-600">
                    Bonjour <span class="font-semibold text-gray-800">{{ $rendezvous->patient->user->name }}</span>,
                </p>
                <p class="text-lg font-medium text-gray-800 mt-2">
                    Nous vous rappelons votre rendez-vous médical
                </p>
            </div>

            <div class="details-card">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">📋 Détails du rendez-vous</h3>

                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">📅 Date :</span>
                        <span class="font-medium text-gray-800">{{ $rendezvous->date_heure->format('d/m/Y') }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600">⏰ Heure :</span>
                        <span class="font-medium text-gray-800">{{ $rendezvous->date_heure->format('H:i') }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600">👨‍⚕️ Médecin :</span>
                        <span class="font-medium text-gray-800">Dr {{ $rendezvous->medecin->user->name }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600">🏥 Service :</span>
                        <span class="font-medium text-gray-800">{{ $rendezvous->service->nom }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600">📝 Motif :</span>
                        <span class="font-medium text-gray-800">{{ $rendezvous->motif }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600">📍 Lieu :</span>
                        <span class="font-medium text-gray-800">{{ $rendezvous->service->nom }} - Bureau du Dr
                            {{ $rendezvous->medecin->user->name }}</span>
                    </div>
                </div>
            </div>

            <div class="important-note">
                <h4 class="font-semibold text-amber-500 mb-3">📋 Préparatifs recommandés :</h4>
                <ul class="space-y-2 text-orange-400">
                    <li class="flex items-start">
                        <span class="mr-2">•</span>
                        Arrivez 15 minutes avant l'heure du rendez-vous
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2">•</span>
                        Apportez votre carte vitale et pièce d'identité
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2">•</span>
                        Préparez vos questions pour le médecin
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2">•</span>
                        Apportez vos derniers examens si nécessaire
                    </li>
                </ul>
            </div>

            <div class="text-center mt-6">
                <p class="text-gray-600 mb-4">En cas d'empêchement, merci de nous prévenir au plus vite :</p>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-lg font-semibold text-gray-800">📞 01 23 45 67 89</p>
                    <p class="text-sm text-gray-600">Du lundi au vendredi, 8h-19h</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p>© {{ date('Y') }} Centre Médical. Tous droits réservés.</p>
            <p class="mt-2">
                <a href="#" {{-- <a href="{{ route('unsubscribe') }}"  --}} class="text-primary-600 hover:text-primary-700 text-sm">
                    Se désabonner des rappels
                </a>
            </p>
        </div>
    </div>
</body>

</html>
