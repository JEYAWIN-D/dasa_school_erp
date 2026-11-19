<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

$teacherRole = Role::where('name', 'teacher')->first();
echo "Teacher permissions:\n";
if ($teacherRole) {
    echo implode(', ', $teacherRole->permissions->pluck('name')->toArray()) . "\n";
} else {
    echo "No teacher role found.\n";
}

$user = User::where('email', 'vinuyagavi@erodepublicschool.edu.in')->first();
if ($user) {
    echo "\nUser vinuyagavi roles: " . implode(', ', $user->getRoleNames()->toArray()) . "\n";
    echo "Can view employees? " . ($user->can('view employees') ? 'YES' : 'NO') . "\n";
    echo "Can view academics? " . ($user->can('view academics') ? 'YES' : 'NO') . "\n";
}

