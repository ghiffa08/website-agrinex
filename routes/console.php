<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule AI Field Capacity Analysis
Schedule::command('ai:analyze-field-capacity')
    ->everyTwoHours()
    ->name('ai-field-capacity-analysis')
    ->withoutOverlapping()
    ->onOneServer();
