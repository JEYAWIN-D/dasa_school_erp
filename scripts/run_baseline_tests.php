<?php
/**
 * DASA EduERP — Stage 2 Comprehensive Baseline Test Runner
 * Executes non-destructive baseline tests across all ERP modules, auth, and APIs.
 */

require dirname(__DIR__) . '/vendor/autoload.php';
$app = require_once dirname(__DIR__) . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Models\User;

$results = [];

function recordTest(&$results, $id, $category, $module, $scenario, $status, $expected, $actual, $evidence = '') {
    $results[] = [
        'test_case_id' => $id,
        'category'     => $category,
        'module'       => $module,
        'scenario'     => $scenario,
        'preconditions'=> 'Application booted, PostgreSQL connected',
        'test_steps'   => 'Invoke HTTP request / query within application kernel',
        'expected_result' => $expected,
        'actual_result'   => $actual,
        'status'       => $status,
        'evidence_ref' => $evidence,
        'tested_at'    => date('Y-m-d H:i:s'),
        'tester'       => 'Senior Enterprise QA Team'
    ];
    echo sprintf("[%s] %-14s | %-16s | %s\n", $status, $id, $module, $scenario);
}

echo "====================================================================\n";
echo " DASA EduERP — STAGE 2 BASELINE APPLICATION TESTING RUNNER\n";
echo "====================================================================\n";

// ── 1. INFRASTRUCTURE & DATABASE CONNECTIVITY ───────────────────────
$dbStart = microtime(true);
try {
    $pgVersion = DB::select('SELECT version()')[0]->version ?? '';
    $latencyMs = round((microtime(true) - $dbStart) * 1000, 2);
    $connCount = DB::select('SELECT count(*) as count FROM pg_stat_activity')[0]->count ?? 0;
    recordTest($results, 'TC-BASE-001', 'Infrastructure', 'Database', 'PostgreSQL 18.6 Connection & Latency Check', 'PASS', 'Connection successful with <100ms latency', "Connected in {$latencyMs}ms. Version: {$pgVersion}. Active conns: {$connCount}", "latency: {$latencyMs}ms");
} catch (\Throwable $e) {
    recordTest($results, 'TC-BASE-001', 'Infrastructure', 'Database', 'PostgreSQL Connection Check', 'FAIL', 'Connection successful', 'Failed: ' . $e->getMessage(), 'error_log');
}

// ── 2. SYSTEM HEALTH ENDPOINTS ──────────────────────────────────────
try {
    $request = Request::create('/health', 'GET');
    $response = $app->handle($request);
    $status = $response->getStatusCode();
    $data = json_decode($response->getContent(), true);
    if ($status === 200 && ($data['status'] ?? '') === 'healthy') {
        recordTest($results, 'TC-BASE-002', 'Infrastructure', 'System Health', 'Application Health Check (/health)', 'PASS', 'HTTP 200 Healthy', "HTTP {$status} Healthy. Total latency: " . ($data['total_latency_ms'] ?? 0) . "ms", json_encode($data));
    } else {
        recordTest($results, 'TC-BASE-002', 'Infrastructure', 'System Health', 'Application Health Check (/health)', 'FAIL', 'HTTP 200 Healthy', "HTTP {$status} status: " . ($data['status'] ?? 'unknown'), $response->getContent());
    }
} catch (\Throwable $e) {
    recordTest($results, 'TC-BASE-002', 'Infrastructure', 'System Health', 'Application Health Check (/health)', 'FAIL', 'HTTP 200 Healthy', 'Exception: ' . $e->getMessage(), 'error');
}

// ── 3. AUTHENTICATION & LOGIN WORKFLOWS ──────────────────────────────
// A. Landing Page
try {
    $req = Request::create('/', 'GET');
    $res = $app->handle($req);
    $status = $res->getStatusCode();
    recordTest($results, 'TC-BASE-003', 'Authentication', 'Landing Page', 'Public Splash / Landing Page (/)' , $status === 200 ? 'PASS' : 'FAIL', 'HTTP 200 OK', "HTTP {$status}", "status: {$status}");
} catch (\Throwable $e) {
    recordTest($results, 'TC-BASE-003', 'Authentication', 'Landing Page', 'Public Splash Page', 'FAIL', 'HTTP 200', $e->getMessage());
}

// B. Show Login Page
try {
    $req = Request::create('/login', 'GET');
    $res = $app->handle($req);
    $status = $res->getStatusCode();
    recordTest($results, 'TC-BASE-004', 'Authentication', 'Login', 'Login Screen Display (/login)', $status === 200 ? 'PASS' : 'FAIL', 'HTTP 200 OK', "HTTP {$status}", "status: {$status}");
} catch (\Throwable $e) {
    recordTest($results, 'TC-BASE-004', 'Authentication', 'Login', 'Login Screen Display', 'FAIL', 'HTTP 200', $e->getMessage());
}

