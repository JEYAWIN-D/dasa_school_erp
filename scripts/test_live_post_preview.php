<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$cookieFile = sys_get_temp_dir() . '/test_post_preview_' . time() . '.cookie';

function req($url, $cookie, $postData = null) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
    if ($postData !== null) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    }
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['code' => $code, 'body' => $res];
}

// Login as admin
$r = req('http://127.0.0.1:8000/login', $cookieFile);
preg_match('/name="_token" value="([^"]+)"/', $r['body'], $m);
$token = $m[1] ?? '';

$loginRes = req('http://127.0.0.1:8000/login', $cookieFile, [
    '_token' => $token,
    'email' => 'admin@schoolerp.in',
    'password' => 'Admin@1234',
    'admin_pin' => '1122',
]);

// Test POST /hr/employees/application-form/preview with empty fields (user clicking without filling name)
$createPage = req('http://127.0.0.1:8000/hr/employees/create', $cookieFile);
preg_match('/name="_token" value="([^"]+)"/', $createPage['body'], $m2);
$token2 = $m2[1] ?? '';

$previewPost = req('http://127.0.0.1:8000/hr/employees/application-form/preview', $cookieFile, [
    '_token' => $token2,
    'gender' => 'female',
    'designation' => 'TGT Science',
]);

echo "POST Preview HTTP Code: {$previewPost['code']}\n";
if ($previewPost['code'] === 200 && str_contains($previewPost['body'], 'Affiliated to Central Board of Secondary Education')) {
    echo "[PASS] Live POST /hr/employees/application-form/preview succeeded with HTTP 200!\n";
} else {
    echo "[FAIL] Unexpected response: {$previewPost['code']}\n";
}

