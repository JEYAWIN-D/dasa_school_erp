<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$teacher = \App\Models\User::where('email', 'vinuyagavi@erodepublicschool.edu.in')->first();
if ($teacher) {
    echo "Teacher user: {$teacher->name}\n";
    echo "Roles: " . implode(', ', $teacher->getRoleNames()->toArray()) . "\n";
    echo "Can view employees? " . ($teacher->can('view employees') ? 'YES' : 'NO') . "\n";
    echo "Can view fees? " . ($teacher->can('view fees') ? 'YES' : 'NO') . "\n";
    echo "Can view academics? " . ($teacher->can('view academics') ? 'YES' : 'NO') . "\n";
}