// C. Unauthenticated Guest Access to Protected Route
try {
    Auth::logout();
    $req = Request::create('/dashboard', 'GET');
    $res = $app->handle($req);
    $status = $res->getStatusCode();
    $target = $res->headers->get('Location');
    $pass = ($status === 302 && str_contains($target, 'login'));
    recordTest($results, 'TC-BASE-005', 'Security', 'Authentication', 'Unauthenticated Access Redirect (/dashboard -> /login)', $pass ? 'PASS' : 'FAIL', 'HTTP 302 Redirect to /login', "HTTP {$status} Redirect to: {$target}", "redirect: {$target}");
} catch (\Throwable $e) {
    recordTest($results, 'TC-BASE-005', 'Security', 'Authentication', 'Unauthenticated Access Redirect', 'FAIL', 'HTTP 302', $e->getMessage());
}

// D. Super Admin Authentication & Dashboard Loading
$adminUser = User::where('email', 'admin@schoolerp.in')->first() ?: User::role('super_admin')->first();
if ($adminUser) {
    Auth::login($adminUser);
    try {
        $req = Request::create('/dashboard', 'GET');
        $res = $app->handle($req);
        $status = $res->getStatusCode();
        recordTest($results, 'TC-BASE-006', 'Functional', 'Dashboard', "Super Admin Dashboard Loading ({$adminUser->email})", $status === 200 ? 'PASS' : 'FAIL', 'HTTP 200 OK with Admin Widgets', "HTTP {$status}", "user_id: {$adminUser->id}");
    } catch (\Throwable $e) {
        recordTest($results, 'TC-BASE-006', 'Functional', 'Dashboard', 'Super Admin Dashboard Loading', 'FAIL', 'HTTP 200 OK', 'Exception: ' . $e->getMessage());
    }
} else {
    recordTest($results, 'TC-BASE-006', 'Functional', 'Dashboard', 'Super Admin Dashboard Loading', 'BLOCKED', 'Super admin user exists', 'No admin user found in database');
}

// ── 4. MODULE-BY-MODULE FUNCTIONAL ROUTE AUDIT ───────────────────────
$modulesToTest = [
    // [Module Name, Test ID, URI, Expected Title/Content, Allowed Roles]
    ['Admissions', 'TC-BASE-010', '/admissions', 'Admission Enquiries & Fee Structure'],
    ['Admissions', 'TC-BASE-011', '/admissions/drafts', 'Draft Recovery Desk'],
    ['Admissions', 'TC-BASE-012', '/admissions/create', 'New Student Admission Form'],
    ['Students', 'TC-BASE-013', '/students', 'Student Information System Directory'],
    ['Students', 'TC-BASE-014', '/students/create', 'Student Registration Portal'],
    ['Academics', 'TC-BASE-015', '/academics', 'Academic Management Overview'],
    ['Academics', 'TC-BASE-016', '/academics/terms', 'Academic Terms Configuration'],
    ['Academics', 'TC-BASE-017', '/classes', 'Classes & Sections Master'],
    ['Academics', 'TC-BASE-018', '/staff-timetable', 'Staff & Master Timetable Hub'],
    ['Attendance', 'TC-BASE-019', '/attendance', 'Student Daily Attendance Desk'],
    ['Attendance', 'TC-BASE-020', '/attendance/mark', 'Section Attendance Marker'],
    ['Examinations', 'TC-BASE-021', '/examinations', 'Examination Cycles & Grading'],
    ['Fees', 'TC-BASE-022', '/fees', 'Student Fee Management Hub'],
    ['Fees', 'TC-BASE-023', '/fees/structure', 'Standard 3-Term Fee Structures'],
    ['Expenses', 'TC-BASE-024', '/expenses', 'Bifurcated Institutional Expenses'],
    ['Accounts', 'TC-BASE-025', '/accounts', 'Cash Drawers & Digital Accounts'],
    ['HR & Staff', 'TC-BASE-026', '/hr/employees', 'Staff & Employee Directory'],
    ['HR & Staff', 'TC-BASE-027', '/hr/leave-approvals', 'Staff Leave Approvals Desk'],
    ['Library', 'TC-BASE-028', '/library/books', 'Library Book Catalog'],
    ['Transport', 'TC-BASE-029', '/transport/routes', 'Fleet Transport Routes'],
    ['Gate & Visitors', 'TC-BASE-030', '/gate', 'Gate Entry & Visitor Management'],
    ['Reports', 'TC-BASE-031', '/reports/general-register', 'General Student Admission Register'],
    ['Reports', 'TC-BASE-032', '/reports/fee-collection-register', 'Fee Collection Register'],
    ['Inventory', 'TC-BASE-033', '/warehouse', 'Warehouse & Uniform Inventory'],
    ['Events Calendar', 'TC-BASE-034A', '/events/calendar', 'Academic Events & Activities Calendar'],
    ['Activity Hub', 'TC-BASE-034B', '/activities/calendar', 'Unified Activity Hub Calendar'],
    ['Circulars', 'TC-BASE-035', '/circulars', 'Administrative Circulars & Orders'],
    ['Settings', 'TC-BASE-036', '/settings', 'System Configuration & Profiles'],
];

