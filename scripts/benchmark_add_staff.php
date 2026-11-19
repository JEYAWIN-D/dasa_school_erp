<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Employee;
use App\Models\User;
use App\Models\Department;
use App\Models\Designation;

$t0 = microtime(true);
function mark($label, &$tPrev) {
    $now = microtime(true);
    echo sprintf("[%6.2f s] %s (delta: %6.2f s)\n", $now - $GLOBALS['t0'], $label, $now - $tPrev);
    $tPrev = $now;
}

$tPrev = $t0;
mark("Bootstrap complete", $tPrev);

// Test a simple DB query to check latency
$cnt = DB::table('employees')->count();
mark("DB count employees: $cnt", $tPrev);

// Check Spatie Role query
$roles = \Spatie\Permission\Models\Role::all();
mark("Spatie roles loaded: " . count($roles), $tPrev);

