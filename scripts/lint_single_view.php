<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$file = $argv[1] ?? null;
if (!$file || !file_exists($file)) {
    echo "Usage: php lint_single_view.php <path-to-blade>\n";
    exit(1);
}

use Illuminate\Support\Facades\Blade;

try {
    $compiled = Blade::compileString(file_get_contents($file));
    $tmp = tempnam(sys_get_temp_dir(), 'lint_');
    file_put_contents($tmp, $compiled);
    $out = shell_exec("php -l \"$tmp\" 2>&1");
    unlink($tmp);
    echo trim($out) . "\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

