<?php


namespace App\Livewire\Forms;

use Livewire\Form;
use Livewire\Attributes\Validate;
use Propaganistas\LaravelPhone\PhoneNumber;
use Nnjeim\World\Models\Country;

class ContactUsForm extends Form
{
    #[Validate]
    public $firstname = '';

    #[Validate]
    public $lastname = '';

    #[Validate]
    public $email = '';

    #[Validate]
    public $phone = '';

    #[Validate]
    public $message = '';

    #[Validate]
    public $terms = '';

    #[Validate]
    public $country_id = '';

    #[Validate]
    public $city_id  = '';

    #[Validate]
    public $subject_id  = '';

    #[Validate]
    public $phoneCode = '';

    #[Validate]
    public $iso2 = '';

    public function rules(): array
    {
        return [
            'firstname' => 'required|min:2|max:50',
            'lastname' => 'required|min:2|max:50',
            'email' => 'required|email|max:255',
            'phone' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!$this->country_id) {
                        $fail('Sélectionnez d\'abord un pays');
                        return;
                    }

                    $country = Country::find($this->country_id);

                    try {
                        $phoneNumber = new PhoneNumber($value, $country->iso2);
                        if (!$phoneNumber->isValid()) {
                            $fail('Format invalide pour le pays : ' . $country->name);
                        }
                    } catch (\Exception $e) {
                        $fail('Erreur de validation téléphonique');
                    }
                }
            ],
            'country_id' => 'required|exists:countries,id',
            'city_id' => 'required|exists:cities,id,country_id,' . $this->country_id,
            'subject_id' => 'required|exists:subjects,id',
            'message' => 'required|min:10|max:1000'
        ];
    }

    protected function messages(): array
    {
        return [
            // Firstname
            'firstname.required' => 'Le nom est obligatoire.',
            'firstname.min' => 'Le nom doit contenir au moins 2 caractères.',
            'firstname.max' => 'Le nom ne peut dépasser 50 caractères.',

            // Lastname
            'lastname.required' => 'Le prénom est obligatoire.',
            'lastname.min' => 'Le prénom doit contenir au moins 2 caractères.',
            'lastname.max' => 'Le prénom ne peut dépasser 50 caractères.',

            // Email
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'Veuillez entrer une adresse email valide.',
            'email.max' => 'L\'email ne peut dépasser 255 caractères.',

            // Phone
            'phone.required' => 'Le numéro de téléphone est obligatoire.',
            'phone.phone' => 'Le format du numéro de téléphone est invalide.',

            // Country
            'country_id.required' => 'La sélection d\'un pays est obligatoire.',
            'country_id.exists' => 'Pays sélectionné invalide.',

            // City
            'city_id.required' => 'La sélection d\'une ville est obligatoire.',
            'city_id.exists' => 'Ville sélectionnée invalide.',

            // Subject
            'subject_id.required' => 'Le sujet est obligatoire.',
            'subject_id.exists' => 'Ville sélectionnée invalide.',

            // Message
            'message.required' => 'Le message est obligatoire.',
            'message.min' => 'Le message doit contenir au moins 10 caractères.',
            'message.max' => 'Le message ne peut dépasser 1000 caractères.',

        ];
    }
}
