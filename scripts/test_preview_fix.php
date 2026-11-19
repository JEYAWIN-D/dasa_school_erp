<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- TESTING PREVIEW APPLICATION FORM WITH EMPTY & FULL DATA ---\n";

$controller = new \App\Http\Controllers\Admin\HrController();

// Case 1: Empty request (what triggered the user's error if fields weren't filled yet)
echo "Case 1: Empty request...\n";
$req1 = new \Illuminate\Http\Request([]);
$v1 = $controller->previewApplicationForm($req1);
$html1 = $v1->render();
echo "[PASS] Empty request rendered without error! Output length: " . strlen($html1) . " bytes\n";

// Case 2: Request with just 'name'
echo "Case 2: Request with 'name'...\n";
$req2 = new \Illuminate\Http\Request(['name' => 'Meenakshi Sundaram']);
$v2 = $controller->previewApplicationForm($req2);
$html2 = $v2->render();
if (stripos($html2, 'Meenakshi Sundaram') !== false && stripos($html2, 'Affiliated to Central Board of Secondary Education') !== false) {
    echo "[PASS] Request with name rendered properly with CBSE context!\n";
} else {
    echo "[FAIL] String check failed on Case 2\n";
}

// Case 3: Request with 'first_name' and 'last_name'
echo "Case 3: Request with 'first_name' & 'last_name'...\n";
$req3 = new \Illuminate\Http\Request(['first_name' => 'Kavitha', 'last_name' => 'R', 'experience_type' => 'fresher']);
$v3 = $controller->previewApplicationForm($req3);
$html3 = $v3->render();
if (stripos($html3, 'Kavitha R') !== false && stripos($html3, 'Registered as a Fresher Candidate') !== false) {
    echo "[PASS] Request with first_name rendered properly with Fresher candidate badge!\n";
} else {
    echo "[FAIL] String check failed on Case 3\n";
}

// Case 4: Existing employee
echo "Case 4: Existing employee applicationForm...\n";
$emp = \App\Models\Employee::first();
if ($emp) {
    $v4 = $controller->applicationForm($emp->id);
    $html4 = $v4->render();
    echo "[PASS] Existing employee #{$emp->id} rendered successfully! Output length: " . strlen($html4) . " bytes\n";
}

echo "--- ALL TEST CASES PASSED ---\n";
