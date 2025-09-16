{{-- 

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component {
    public string $name = '';
    public string $email = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id)
            ],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your name and email address')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" />

            <div>
                <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />

                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
                    <div>
                        <flux:text class="mt-4">
                            {{ __('Your email address is unverified.') }}

                            <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email.') }}
                            </flux:link>
                        </flux:text>

                        @if (session('status') === 'verification-link-sent')
                            <flux:text class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </flux:text>
                        @endif
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('Save') }}</flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>

        <livewire:settings.delete-user-form />
    </x-settings.layout>
</section> --}}
<?php
// use App\Models\User;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Session;
// use Illuminate\Validation\Rule;
// use Livewire\Volt\Component;
// use Livewire\WithFileUploads;

// new class extends Component {
//     use WithFileUploads;

//     public string $name = '';
//     public string $email = '';
//     public $photo = null; // fichier uploadé
//     public bool $removePhoto = false; // option suppression avatar

//     /**
//      * Mount the component.
//      */
//     public function mount(): void
//     {
//         $this->name = Auth::user()->name;
//         $this->email = Auth::user()->email;
//     }

//     /**
//      * Update the profile information for the currently authenticated user.
//      */
//     public function updateProfileInformation(): void
//     {
//         $user = Auth::user();

//         $validated = $this->validate([
//             'name' => ['required', 'string', 'max:255'],
//             'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
//             'photo' => ['nullable', 'image', 'max:2048'], // max 2Mo
//             'removePhoto' => ['boolean'],
//         ]);

//         $user->fill([
//             'name' => $validated['name'],
//             'email' => $validated['email'],
//         ]);

//         if ($user->isDirty('email')) {
//             $user->email_verified_at = null;
//         }

//         // 📸 Gestion de la photo
//         if ($validated['removePhoto']) {
//             $user->clearMediaCollection('avatars');
//         } elseif ($this->photo) {
//             $user->clearMediaCollection('avatars');
//             $user->addMedia($this->photo)->toMediaCollection('avatars');
//         }

//         $user->save();

//         $this->dispatch('profile-updated', name: $user->name);
//         $this->reset('photo', 'removePhoto');
//     }

//     /**
//      * Send an email verification notification to the current user.
//      */
//     public function resendVerificationNotification(): void
//     {
//         $user = Auth::user();

//         if ($user->hasVerifiedEmail()) {
//             $this->redirectIntended(default: route('dashboard', absolute: false));
//             return;
//         }

//         $user->sendEmailVerificationNotification();
//         Session::flash('status', 'verification-link-sent');
//     }
// };

// <section class="w-full">
//     @include('partials.settings-heading')

//     <x-settings.layout :heading="__('Profile')" :subheading="__('Update your personal information')">
//         <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">

//             {{-- Photo de profil --}}
//             <div class="space-y-2">
//                 <flux:label>{{ __('Profile Photo') }}</flux:label>

//                 <div class="flex items-center gap-4">
//                     {{-- Preview actuelle --}}
//                     <img src="{{ auth()->user()->getFirstMediaUrl('avatars', 'thumb') ?: asset('images/default-avatar.png') }}"
//                         alt="avatar" class="w-16 h-16 rounded-full object-cover">

//                     {{-- Upload --}}
//                     <input type="file" wire:model="photo" accept="image/*"
//                         class="block w-full text-sm text-gray-600" />

//                     {{-- Supprimer photo --}}
//                     @if (auth()->user()->getFirstMediaUrl('avatars'))
//                         <label class="flex items-center gap-2 text-red-600 text-sm cursor-pointer">
//                             <input type="checkbox" wire:model="removePhoto" class="rounded border-gray-300">
//                             {{ __('Remove photo') }}
//                         </label>
//                     @endif
//                 </div>
//                 @error('photo')
//                     <span class="text-red-600 text-sm">{{ $message }}</span>
//                 @enderror
//             </div>

//             {{-- Nom --}}
//             <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus
//                 autocomplete="name" />

//             {{-- Email --}}
//             <div>
//                 <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />

//                 @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
//                     <div class="mt-2">
//                         <flux:text>
//                             {{ __('Your email address is unverified.') }}
//                             <flux:link class="text-sm cursor-pointer"
//                                 wire:click.prevent="resendVerificationNotification">
//                                 {{ __('Click here to re-send the verification email.') }}
//                             </flux:link>
//                         </flux:text>

