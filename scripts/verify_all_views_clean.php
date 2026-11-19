<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$compiler = app('blade.compiler');
$viewsDir = resource_path('views');

$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsDir));
$bladeFiles = [];
foreach ($files as $file) {
    if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
        $bladeFiles[] = $file->getPathname();
    }
}

echo "Auditing " . count($bladeFiles) . " Blade templates for syntax errors...\n";

$errors = [];
$passed = 0;

foreach ($bladeFiles as $filePath) {
    $relPath = str_replace('\\', '/', substr($filePath, strlen($viewsDir) + 1));
    $rawContent = file_get_contents($filePath);
    
    try {
        $compiled = $compiler->compileString($rawContent);
        $tempFile = tempnam(sys_get_temp_dir(), 'blade_test_');
        file_put_contents($tempFile, $compiled);
        
        $output = [];
        $exitCode = 0;
        exec("php -l \"$tempFile\" 2>&1", $output, $exitCode);
        unlink($tempFile);
        
        if ($exitCode !== 0) {
            $errors[] = [
                'file' => $relPath,
                'error' => implode("\n", $output)
            ];
        } else {
            $passed++;
        }
    } catch (\Throwable $e) {
        $errors[] = [
            'file' => $relPath,
            'error' => $e->getMessage()
        ];
    }
}

echo "Results:\n";
echo "- Total Views: " . count($bladeFiles) . "\n";
echo "- Passed Cleanly: $passed\n";
echo "- Errors: " . count($errors) . "\n";

if (!empty($errors)) {
    echo "Failures:\n";
    print_r($errors);
    exit(1);
} else {
    echo "SUCCESS: 100% of Blade views compile and pass PHP syntax validation!\n";
    exit(0);
}

