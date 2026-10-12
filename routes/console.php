<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Generate sesi jadwal + QR token setiap hari dini hari.
Schedule::command('schedule:generate --days=30')->dailyAt('01:00');
