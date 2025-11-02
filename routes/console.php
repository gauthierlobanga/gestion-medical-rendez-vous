<?php

// use Illuminate\Foundation\Inspiring;
// use App\Jobs\EnvoyerRappelsRendezVous;
// use Illuminate\Support\Facades\Artisan;
// use Illuminate\Console\Scheduling\Schedule;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote');

// Schedule::job(new EnvoyerRappelsRendezVous)
//     ->hourly()
//     ->between('8:00', '20:00')
//     ->timezone('Europe/Paris')
//     ->onOneServer()
//     ->withoutOverlapping(30);

// Artisan::command('model:prune', [
//     '--model' => [\App\Models\NotificationRendezVous::class],
//     '--hours' => 720 // 30 jours
// ])->monthly();

// Artisan::command('backup:run')->dailyAt('02:00');

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\EnvoyerRappelsRendezVous;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Planification avec call() pour exécuter le job
Schedule::call(function () {
    dispatch(new EnvoyerRappelsRendezVous);
})->hourly()
    ->name('notifications')
    ->between('8:00', '20:00')
    ->timezone('Europe/Paris')
    ->withoutOverlapping(30);

// Nettoyage des anciennes notifications
Schedule::command('model:prune', [
    '--model' => 'App\Models\NotificationRendezVous',
    '--hours' => 720
])->monthly();

// Backup de la base de données
Schedule::command('backup:run')->dailyAt('02:00');
