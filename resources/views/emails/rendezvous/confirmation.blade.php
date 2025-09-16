<!DOCTYPE html>
<html>

<head>
    <title>{{ $subject }}</title>
    @include('partials.head-emails')
</head>

<body class="bg-gray-50">
    <div class="email-container">
        <!-- Header -->
        <div class="email-header bg-success-500">
            <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold">Confirmation de Rendez-vous</h1>
            <p class="text-white opacity-90 mt-2">Votre rendez-vous est confirmé</p>
        </div>

        <!-- Content -->
        <div class="email-content">
            <div class="text-center mb-6">
                <p class="text-gray-600">
                    @if ($destinataire === 'patient')
                        Bonjour <span class="font-semibold text-gray-800">{{ $rendezvous->patient->user->name }}</span>,
                    @else
                        Bonjour <span class="font-semibold text-gray-800">Dr
                            {{ $rendezvous->medecin->user->name }}</span>,
                    @endif
                </p>
                <p class="text-lg font-medium text-gray-800 mt-2">
                    @if ($destinataire === 'patient')
                        Votre rendez-vous a été confirmé avec succès
                    @else
                        Nouveau rendez-vous confirmé
                    @endif
                </p>
            </div>

            <div class="details-card">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">✅ Détails du rendez-vous confirmé</h3>

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
                        <span class="text-gray-600">👤 Patient :</span>
                        <span class="font-medium text-gray-800">{{ $rendezvous->patient->user->name }}</span>
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
                        <span class="text-gray-600">⏱️ Durée :</span>
                        <span class="font-medium text-gray-800">{{ $rendezvous->duree }} minutes</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600">💰 Tarif :</span>
                        <span
                            class="font-medium text-gray-800">{{ number_format($rendezvous->prix_consultation, 2, ',', ' ') }}
                            €</span>
                    </div>
                </div>
            </div>

            @if ($destinataire === 'patient')
                <div class="text-center">
                    <p class="text-gray-600 mb-4">Vous recevrez un rappel 24 heures avant votre rendez-vous.</p>

                    <div class="bg-blue-50 rounded-lg p-4 mb-4">
                        <p class="text-blue-800 font-medium">📞 Contactez-nous pour toute modification :</p>
                        <p class="text-lg font-semibold text-blue-900">01 23 45 67 89</p>
                    </div>

                    <a href="#" {{-- <a href="{{ route('mes-rendezvous') }}" --}}
                        class="inline-block bg-primary-500 hover:bg-primary-600 text-white font-medium py-3 px-8 rounded-lg transition-colors mb-4">
                        Voir mes rendez-vous
                    </a>
                </div>
            @else
                <p class="text-gray-600 text-center">Le patient a été informé de cette confirmation.</p>
            @endif
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p>© {{ date('Y') }} Centre Médical. Tous droits réservés.</p>
            <p class="mt-2">
                <a href="#" {{-- <a href="{{ route('unsubscribe') }}"  --}} class="text-primary-600 hover:text-primary-700 text-sm">
                    Se désabonner des notifications
                </a>
            </p>
        </div>
    </div>
</body>

</html>
