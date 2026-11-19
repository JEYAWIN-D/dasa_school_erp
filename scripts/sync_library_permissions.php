<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

$permissions = [
    'view library',
    'manage library',
    'issue library books',
    'return library books',
    'waive library fines',
    'manage library procurement',
    'manage library periodicals',
    'manage library assets',
    'manage library wastage',
    'manage library stock verification',
    'export library reports',
    'manage library settings',
    'suspend library members',
    'audit library logs',
];

foreach ($permissions as $permName) {
    Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
}

$adminRoles = Role::whereIn('name', ['Super Admin', 'Admin', 'librarian'])->get();
foreach ($adminRoles as $role) {
    $role->givePermissionTo($permissions);
}

echo "Library permissions synchronized successfully!\n";

