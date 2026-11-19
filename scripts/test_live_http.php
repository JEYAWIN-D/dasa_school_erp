<?php

$baseUrl = 'http://127.0.0.1:8000';
$cookieFile = sys_get_temp_dir() . '/eps_test_cookie.txt';
if (file_exists($cookieFile)) unlink($cookieFile);

echo "=== REAL BROWSER HTTP INTEGRATION TEST ===\n\n";

function request($url, $method = 'GET', $data = [], $cookieFile = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    if ($cookieFile) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    }
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    }
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);
    return ['code' => $httpCode, 'url' => $finalUrl, 'body' => $response];
}

// 1. Fetch CSRF token from login page
$loginPage = request("$baseUrl/login", 'GET', [], $cookieFile);
preg_match('/name="_token"\s+value="([^"]+)"/', $loginPage['body'], $tokenMatch);
$csrfToken = $tokenMatch[1] ?? '';

// 2. Perform Login as Super Admin
$loginRes = request("$baseUrl/login", 'POST', [
    '_token' => $csrfToken,
    'email' => 'admin@schoolerp.in',
    'password' => 'Admin@1234',
    'admin_pin' => '1122',
], $cookieFile);

echo "Admin Login: HTTP {$loginRes['code']} -> {$loginRes['url']}\n";

$tests = [
    '/hr/employees/create' => ['name' => 'Add Staff Form', 'expect_text' => 'Add New Staff Member'],
    '/hr/employees/export' => ['name' => 'Export Staff Directory CSV', 'expect_text' => 'Employee Code'],
    '/hr/employees/template' => ['name' => 'Download Staff Template CSV', 'expect_text' => 'experience_type'],
    '/academics/teacher-allocation' => ['name' => 'Teacher & Class Teacher Allocation', 'expect_text' => 'Allocate Subject'],
    '/hr' => ['name' => 'HR & Payroll Dashboard', 'expect_text' => 'Staff Timetable'],
    '/staff-timetable' => ['name' => 'Staff Timetable Hub', 'expect_text' => 'Staff Timetable'],
    '/classes' => ['name' => 'Classes & Timetable Grid', 'expect_text' => 'Classes'],
    '/dashboard' => ['name' => 'Admin Dashboard', 'expect_text' => 'Dashboard'],
];

$allPassed = true;
foreach ($tests as $endpoint => $config) {
    $res = request("$baseUrl$endpoint", 'GET', [], $cookieFile);
    $hasText = str_contains($res['body'], $config['expect_text']);
    $statusOk = ($res['code'] === 200);
    $passed = $statusOk && $hasText;
    if (!$passed) $allPassed = false;
    $icon = $passed ? "✅ PASS" : "❌ FAIL";
    echo "[$icon] {$config['name']} ($endpoint) -> HTTP {$res['code']}" . ($hasText ? " (Content Verified)" : " (Missing: {$config['expect_text']})") . "\n";
}

// Also check Add Staff form does NOT contain Section B.2 "ACADEMIC ALLOCATION"
$addStaffRes = request("$baseUrl/hr/employees/create", 'GET', [], $cookieFile);
$hasAcademicAlloc = str_contains($addStaffRes['body'], 'ACADEMIC ALLOCATION');
echo ($hasAcademicAlloc ? "❌ FAIL: Academic Allocation still present" : "✅ PASS: Academic Allocation removed from Add Staff") . "\n";

// Check that Fresher toggle exists on Add Staff form
$hasFresherToggle = str_contains($addStaffRes['body'], 'Fresher');
echo ($hasFresherToggle ? "✅ PASS: Fresher status option present on Add Staff" : "❌ FAIL: Fresher status missing") . "\n";

// Check that Export / Download Application Form button is present on Add Staff form
$hasExportBtn = str_contains($addStaffRes['body'], 'Export / Download Application Form');
echo ($hasExportBtn ? "✅ PASS: Export / Download Application Form button present on Add Staff form" : "❌ FAIL: Export button missing") . "\n";

echo "\nResult: " . ($allPassed ? "ALL 8 HTTP TESTS PASSED PERFECTLY!" : "SOME TESTS FAILED") . "\n";