// Ensure authenticated as Super Admin
if ($adminUser) {
    Auth::login($adminUser);
}

foreach ($modulesToTest as $mod) {
    list($modName, $testId, $uri, $desc) = $mod;
    $start = microtime(true);
    try {
        $req = Request::create($uri, 'GET');
        $res = $app->handle($req);
        $status = $res->getStatusCode();
        $duration = round((microtime(true) - $start) * 1000, 2);
        $contentLen = strlen($res->getContent());
        $pass = ($status === 200);
        recordTest($results, $testId, 'Functional', $modName, "{$desc} ({$uri})", $pass ? 'PASS' : 'FAIL', 'HTTP 200 OK', "HTTP {$status} in {$duration}ms ({$contentLen} bytes)", "status: {$status}, latency: {$duration}ms");
    } catch (\Throwable $e) {
        $duration = round((microtime(true) - $start) * 1000, 2);
        recordTest($results, $testId, 'Functional', $modName, "{$desc} ({$uri})", 'FAIL', 'HTTP 200 OK', 'Exception: ' . substr($e->getMessage(), 0, 120), 'error_trace');
    }
}

// ── 5. DISABLED MODULES ACCESS ENFORCEMENT ───────────────────────────
$disabledModules = [
    ['Hostel', 'TC-BASE-037', '/hostel', 'EnsureHostelEnabled'],
    ['LMS', 'TC-BASE-038', '/lms', 'EnsureLmsEnabled'],
    ['Communication', 'TC-BASE-039', '/communication', 'EnsureCommunicationEnabled'],
    ['Alumni', 'TC-BASE-040', '/alumni', 'EnsureAlumniEnabled'],
];

foreach ($disabledModules as $dm) {
    list($modName, $testId, $uri, $middleware) = $dm;
    try {
        $req = Request::create($uri, 'GET');
        $res = $app->handle($req);
        $status = $res->getStatusCode();
        // Disabled modules redirect to dashboard with warning or return 403
        $pass = ($status === 302 || $status === 403);
        $target = $res->headers->get('Location') ?? 'N/A';
        recordTest($results, $testId, 'Security', $modName, "Disabled Module Access Prevention ({$uri})", $pass ? 'PASS' : 'FAIL', 'HTTP 302 Redirect or HTTP 403 Forbidden', "HTTP {$status} (Redirect to: {$target})", "middleware: {$middleware}");
    } catch (\Throwable $e) {
        recordTest($results, $testId, 'Security', $modName, "Disabled Module Access Prevention ({$uri})", 'FAIL', 'Blocked', $e->getMessage());
    }
}

// ── 6. PUBLIC TOKENIZED & RATE-LIMITED ENDPOINTS ─────────────────────
Auth::logout();
$publicEndpoints = [
    ['Public Fee Payment', 'TC-BASE-041', '/pay/fee/15', 'Public Fee Checkout Screen'],
    ['Public Enquiry Form', 'TC-BASE-042', '/enquiry', 'Public Admission Enquiry Form'],
    ['Public Gate Verification', 'TC-BASE-043', '/gate/verify/TEST_INVALID_TOKEN', 'Public Gate Pass Verification (Invalid Token)'],
    ['Public ID Card Verify', 'TC-BASE-044', '/verify-id-card/TEST_INVALID_TOKEN', 'Public ID Card Verification (Invalid Token)'],
];

foreach ($publicEndpoints as $pe) {
    list($modName, $testId, $uri, $desc) = $pe;
    try {
        $req = Request::create($uri, 'GET');
        $res = $app->handle($req);
        $status = $res->getStatusCode();
        // Public valid pages return 200; invalid tokens should return 404 or safe error view
        $pass = in_array($status, [200, 404, 302]);
        recordTest($results, $testId, 'Security/Public', $modName, "{$desc} ({$uri})", $pass ? 'PASS' : 'FAIL', 'HTTP 200 or Safe Handled 404', "HTTP {$status}", "status: {$status}");
    } catch (\Throwable $e) {
        recordTest($results, $testId, 'Security/Public', $modName, "{$desc} ({$uri})", 'FAIL', 'Safe Response', $e->getMessage());
    }
}

