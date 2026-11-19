<?php

namespace Database\Seeders;

use App\Services\StaffSyncService;
use Illuminate\Database\Seeder;

class SyncRealTeachersAndAllocationsSeeder extends Seeder
{
    public function run(): void
    {
        $result = StaffSyncService::run();
        foreach ($result['log'] as $line) {
            $this->command->info($line);
        }
    }
}

