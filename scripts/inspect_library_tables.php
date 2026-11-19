<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$tables = DB::select("
    SELECT table_name 
    FROM information_schema.tables 
    WHERE table_schema = 'public' 
      AND (table_name LIKE '%book%' OR table_name LIKE '%library%' OR table_name LIKE '%periodical%')
    ORDER BY table_name
");

echo "Matching Tables in PostgreSQL:\n";
foreach ($tables as $t) {
    echo "- {$t->table_name}\n";
    $cols = DB::select("
        SELECT column_name, data_type, is_nullable, column_default
        FROM information_schema.columns
        WHERE table_schema = 'public' AND table_name = '{$t->table_name}'
        ORDER BY ordinal_position
    ");
    foreach ($cols as $c) {
        echo "    * {$c->column_name} ({$c->data_type}, nullable: {$c->is_nullable})\n";
    }
    $rowCount = DB::table($t->table_name)->count();
    echo "    Total rows: $rowCount\n\n";
}

