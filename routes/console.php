<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedulers
Schedule::command('ops:recalc-sla')->dailyAt('01:00');
Schedule::command('ar:mark-overdue')->dailyAt('02:00');
