<?php

use Illuminate\Foundation\Inspiring;
use App\Jobs\EnvoyerRappelsRendezVous;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new EnvoyerRappelsRendezVous)
    ->hourly()
    ->between('8:00', '20:00')
    ->timezone('Europe/Paris')
    ->onOneServer()
    ->withoutOverlapping(30);

Artisan::command('model:prune', [
    '--model' => [\App\Models\NotificationRendezVous::class],
    '--hours' => 720 // 30 jours
])->monthly();

Artisan::command('backup:run')->dailyAt('02:00');
