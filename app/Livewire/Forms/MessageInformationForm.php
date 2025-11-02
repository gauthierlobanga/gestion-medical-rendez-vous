<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use Livewire\Attributes\Validate;

class MessageInformationForm extends Form
{
    #[Validate]
    public $subject_id = '';

    #[Validate]
    public $message = '';

    public function rules(): array
    {
        return [
            'subject_id' => 'required|exists:subjects,id',
            'message' => 'required|min:10|max:1000',
        ];
    }

    protected function messages(): array
    {
        return [
            'subject_id.required' => 'Le sujet est obligatoire.',
            'subject_id.exists' => 'Sujet sélectionné invalide.',
            'message.required' => 'Le message est obligatoire.',
            'message.min' => 'Le message doit contenir au moins 10 caractères.',
            'message.max' => 'Le message ne peut dépasser 1000 caractères.',
        ];
    }
}

