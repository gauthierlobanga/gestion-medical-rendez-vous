<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use Livewire\Attributes\Validate;
use Nnjeim\World\Models\Country;
use Propaganistas\LaravelPhone\PhoneNumber;

class LocationInformationForm extends Form
{
    #[Validate]
    public $country_id = '';

    #[Validate]
    public $city_id = '';

    #[Validate]
    public $phone = '';

    #[Validate]
    public $phoneCode = '';

    #[Validate]
    public $iso2 = '';

    public function rules(): array
    {
        return [
            'country_id' => 'required|exists:countries,id',
            'city_id' => 'required|exists:cities,id,country_id,' . $this->country_id,
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
        ];
    }

    protected function messages(): array
    {
        return [
            'country_id.required' => 'La sélection d\'un pays est obligatoire.',
            'country_id.exists' => 'Pays sélectionné invalide.',
            'city_id.required' => 'La sélection d\'une ville est obligatoire.',
            'city_id.exists' => 'Ville sélectionnée invalide.',
            'phone.required' => 'Le numéro de téléphone est obligatoire.',
        ];
    }
}
