<?php

use Livewire\Volt\Volt;
use App\Livewire\Blog\Blog;
use App\Livewire\About\About;
use App\Livewire\Home\Accueil;
use App\Livewire\Support\Aide;
use App\Livewire\Blog\ShowBlog;
use App\Livewire\Contact\Contact;
use App\Livewire\Services\Service;
use Illuminate\Support\Facades\Route;
use App\Livewire\Services\ShowService;
use App\Http\Controllers\DashboardController;
use App\Livewire\Home\Home;

/**
 * Routes pour le public
 */
Route::name('medical.')->group(function () {
    Route::get('/', Home::class)->name('accueil');
    Route::get('/blog', Blog::class)->name('blog')->lazy();
    Route::get('/blog/{post:slug}', ShowBlog::class)->name('blog.show');
    Route::get('/service', Service::class)->name('service');
    Route::get('/service/{service:slug}', ShowService::class)->name('service.show');
    Route::get('/contact', Contact::class)->name('contact');
    Route::get('/aide', Aide::class)->name('aide');
    Route::get('/about', About::class)->name('about');
});

Route::get('dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    // Routes médecin
    Route::middleware(['auth', 'role:medecin|Medecin Chef Service|Super Admin'])->group(function () {
        Route::get('/medecin/dashboard', [DashboardController::class, 'index'])
            ->name('medecin.dashboard');

        Volt::route('/medecin/agenda', 'medecin.agenda')->name('medecin.agenda');
        Volt::route('/medecin/rendezvous', 'medecin.rendezvous')->name('medecin.rendezvous');
        Volt::route('/medecin/patients', 'medecin.patients')->name('medecin.patients');
        Volt::route('/medecin/disponibilites', 'medecin.disponibilites')->name('medecin.disponibilites');
        Volt::route('/medecin/service', 'medecin.service')->name('medecin.service');
    });
});

require __DIR__ . '/auth.php';
