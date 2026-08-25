<?php

use App\Console\Commands\ResetItemsAmountAutoBudget;
use App\Console\Commands\ResetItemsAmountCustomBudget;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(ResetItemsAmountAutoBudget::class)->everyFifteenSeconds();
Schedule::command(ResetItemsAmountCustomBudget::class)->everyFifteenSeconds();
