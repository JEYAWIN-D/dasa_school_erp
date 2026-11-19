<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('school:sync-staff', function () {
    $this->info("Starting real staff & allocation synchronization...");
    $result = \App\Services\StaffSyncService::run();
    foreach ($result['log'] as $line) {
        $this->info($line);
    }
    $this->info("Successfully synchronized {$result['teaching_staff_count']} teachers with {$result['allocations_count']} allocations.");
})->purpose('Synchronize the 42 official teachers and exact subject allocations');

\Illuminate\Support\Facades\Schedule::command('hr:autofill-permission-in-time')
    ->everyTenMinutes()
    ->withoutOverlapping();

