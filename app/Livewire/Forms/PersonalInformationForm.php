<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class PersonalInformationForm extends Form
{
    #[Validate]
    public $firstname = '';

    #[Validate]
    public $lastname = '';

    #[Validate]
    public $email = '';

    public function rules(): array
    {
        return [
            'firstname' => 'required|min:2|max:50',
            'lastname' => 'required|min:2|max:50',
            'email' => 'required|email|max:255',
        ];
    }

    protected function messages(): array
    {
        return [
            'firstname.required' => 'Le nom est obligatoire.',
            'firstname.min' => 'Le nom doit contenir au moins 2 caractères.',
            'firstname.max' => 'Le nom ne peut dépasser 50 caractères.',
            'lastname.required' => 'Le prénom est obligatoire.',
            'lastname.min' => 'Le prénom doit contenir au moins 2 caractères.',
            'lastname.max' => 'Le prénom ne peut dépasser 50 caractères.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'Veuillez entrer une adresse email valide.',
            'email.max' => 'L\'email ne peut dépasser 255 caractères.',
        ];
    }
}