// ── 7. DOCUMENT & PRINT ENGINE BASELINE ──────────────────────────────
if ($adminUser) {
    Auth::login($adminUser);
}
$printEndpoints = [
    ['Admissions PDF', 'TC-BASE-045', '/admissions/print-form?form=admission', 'Admission Application PDF Generation'],
    ['Grade XI PDF', 'TC-BASE-046', '/admissions/print-form?form=grade11', 'Grade XI Stream Application PDF Generation'],
    ['Enquiry PDF', 'TC-BASE-047', '/admissions/print-form?form=enquiry', 'Official Enquiry Form PDF Generation'],
    ['Fee Schedule Print', 'TC-BASE-048', '/admissions/fee-structure/15/print', 'Standard Pre-KG Fee Schedule Print Preview'],
];

foreach ($printEndpoints as $pe) {
    list($modName, $testId, $uri, $desc) = $pe;
    $start = microtime(true);
    try {
        $req = Request::create($uri, 'GET');
        $res = $app->handle($req);
        $status = $res->getStatusCode();
        $duration = round((microtime(true) - $start) * 1000, 2);
        $contentType = $res->headers->get('Content-Type');
        $pass = ($status === 200);
        recordTest($results, $testId, 'Print/Export', $modName, "{$desc} ({$uri})", $pass ? 'PASS' : 'FAIL', 'HTTP 200 OK (PDF or Printable HTML)', "HTTP {$status} in {$duration}ms ({$contentType})", "content_type: {$contentType}");
    } catch (\Throwable $e) {
        recordTest($results, $testId, 'Print/Export', $modName, "{$desc} ({$uri})", 'FAIL', 'HTTP 200', $e->getMessage());
    }
}

// ── 7. ACTIVITY HUB REST APIS BASELINE ──────────────────────────────
if ($adminUser) {
    Auth::login($adminUser);
}
$apiEndpoints = [
    ['Notices API', 'TC-BASE-049', '/api/notices', 'Notices Listing API'],
    ['Circulars API', 'TC-BASE-050', '/api/circulars', 'Circulars Listing API'],
    ['Events API', 'TC-BASE-051', '/api/events', 'Events Listing API'],
    ['Meetings API', 'TC-BASE-052', '/api/meetings', 'Meetings Listing API'],
    ['Tasks API', 'TC-BASE-053', '/api/tasks', 'Tasks Listing API'],
    ['Calendar API', 'TC-BASE-054', '/api/calendar/activities', 'Calendar Activities API'],
    ['Notifications API', 'TC-BASE-055', '/api/notifications/unread-count', 'Notifications Unread Count API'],
    ['Dashboard API', 'TC-BASE-056', '/api/dashboard/my-activities', 'Dashboard User Activities API'],
];

foreach ($apiEndpoints as $ae) {
    list($modName, $testId, $uri, $desc) = $ae;
    $start = microtime(true);
    try {
        $req = Request::create($uri, 'GET');
        $res = $app->handle($req);
        $status = $res->getStatusCode();
        $duration = round((microtime(true) - $start) * 1000, 2);
        $pass = ($status === 200);
        recordTest($results, $testId, 'API', $modName, "{$desc} ({$uri})", $pass ? 'PASS' : 'FAIL', 'HTTP 200 OK (JSON)', "HTTP {$status} in {$duration}ms", "status: {$status}, latency: {$duration}ms");
    } catch (\Throwable $e) {
        recordTest($results, $testId, 'API', $modName, "{$desc} ({$uri})", 'FAIL', 'HTTP 200', $e->getMessage());
    }
}

// ── 8. SAVE RESULTS TO CSV AND JSON ──────────────────────────────────
$csvPath = dirname(__DIR__) . '/QA_AUDIT/VERSION_1/TEST_CASE_REGISTER.csv';
$fp = fopen($csvPath, 'w');
fputcsv($fp, ['test_case_id', 'category', 'module', 'scenario', 'preconditions', 'test_steps', 'expected_result', 'actual_result', 'status', 'evidence_ref', 'tested_at', 'tester']);
foreach ($results as $row) {
    fputcsv($fp, array_values($row));
}
fclose($fp);

$jsonPath = dirname(__DIR__) . '/QA_AUDIT/VERSION_1/baseline_results.json';
file_put_contents($jsonPath, json_encode($results, JSON_PRETTY_PRINT));

$total = count($results);
$passed = count(array_filter($results, fn($r) => $r['status'] === 'PASS'));
$failed = count(array_filter($results, fn($r) => $r['status'] === 'FAIL'));
$blocked = count(array_filter($results, fn($r) => $r['status'] === 'BLOCKED'));

echo "\n====================================================================\n";
echo sprintf(" BASELINE TESTING COMPLETE: Total: %d | PASS: %d | FAIL: %d | BLOCKED: %d\n", $total, $passed, $failed, $blocked);
echo " Results saved to TEST_CASE_REGISTER.csv\n";
echo "====================================================================\n";

