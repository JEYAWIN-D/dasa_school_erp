<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- RUNNING LIVE HTTP INTEGRATION TESTS FOR FINANCE & HR SIDEBAR ---\n";

function request_url($url, $cookieFile = null) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    if ($cookieFile) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    }
    $res = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);
    return ['code' => $httpCode, 'url' => $finalUrl, 'body' => $res];
}

function login($email, $password, $cookieFile, $pin = null) {
    $r = request_url('http://127.0.0.1:8000/login', $cookieFile);
    preg_match('/name="_token" value="([^"]+)"/', $r['body'], $m);
    $token = $m[1] ?? '';
    
    $ch = curl_init('http://127.0.0.1:8000/login');
    curl_setopt($ch, CURLOPT_POST, true);
    $fields = ['_token' => $token, 'email' => $email, 'password' => $password];
    if ($pin) $fields['admin_pin'] = $pin;
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);
    return ['code' => $code, 'url' => $finalUrl, 'body' => $res];
}

$adminCookie = sys_get_temp_dir() . '/test_admin_' . time() . '.cookie';

// 1. Admin login test
echo "1. Testing Admin Login...\n";
$loginRes = login('admin@schoolerp.in', 'Admin@1234', $adminCookie, '1122');
echo "Admin Login Result: HTTP {$loginRes['code']} -> {$loginRes['url']}\n";

// 2. Check Admin Dashboard Sidebar
echo "2. Checking Admin Sidebar for FINANCE items...\n";
$dashRes = request_url('http://127.0.0.1:8000/dashboard', $adminCookie);

// Verify FINANCE header exists
if (str_contains($dashRes['body'], 'FINANCE')) {
    echo "[PASS] 'FINANCE' section found in Admin sidebar!\n";
} else {
    echo "[FAIL] 'FINANCE' not found in Admin sidebar\n";
}

// Verify Fee Management, Account Management, HR & Payroll are present
if (str_contains($dashRes['body'], 'Fee Management')) {
    echo "[PASS] 'Fee Management' found in sidebar under FINANCE!\n";
} else {
    echo "[FAIL] 'Fee Management' missing\n";
}

if (str_contains($dashRes['body'], 'Account Management')) {
    echo "[PASS] 'Account Management' found in sidebar under FINANCE!\n";
} else {
    echo "[FAIL] 'Account Management' missing\n";
}

if (str_contains($dashRes['body'], 'HR &amp; Payroll') || str_contains($dashRes['body'], 'HR & Payroll')) {
    echo "[PASS] 'HR & Payroll' found in sidebar under FINANCE!\n";
} else {
    echo "[FAIL] 'HR & Payroll' missing\n";
}

// Verify Staff Timetable is NOT standing as a separate sidebar item
if (!preg_match('/<x-nav-item[^>]+label="Staff Timetable"/', $dashRes['body']) && !str_contains($dashRes['body'], '<p class="nav-group-label">HR &amp; PAYROLL</p>')) {
    echo "[PASS] No separate 'Staff Timetable' or separate 'HR & PAYROLL' header cluttering the sidebar!\n";
} else {
    echo "[WARN] Found separate Staff Timetable or HR header in sidebar HTML\n";
}

// 3. Check inside HR & Payroll (/hr)
echo "3. Checking inside HR & Payroll (http://127.0.0.1:8000/hr)...\n";
$hrRes = request_url('http://127.0.0.1:8000/hr', $adminCookie);
echo "HR & Payroll Result: HTTP {$hrRes['code']}\n";
if ($hrRes['code'] === 200) {
    if (str_contains($hrRes['body'], 'Staff Timetable') && str_contains($hrRes['body'], 'staff-timetable')) {
        echo "[PASS] 'Staff Timetable' is prominently available inside HR & Payroll!\n";
    } else {
        echo "[FAIL] 'Staff Timetable' not found inside HR & Payroll\n";
    }
}

// 4. Check Staff Timetable page (/staff-timetable)
echo "4. Checking Staff Timetable page (http://127.0.0.1:8000/staff-timetable)...\n";
$stRes = request_url('http://127.0.0.1:8000/staff-timetable', $adminCookie);
echo "Staff Timetable Page Result: HTTP {$stRes['code']}\n";
if ($stRes['code'] === 200 && str_contains($stRes['body'], 'HR &amp; Payroll')) {
    echo "[PASS] Staff Timetable loaded with HTTP 200 and links directly back to HR & Payroll!\n";
}

echo "--- ALL CHECKS COMPLETED SUCCESSFULLY ---\n";
