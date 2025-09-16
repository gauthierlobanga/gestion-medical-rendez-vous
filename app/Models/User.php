<?php

namespace App\Models;

use Filament\Panel;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\Email\Contracts\HasEmailAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;

class User extends Authenticatable implements HasMedia, FilamentUser, HasAvatar, HasEmailAuthentication, HasAppAuthentication, HasAppAuthenticationRecovery, MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory,
        Notifiable,
        HasRoles,
        InteractsWithMedia;


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'date_of_birth',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'app_authentication_secret',
        'app_authentication_recovery_codes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'app_authentication_secret' => 'encrypted',
            'app_authentication_recovery_codes' => 'encrypted:array',
            'has_email_authentication' => 'boolean',
            'date_of_birth' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatars')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpg', 'image/jpeg', 'image/png', 'image/webp'])
            ->withResponsiveImages();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->sharpen(10)
            ->performOnCollections('avatars')
            ->format('webp')
            ->quality(80);
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->getFirstMediaUrl('avatars', 'thumb') ?: $this->avatar_url;
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function getAppAuthenticationSecret(): ?string
    {
        // This method should return the user's saved app authentication secret.

        return $this->app_authentication_secret;
    }

    public function saveAppAuthenticationSecret(?string $secret): void
    {
        // This method should save the user's app authentication secret.

        $this->app_authentication_secret = $secret;
        $this->save();
    }

    public function getAppAuthenticationHolderName(): string
    {
        // In a user's authentication app, each account can be represented by a "holder name".
        // If the user has multiple accounts in your app, it might be a good idea to use
        // their email address as then they are still uniquely identifiable.

        return $this->email;
    }

    /**
     * @return ?array<string>
     */
    public function getAppAuthenticationRecoveryCodes(): ?array
    {
        // This method should return the user's saved app authentication recovery codes.

        return $this->app_authentication_recovery_codes;
    }

    /**
     * @param  array<string> | null  $codes
     */
    public function saveAppAuthenticationRecoveryCodes(?array $codes): void
    {
        // This method should save the user's app authentication recovery codes.

        $this->app_authentication_recovery_codes = $codes;
        $this->save();
    }

    public function hasEmailAuthentication(): bool
    {
        // This method should return true if the user has enabled email authentication.

        return $this->has_email_authentication;
    }

    public function toggleEmailAuthentication(bool $condition): void
    {
        // This method should save whether or not the user has enabled email authentication.

        $this->has_email_authentication = $condition;
        $this->save();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return str_ends_with($this->email, '@gmail.com')
                && $this->hasVerifiedEmail()
                && $this->hasAnyRole(['Super Admin', 'medecin', 'personnel'])
                && $this->is_active;
        }

        return true;
    }

    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    public function isSuperAdmin()
    {
        return $this->hasRole('Super Admin');
    }

    public function isMedecin()
    {
        return $this->hasRole('medecin');
    }

    public function isPersonnel()
    {
        return $this->hasRole('personnel');
    }

    public function isPatient()
    {
        return $this->hasRole('patient');
    }

    public function getRoleNameAttribute()
    {
        return $this->roles->first()->name ?? 'patient';
    }

    protected static function booted()
    {
        static::created(function ($user) {
            if (!$user->hasAnyRole(['Super Admin', 'medecin', 'personnel', 'secretaire'])) {
                $user->assignRole('patient');
            }
        });
    }
}
