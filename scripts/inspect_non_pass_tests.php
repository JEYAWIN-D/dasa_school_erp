<?php
declare(strict_types=1);

$file = dirname(__DIR__) . '/QA_AUDIT/VERSION_1/TEST_CASE_REGISTER.csv';
$fp = fopen($file, 'r');
$header = fgetcsv($fp);

$tests = [];
while (($row = fgetcsv($fp)) !== false) {
    $item = array_combine($header, $row);
    if ($item['status'] !== 'PASS') {
        $tests[] = [
            'id' => $item['test_case_id'],
            'category' => $item['category'],
            'status' => $item['status'],
            'scenario' => $item['scenario'],
            'actual' => substr($item['actual_result'], 0, 80)
        ];
    }
}
fclose($fp);

echo "Found " . count($tests) . " non-PASS test records in TEST_CASE_REGISTER.csv:\n";
foreach ($tests as $t) {
    echo "{$t['id']} | {$t['status']} | {$t['category']} | {$t['scenario']} | {$t['actual']}\n";
}

