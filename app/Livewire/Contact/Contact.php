<?php

namespace App\Livewire\Contact;

use App\Models\Subject;
use Livewire\Component;
use Nnjeim\World\World;
use App\Mail\ContactMail;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Nnjeim\World\Models\Country;
use App\Events\ContactRequestEvent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Models\Contact as ContactModel;
use App\Livewire\Forms\MessageInformationForm;
use App\Livewire\Forms\LocationInformationForm;
use App\Livewire\Forms\PersonalInformationForm;

#[Title('Nxotech - Contact')]
#[Layout('layouts.main')]
class Contact extends Component
{
    public PersonalInformationForm $personalForm;
    public LocationInformationForm $locationForm;
    public MessageInformationForm $messageForm;

    public $step = 1;
    public $countries = [];
    public $cities = [];
    public array $subjects = [];
    public $loadingCities = false;


    public function toJSON()
    {
        return json_encode([]);
    }
    public function mount()
    {
        World::setLocale('fr');

        $this->countries = Cache::remember('countries', 3600, function () {
            return World::countries()->data->toArray();
        });

        $this->subjects = Cache::remember('subjects', 3600, function () {
            return Subject::all()->pluck('description', 'id')->toArray();
        });

        if ($this->locationForm->country_id) {
            $this->loadCities($this->locationForm->country_id);
        }
    }

    public function updatedLocationFormCountryId($country_id)
    {
        $this->loadingCities = true;
        $this->loadCities($country_id);

        $country = Country::find($country_id);
        if ($country) {
            $this->locationForm->phoneCode = $country->phone_code ?? '';
            $this->locationForm->iso2 = $country->iso2 ?? '';
        }

        $this->locationForm->phone = '';
        $this->locationForm->city_id = null;
        $this->loadingCities = false;
    }

    protected function loadCities($country_id)
    {
        $this->cities = Cache::remember(
            "cities_{$country_id}",
            86400,
            function () use ($country_id) {
                return World::cities([
                    'filters' => ['country_id' => $country_id],
                    'fields' => 'id,name'
                ])->data->toArray();
            }
        );
    }

    public function nextStep()
    {
        $this->validateCurrentStep();
        $this->step++;
    }

    public function previousStep()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function validateCurrentStep()
    {
        if ($this->step == 1) {
            $this->personalForm->validate();
        }

        if ($this->step == 2) {
            $this->locationForm->validate();
        }

        if ($this->step == 3) {
            $this->messageForm->validate();
        }
    }

    public function submitForm()
    {
        $validatedPersonal = $this->personalForm->validate();
        $validatedLocation = $this->locationForm->validate();
        $validatedMessage = $this->messageForm->validate();

        $validated = array_merge(
            $validatedPersonal,
            $validatedLocation,
            $validatedMessage
        );

        $validated['country_name'] = collect($this->countries)
            ->firstWhere('id', $validated['country_id'])['name'] ?? '';

        $validated['city_name'] = collect($this->cities)
            ->firstWhere('id', $validated['city_id'])['name'] ?? '';

        ContactModel::create($validated);

        // event(new ContactRequestEvent($this, $validated));

        try {
            Mail::to($validated['email'])->send(new ContactMail($validated));
        } catch (\Exception $e) {
            Log::error('Direct mail failed: ' . $e->getMessage());
        }

        session()->flash('success', 'Votre message a été envoyé avec succès. Merci de nous avoir contactés !');

        $this->personalForm->reset();
        $this->locationForm->reset();
        $this->messageForm->reset();
        $this->cities = [];

        $this->locationForm->iso2 = '';
        $this->locationForm->phoneCode = '';

        $this->step = 1;

        return $this->redirect('/contact', navigate: true);
    }

    public function render()
    {
        return view('livewire.contact.contact');
    }
}
