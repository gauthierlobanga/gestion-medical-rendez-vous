<!DOCTYPE html>
<html>

<head>
    <title>{{ $subject }}</title>
    @include('partials.head-emails')
</head>

<body class="bg-gray-50">
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold">Annulation de Rendez-vous</h1>
            <p class="text-white opacity-90 mt-2">Votre rendez-vous a été annulé</p>
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
            </div>

            <div class="details-card">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Détails du rendez-vous annulé</h3>

                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Date et heure :</span>
                        <span
                            class="font-medium text-gray-800">{{ $rendezvous->date_heure->format('d/m/Y à H:i') }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600">Médecin :</span>
                        <span class="font-medium text-gray-800">Dr {{ $rendezvous->medecin->user->name }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600">Patient :</span>
                        <span class="font-medium text-gray-800">{{ $rendezvous->patient->user->name }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600">Service :</span>
                        <span class="font-medium text-gray-800">{{ $rendezvous->service->nom }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600">Motif initial :</span>
                        <span class="font-medium text-gray-800">{{ $rendezvous->motif }}</span>
                    </div>

                    @if ($raison)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Raison de l'annulation :</span>
                            <span class="font-medium text-red-600">{{ $raison }}</span>
                        </div>
                    @endif
                </div>
            </div>

            @if ($destinataire === 'patient')
                <div class="text-center">
                    <p class="text-gray-600 mb-4">Nous vous invitons à prendre un nouveau rendez-vous en contactant
                        notre secrétariat.</p>
                    <a href="#" {{-- <a href="{{ route('prendre-rendezvous') }}" --}}
                        class="inline-block bg-primary-500 hover:bg-primary-600 text-white font-medium py-2 px-6 rounded-lg transition-colors">
                        Prendre un nouveau rendez-vous
                    </a>
                </div>
            @else
                <p class="text-gray-600 text-center">Le patient a été informé de cette annulation.</p>
            @endif
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p>© {{ date('Y') }} Centre Médical. Tous droits réservés.</p>
            <p class="mt-1">Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
            <p class="mt-2">
                <a href="#" {{-- <a href="{{ route('unsubscribe') }}"  --}} class="text-primary-600 hover:text-primary-700 text-sm">
                    Se désabonner des notifications
                </a>
            </p>
        </div>
    </div>
</body>

</html>
