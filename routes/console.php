<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule loan repayment overdue checks
Schedule::call(function () {
    \App\Services\LoanService::updateOverdueRepayments();
})->daily();
