<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Services\ClassBatchStatusService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(fn () => app(ClassBatchStatusService::class)->closeStartedBatches())
    ->name('close-started-class-batches')
    ->everyMinute()
    ->withoutOverlapping()
    ->timezone('Asia/Jakarta');
