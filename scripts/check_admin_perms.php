<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$admin = \App\Models\User::where('email', 'admin@schoolerp.in')->first();
echo "Admin user: " . $admin->name . "\n";
echo "Admin can view employees? " . ($admin->can('view employees') ? 'YES' : 'NO') . "\n";
echo "Admin permissions: " . implode(', ', $admin->getAllPermissions()->pluck('name')->toArray()) . "\n";

