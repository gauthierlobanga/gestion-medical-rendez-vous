<?php

namespace App\Livewire\Contact;

use App\Models\Contact;
use App\Models\Subject;
use Nnjeim\World\World;
use Illuminate\Support\Facades\Cache;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;
use Filament\Notifications\Notification;
use App\Events\ContactRequestEvent;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Wizard;
use Illuminate\Support\Facades\Blade;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\RichEditor;

class CreateContact extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $contactData = [];
    public ?array $data = [];

    public function mount(): void
    {
        World::setLocale('fr');
        $this->form->fill();
    }

    public function contactForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('firstname')
                    ->placeholder('Entrer votre prénom')
                    ->label('Prénom')
                    ->required(),
                Forms\Components\TextInput::make('lastname')
                    ->placeholder('Entrer votre nom')
                    ->label('Nom')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->placeholder('Entrer votre email')
                    ->label('Adresse e-mail')
                    ->email()
                    ->required(),
                Forms\Components\Select::make('country_id')
                    ->label('Pays')
                    ->required()
                    ->placeholder('Sélectionnez le pays')
                    ->options(function () {
                        return Cache::remember('countries_formatted', 3600, function () {
                            $countries = World::countries()->data;

                            return collect($countries)
                                ->map(function ($country) {
                                    // Conversion en tableau associatif
                                    $countryArray = (array) $country;

                                    return [
                                        'id' => $countryArray['id'] ?? null,
                                        'name' => $countryArray['name'] ?? 'Inconnu',
                                        'phone_code' => $countryArray['phone_code'] ?? '',
                                    ];
                                })
                                ->pluck('name', 'id') // Garde seulement id => name pour les options
                                ->toArray();
                        });
                    })
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('city_id', null);
                        $set('phone', '');
                    }),

                Forms\Components\Select::make('city_id')
                    ->label('Ville')
                    ->required()
                    ->placeholder('Sélectionnez la ville')
                    ->options(function (callable $get) {
                        $countryId = $get('country_id');
                        if (!$countryId) return [];

                        return Cache::remember("cities_formatted_{$countryId}", 86400, function () use ($countryId) {
                            $cities = World::cities([
                                'filters' => ['country_id' => $countryId],
                                'fields' => 'id,name'
                            ])->data;

                            return collect($cities)
                                ->map(function ($city) {
                                    $cityArray = (array) $city;
                                    return [
                                        'id' => $cityArray['id'] ?? null,
                                        'name' => $cityArray['name'] ?? 'Inconnu',
                                    ];
                                })
                                ->pluck('name', 'id')
                                ->toArray();
                        });
                    })
                    ->searchable()
                    ->live()
                    ->loadingMessage('Chargement des villes...'),

                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->mask('999 99 999 9999')
                    ->placeholder('55 555 5555')
                    ->required()
                    ->prefix(function (callable $get) {
                        $countryId = $get('country_id');
                        $countries = Cache::remember('countries_full_data', 3600, function () {
                            return World::countries()->data;
                        });

                        $country = collect($countries)
                            ->firstWhere('id', $countryId);

                        return $country ? ($country->phone_code ?? $country['phone_code'] ?? '') : '';
                    })
                    ->prefixIcon('heroicon-o-phone'),

                Forms\Components\Select::make('subject_id')
                    ->label('Sujet')
                    ->placeholder('Sélectionnez un sujet')
                    ->required()
                    ->preload()
                    ->options(Subject::pluck('description', 'id'))
                    ->searchable(),

                Forms\Components\Textarea::make('message')
                    ->required()
                    ->rows(6)
                    ->placeholder('Décrivez votre besoin ou question ici...')
                    ->helperText(str('Your **Modelsessage** here, including any middle names.')->inlineMarkdown()->toHtmlString())
                    ->columnSpanFull(),
            ])->columns(2)
            ->statePath('contactData')
            ->model(Contact::class)
            ->extraAttributes(['class' => 'flex justify-center w-full max-w-7xl space-y-4']);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Informations personnelles')
                        ->completedIcon('heroicon-m-hand-thumb-up')
                        ->icon('heroicon-m-shopping-bag')
                        ->schema([
                            Forms\Components\TextInput::make('firstname')
                                ->placeholder('Entrer votre prénom')
                                ->label('Prénom')
                                ->required(),
                            Forms\Components\TextInput::make('lastname')
                                ->placeholder('Entrer votre nom')
                                ->label('Nom')
                                ->required(),
                            Forms\Components\TextInput::make('email')
                                ->placeholder('Entrer votre email')
                                ->label('Adresse e-mail')
                                ->email()
                                ->required(),
                        ]),
                    Wizard\Step::make('Informations de Localisation')
                        ->completedIcon('heroicon-m-hand-thumb-up')
                        ->schema([
                            Forms\Components\Select::make('country_id')
                                ->label('Pays')
                                ->required()
                                ->placeholder('Sélectionnez le pays')
                                ->options(function () {
                                    return Cache::remember('countries_formatted', 3600, function () {
                                        $countries = World::countries()->data;

                                        return collect($countries)
                                            ->map(function ($country) {
                                                // Conversion en tableau associatif
                                                $countryArray = (array) $country;

                                                return [
                                                    'id' => $countryArray['id'] ?? null,
                                                    'name' => $countryArray['name'] ?? 'Inconnu',
                                                    'phone_code' => $countryArray['phone_code'] ?? '',
                                                ];
                                            })
                                            ->pluck('name', 'id') // Garde seulement id => name pour les options
                                            ->toArray();
                                    });
                                })
                                ->searchable()
                                ->preload()
                                ->live()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    $set('city_id', null);
                                    $set('phone', '');
                                }),

                            // Sélection de la ville
                            Forms\Components\Select::make('city_id')
                                ->label('Ville')
                                ->required()
                                ->placeholder('Sélectionnez la ville')
                                ->options(function (callable $get) {
                                    $countryId = $get('country_id');
                                    if (!$countryId) return [];

                                    return Cache::remember("cities_formatted_{$countryId}", 86400, function () use ($countryId) {
                                        $cities = World::cities([
                                            'filters' => ['country_id' => $countryId],
                                            'fields' => 'id,name'
                                        ])->data;

                                        return collect($cities)
                                            ->map(function ($city) {
                                                $cityArray = (array) $city;
                                                return [
                                                    'id' => $cityArray['id'] ?? null,
                                                    'name' => $cityArray['name'] ?? 'Inconnu',
                                                ];
                                            })
                                            ->pluck('name', 'id')
                                            ->toArray();
                                    });
                                })
                                ->searchable()
                                ->live()
                                ->loadingMessage('Chargement des villes...'),

                            // Champ téléphone avec préfixe corrigé
                            Forms\Components\TextInput::make('phone')
                                ->tel()
                                ->mask('(999) 99 999 99 99')
                                ->placeholder('55 555 55 55')
                                ->required()
                                ->prefix(function (callable $get) {
                                    $countryId = $get('country_id');
                                    $countries = Cache::remember('countries_full_data', 3600, function () {
                                        return World::countries()->data;
                                    });

                                    $country = collect($countries)
                                        ->firstWhere('id', $countryId);

                                    return $country ? ($country->phone_code ?? $country['phone_code'] ?? '') : '';
                                })
                                ->prefixIcon('heroicon-o-phone'),
                        ]),

                    Wizard\Step::make('Decription de message')
                        ->completedIcon('heroicon-m-hand-thumb-up')
                        ->schema([
                            // Sélection du sujet
                            Forms\Components\Select::make('subject_id')
                                ->label('Sujet')
                                ->placeholder('Sélectionnez un sujet')
                                ->required()
                                ->preload()
                                ->options(Subject::pluck('description', 'id'))
                                ->searchable(),

                            Forms\Components\Textarea::make('message')
                                ->required()
                                ->rows(6)
                                ->placeholder('Décrivez votre besoin ou question ici...')
                                ->columnSpanFull(),
                        ])
                    // ->submitAction(new HtmlString(Blade::render(<<<BLADE
                    //     <flux:button 
                    //         class="cursor-pointer bg-blue-800 dark:bg-blue-800 text-white dark:text-white"
                    //         variant="primary" 
                    //         type="submit">
                    //         {{ __('Soumettre') }}
                    //     </flux:button>
                    // BLADE)))
                ])->persistStepInQueryString(),
            ])
            ->statePath('data')
            ->model(Contact::class)
            ->extraAttributes(['class' => 'flex justify-center w-full max-w-9xl']);
    }

    public function create()
    {
        $data = $this->form->getState();

        // Formatage du numéro de téléphone
        $countries = Cache::get('countries_full_data', []);
        $country = collect($countries)->firstWhere('id', $data['country_id']);

        if ($country) {
            $phoneCode = is_object($country)
                ? $country->phone_code
                : ($country['phone_code'] ?? '');

            $data['phone'] = $phoneCode . $data['phone'];
        }

        $contact = Contact::create($data);

        // event(new ContactRequestEvent($this, $contact));

        session()->flash('success', 'Votre message a été envoyé avec succès. Merci de nous avoir contactés !');

        $this->form->fill();

        return $this->redirect('/service', navigate: true);
    }
    public function save()
    {
        $contactData = $this->contactForm->getState();

        // Formatage du numéro de téléphone
        $countries = Cache::get('countries_full_data', []);
        $country = collect($countries)->firstWhere('id', $contactData['country_id']);

        if ($country) {
            $phoneCode = is_object($country)
                ? $country->phone_code
                : ($country['phone_code'] ?? '');

            $contactData['phone'] = $phoneCode . $contactData['phone'];
        }

        $contact = Contact::create($contactData);

        // event(new ContactRequestEvent($this, $contact));

        session()->flash('success', 'Votre message a été envoyé avec succès.');

        $this->form->fill();

        return $this->redirect('/service', navigate: true);
    }

    protected function getForms(): array
    {
        return [
            'contactForm',
            'form'
        ];
    }
    public function render()
    {
        return view('livewire.contact.create-contact');
    }
}
