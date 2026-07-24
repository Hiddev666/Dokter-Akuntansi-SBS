<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:monitor-scanner')->everyFiveSeconds();
Schedule::call(function () {
    logger('Inline scheduled task executed.');
})->everyFiveSeconds();
