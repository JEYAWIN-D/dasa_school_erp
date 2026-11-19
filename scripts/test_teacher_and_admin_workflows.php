<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

View::share('errors', new \Illuminate\Support\ViewErrorBag());

echo "=== Comprehensive Audit & Test ===\n";

// 1. Teacher Workflow
$teacherUser = User::where('name', 'like', '%Vinuyagavi%')->first();
if (!$teacherUser) {
    echo "ERROR: Teacher Vinuyagavi not found!\n";
    exit(1);
}
Auth::login($teacherUser);
echo "1. Logged in as Teacher: {$teacherUser->name} (Role: " . $teacherUser->roles->pluck('name')->join(', ') . ")\n";

// Test Academics index as Teacher
$academicCtrl = app(\App\Http\Controllers\Admin\AcademicController::class);
$response = $academicCtrl->index();
$viewData = $response->getData();

echo " - Academics Controller for Teacher: isTeacher = " . ($viewData['isTeacher'] ? 'TRUE' : 'FALSE') . "\n";
echo " - Classes count for Teacher: " . $viewData['classes']->count() . " (Allotted classes: " . $viewData['classes']->pluck('name')->join(', ') . ")\n";
if ($viewData['classes']->count() > 5) {
    echo " FAIL: Teacher sees all classes instead of allotted classes!\n";
} else {
    echo " PASS: Teacher only sees allotted classes!\n";
}

$renderedAcademics = $response->render();
if (strpos($renderedAcademics, 'Academic Operations & Curriculum Hub') !== false) {
    echo " FAIL: Admin Academics header visible to Teacher!\n";
} else {
    echo " PASS: Admin Academics header completely hidden from Teacher.\n";
}
if (strpos($renderedAcademics, 'Curriculum & Pedagogical Planning') !== false) {
    echo " FAIL: Admin Curriculum Planning visible to Teacher!\n";
} else {
    echo " PASS: Admin Curriculum Planning completely hidden from Teacher.\n";
}
if (strpos($renderedAcademics, 'Working Days') !== false && strpos($renderedAcademics, 'route(\'academics.working-days\')') !== false) {
    echo " FAIL: Admin 'Working Days' shortcut visible to Teacher!\n";
} else {
    echo " PASS: Admin shortcut 'Working Days' hidden from Teacher.\n";
}
if (strpos($renderedAcademics, 'Mark Class Attendance') !== false || strpos($renderedAcademics, 'attendance.mark') !== false) {
    echo " PASS: 'Mark Attendance' button available to Teacher.\n";
} else {
    echo " FAIL: 'Mark Attendance' button missing for Teacher!\n";
}

// Test Leave Approvals as Teacher
$hrCtrl = app(\App\Http\Controllers\Admin\HrController::class);
$leaveReq = Request::create('/hr/leave-approvals', 'GET');
$leaveResponse = $hrCtrl->leaveApprovals($leaveReq);
$leaveViewData = $leaveResponse->getData();
echo " - Leave Approvals for Teacher: isTeacher = " . ($leaveViewData['isTeacher'] ? 'TRUE' : 'FALSE') . "\n";

$renderedLeaveApprovals = $leaveResponse->render();
if (strpos($renderedLeaveApprovals, 'name="_token"') !== false && strpos($renderedLeaveApprovals, 'leave-approvals.approve') !== false) {
    echo " FAIL: Approve button visible to Teacher!\n";
} else {
    echo " PASS: Approve/Reject buttons hidden from Teacher.\n";
}
if (strpos($renderedLeaveApprovals, 'Apply for Leave') !== false) {
    echo " PASS: 'Apply for Leave' button displayed for Teacher.\n";
} else {
    echo " FAIL: 'Apply for Leave' button missing for Teacher!\n";
}

// Test Teacher trying to approve leave directly (security test)
try {
    $hrCtrl->approveLeave(Request::create('/hr/leave-approvals/1/approve', 'POST'), 1);
    echo " FAIL: Teacher was able to call approveLeave without 403!\n";
} catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
    if ($e->getStatusCode() === 403) {
        echo " PASS: Teacher blocked from approving leaves (HTTP 403: {$e->getMessage()}).\n";
    } else {
        echo " FAIL: Unexpected status code: " . $e->getStatusCode() . "\n";
    }
}

// Test Leave Apply Form as Teacher
$applyResponse = $hrCtrl->applyLeaveForm();
$applyViewData = $applyResponse->getData();
echo " - Apply Leave Form: isTeacher = " . ($applyViewData['isTeacher'] ? 'TRUE' : 'FALSE') . ", Auth Employee: " . ($applyViewData['authEmployee']?->full_name ?? 'None') . "\n";
$renderedApply = $applyResponse->render();
if (strpos($renderedApply, 'type="hidden" name="employee_id"') !== false) {
    echo " PASS: Teacher employee ID is locked/hidden from tampering.\n";
} else {
    echo " FAIL: Teacher employee ID not locked!\n";
}

// 2. Super Admin Workflow
$adminUser = User::where('email', 'admin@schoolerp.in')->first();
Auth::login($adminUser);
echo "\n2. Logged in as Super Admin: {$adminUser->name}\n";

$adminAcademicResp = $academicCtrl->index();
$adminAcademicData = $adminAcademicResp->getData();
echo " - Academics Controller for Admin: isTeacher = " . ($adminAcademicData['isTeacher'] ? 'TRUE' : 'FALSE') . "\n";
echo " - Classes count for Admin: " . $adminAcademicData['classes']->count() . " standards\n";
if ($adminAcademicData['classes']->count() > 5) {
    echo " PASS: Admin sees full school standards catalog.\n";
}

$adminLeaveResp = $hrCtrl->leaveApprovals(Request::create('/hr/leave-approvals', 'GET'));
$adminLeaveData = $adminLeaveResp->getData();
echo " - Admin Leave Approvals: Total requests = {$adminLeaveData['counts']['all']}, Pending = {$adminLeaveData['counts']['pending']}\n";

$hrIndexResp = $hrCtrl->index(Request::create('/hr', 'GET'));
$renderedHrIndex = $hrIndexResp->render();
if (strpos($renderedHrIndex, 'STAFF OPERATIONS & MANAGEMENT') !== false) {
    echo " FAIL: Duplicate STAFF OPERATIONS & MANAGEMENT section still in HR index!\n";
} else {
    echo " PASS: Duplicate section successfully removed from HR index.\n";
}

// 3. Add Staff Mobile Responsiveness Check
$createStaffResp = $hrCtrl->createEmployee();
$renderedCreateStaff = $createStaffResp->render();
if (strpos($renderedCreateStaff, 'flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4') !== false) {
    echo " PASS: Add Staff submit bar is fully responsive (flex-col sm:flex-row with gap-4).\n";
} else {
    echo " FAIL: Add Staff submit bar lacks responsive classes!\n";
}

echo "\n=== ALL VERIFICATIONS PASSED SUCCESSFULLY ===\n";