//                         @if (session('status') === 'verification-link-sent')
//                             <flux:text class="mt-2 font-medium text-green-600 dark:text-green-400">
//                                 {{ __('A new verification link has been sent to your email address.') }}
//                             </flux:text>
//                         @endif
//                     </div>
//                 @endif
//             </div>

//             {{-- Boutons --}}
//             <div class="flex items-center gap-4">
//                 <flux:button variant="primary" type="submit" class="w-full">{{ __('Save') }}</flux:button>
//                 <x-action-message class="me-3" on="profile-updated">
//                     {{ __('Saved.') }}
//                 </x-action-message>
//             </div>
//         </form>

//         <livewire:settings.delete-user-form />
//     </x-settings.layout>
// </section>

use App\Models\User;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\StreamedResponse;

new class extends Component {
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public $phone = '';
    public $address = '';
    public $date_of_birth = '';
    public $photo;
    public $avatar_url;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->address = $user->address ?? '';
        $this->date_of_birth = $user->date_of_birth?->format('Y-m-d') ?? '';
        $this->avatar_url = $user->getFilamentAvatarUrl();
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    // public function updateProfileInformation(): void
    // {
    //     $user = Auth::user();

    //     $validated = $this->validate([
    //         'name' => ['required', 'string', 'max:255'],
    //         'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
    //         'phone' => ['nullable', 'string', 'max:20'],
    //         'address' => ['nullable', 'string', 'max:500'],
    //         'date_of_birth' => ['nullable', 'date', 'before:today'],
    //         'photo' => ['nullable', 'image', 'max:2048', 'mimes:jpg,jpeg,png,webp'],
    //     ]);

    //     $user->fill(collect($validated)->except('photo')->toArray());

    //     if ($user->isDirty('email')) {
    //         $user->email_verified_at = null;
    //     }

    //     // Handle photo upload
    //     if ($this->photo) {
    //         $this->updateProfilePhoto($user);
    //     }

    //     $user->save();

    //     $this->dispatch('profile-updated', name: $user->name);
    //     $this->reset('photo');
    //     $this->avatar_url = $user->fresh()->getFilamentAvatarUrl();
    // }
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'photo' => ['nullable', 'image', 'max:2048', 'mimes:jpg,jpeg,png,webp'],
        ]);

        // 🔹 Si la date est vide, on force null
        if (empty($validated['date_of_birth'])) {
            $validated['date_of_birth'] = null;
        }

        $user->fill(collect($validated)->except('photo')->toArray());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // 🔹 Gestion de la photo
        if ($this->photo) {
            $this->updateProfilePhoto($user);
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
        $this->reset('photo');
        $this->avatar_url = $user->fresh()->getFilamentAvatarUrl();
    }

    /**
     * Update the user's profile photo.
     */
    protected function updateProfilePhoto(User $user): void
    {
        // Delete old photo if exists
        if ($user->getFirstMedia('avatars')) {
            $user->getFirstMedia('avatars')->delete();
        }

        // Add new photo
        $user
            ->addMedia($this->photo->getRealPath())
            ->usingFileName($this->generateFileName())
            ->toMediaCollection('avatars');
    }

    /**
     * Generate unique filename for the photo.
     */
    protected function generateFileName(): string
    {
        return 'avatar_' . Auth::id() . '_' . time() . '.' . $this->photo->getClientOriginalExtension();
    }

    /**
     * Delete the user's profile photo.
     */
    public function deleteProfilePhoto(): void
    {
        $user = Auth::user();

        if ($user->getFirstMedia('avatars')) {
            $user->getFirstMedia('avatars')->delete();
            $this->avatar_url = null;
            $this->dispatch('profile-photo-deleted');
        }
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));
            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Download user data.
     */
    public function downloadData(): StreamedResponse
    {
        $user = Auth::user();
        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $user->address,
            'date_of_birth' => $user->date_of_birth?->format('Y/m/d'),
            'email_verified_at' => $user->email_verified_at?->format('Y/m/d H:i:s'),
            'created_at' => $user->created_at->format('Y/m/d H:i:s'),
            'updated_at' => $user->updated_at->format('Y/m/d H:i:s'),
            'roles' => $user->roles->pluck('name')->implode(', '),
        ];

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return response()->streamDownload(function () use ($json) {
            echo $json;
        }, 'mes-donnees-personnelles.json');
    }
}; ?>
<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Profile Information')" :subheading="__('Update your account profile information and photo')">
        <!-- Photo Section -->
        <div class="mb-8">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Profile Photo') }}</h3>

            <div class="flex items-center gap-6">
                @if ($avatar_url)
                    <img src="{{ $avatar_url }}" alt="{{ auth()->user()->name }}"
                        class="w-20 h-20 rounded-full object-cover border-2 border-gray-200 dark:border-gray-700">
                @else
                    <div
                        class="w-20 h-20 rounded-full bg-primary-500 flex items-center justify-center text-white text-2xl font-bold">
                        {{ auth()->user()->initials() }}
                    </div>
                @endif

                <div class="flex-1 space-y-3">
                    <div>
                        <input type="file" wire:model="photo" accept="image/*" class="hidden" id="photo-upload">

                        <label for="photo-upload"
                            class="cursor-pointer inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </label>

                        @if ($avatar_url)
                            <button type="button" wire:click="deleteProfilePhoto"
                                wire:confirm="{{ __('Are you sure you want to delete your profile photo?') }}"
                                class="ml-3 inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                            </button>
                        @endif
                    </div>

                    @if ($photo)
                        <div class="flex items-center text-sm text-green-600 dark:text-green-400">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('New photo selected') }}
                        </div>
                    @endif

                    @error('photo')
                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Profile Information Form -->
        <form wire:submit="updateProfileInformation" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:input wire:model="name" :label="__('Full Name')" type="text" required autofocus
                    autocomplete="name" />

                <flux:input wire:model="email" :label="__('Email Address')" type="email" required
                    autocomplete="email" />

                <flux:input wire:model="phone" :label="__('Phone Number')" type="tel" autocomplete="tel" />

                <flux:input wire:model="date_of_birth" :label="__('Date of Birth')" type="date" />

            </div>
            <flux:textarea wire:model="address" :label="__('Address')" rows="3" class="md:col-span-2" />

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
                <div
                    class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-2a6 6 0 100-12 6 6 0 000 12z"
                                    clip-rule="evenodd" />
                                <path fill-rule="evenodd"
                                    d="M10 12a1 1 0 100-2 1 1 0 000 2zm0-4a1 1 0 100-2 1 1 0 000 2z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                {{ __('Email Verification Required') }}
                            </h3>
                            <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                <p>{{ __('Your email address is unverified.') }}</p>
                                <button type="button" wire:click="resendVerificationNotification"
                                    class="underline text-sm hover:text-yellow-600 dark:hover:text-yellow-400 mt-1">
                                    {{ __('Click here to re-send the verification email.') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                @if (session('status') === 'verification-link-sent')
                    <div
                        class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800 dark:text-green-200">
                                    {{ __('A new verification link has been sent to your email address.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-4">
                    <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                        <span wire:loading.remove>{{ __('Save Changes') }}</span>
                        <span wire:loading>{{ __('Saving...') }}</span>
                    </flux:button>

                    <x-action-message class="text-green-600 dark:text-green-400" on="profile-updated">
                        {{ __('Saved.') }}
                    </x-action-message>

                    <x-action-message class="text-green-600 dark:text-green-400" on="profile-photo-deleted">
                        {{ __('Photo removed.') }}
                    </x-action-message>
                </div>

                <button type="button" wire:click="downloadData"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-200 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    {{ __('Download My Data') }}
                </button>
            </div>
        </form>

        <!-- Additional Information -->
        {{-- <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Account Information') }}</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                <div>
                    <p class="text-gray-600 dark:text-gray-400">{{ __('Account Created') }}</p>
                    <p class="font-medium text-gray-900 dark:text-gray-100">
                        {{ auth()->user()->created_at->format('Y/m/d H:i') }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-600 dark:text-gray-400">{{ __('Last Updated') }}</p>
                    <p class="font-medium text-gray-900 dark:text-gray-100">
                        {{ auth()->user()->updated_at->format('Y/m/d H:i') }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-600 dark:text-gray-400">{{ __('Email Verified') }}</p>
                    <p class="font-medium text-gray-900 dark:text-gray-100">
                        {{ auth()->user()->email_verified_at ? auth()->user()->email_verified_at->format('Y/m/d H:i') : __('Not verified') }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-600 dark:text-gray-400">{{ __('User Role') }}</p>
                    <p class="font-medium text-gray-900 dark:text-gray-100">
                        {{ auth()->user()->role_name }}
                    </p>
                </div>
            </div>
        </div> --}}

        <livewire:settings.delete-user-form />
    </x-settings.layout>
</section>
