<x-mail::message>
    # Bonjour {{ $data['firstname'] }} {{ $data['lastname'] }},

    Merci pour votre message. Voici un récapitulatif de votre demande :

    **Email :** {{ $data['email'] }}
    **Téléphone :** {{ $data['phone'] }}
    **Ville :** {{ $data['city_name'] }}
    **Pays :** {{ $data['country_name'] }}
    **Motif :** {{ $subjects[$data['subject_id']] ?? 'Non spécifié' }}
    **Message :**{{ $data['message'] }}

    Nous vous contacterons sous peu pour répondre à votre demande.

    Cordialement,
    L'équipe ArchiCyc.

    <x-mail::button :url="config('app.url')">
        Visitez notre site
    </x-mail::button>

    Merci,
    {{ config('app.name') }}
</x-mail::message>

{{-- "nnjeim/world": "^1.1",
        "propaganistas/laravel-phone": "^5.3", --}}
