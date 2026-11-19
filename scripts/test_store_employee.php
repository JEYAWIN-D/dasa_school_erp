<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- TESTING EMPLOYEE STORE WITH FRESHER CANDIDATE ---\n";

$uniqueSuffix = rand(1000, 9999);
$code = 'TEST' . $uniqueSuffix;
$aadhaar = '88887777' . $uniqueSuffix;
$pan = 'ABCDE' . $uniqueSuffix . 'Z';
$email = 'fresher.' . $uniqueSuffix . '@schoolerp.in';
$mobile = '98' . rand(10000000, 99999999);
$contactNo = '97' . rand(10000000, 99999999);

$req = new \Illuminate\Http\Request([
    'name' => 'Kavitha Test Fresher',
    'dob' => '1998-05-20',
    'gender' => 'female',
    'mother_tongue' => 'Tamil',
    'nationality' => 'Indian',
    'religion' => 'Hindu',
    'caste' => 'BC',
    'marital_status' => 'Single',
    'address' => '42 Erode Cross Road, Erode',
    'pincode' => '638001',
    'employee_code' => $code,
    'designation' => 'PRT English',
    'department' => 'English',
    'joining_date' => '2026-11-01',
    'current_employment_status' => 'Probation',
    'employee_type' => 'teaching',
    'experience_type' => 'fresher',
    'aadhaar_no' => $aadhaar,
    'pan_no' => $pan,
    'epf_no' => 'EPF' . $uniqueSuffix,
    'official_email' => $email,
    'mobile' => $mobile,
    'name_of_contact_person' => 'Ramasamy K',
    'contact_relationship' => 'Father',
    'contact_address' => '42 Erode Cross Road, Erode',
    'contact_no' => $contactNo,
    'academic_qualification' => 'B.A. English Literature',
    'academic_university' => 'Bharathiar University',
    'academic_medium' => 'English',
    'academic_institution' => 'Vellalar College for Women',
    'academic_percentage' => '82.5',
    'academic_year_of_passing' => 2021,
    'professional_qualification' => 'B.Ed English',
    'professional_university' => 'TNTEU',
    'professional_medium' => 'English',
    'professional_institution' => 'Government College of Education',
    'professional_percentage' => '79.0',
    'professional_year_of_passing' => 2023,
]);

// Set authenticated user as Super Admin
$admin = \App\Models\User::first();
\Illuminate\Support\Facades\Auth::login($admin);

$controller = new \App\Http\Controllers\Admin\HrController();
$response = $controller->storeEmployee($req);

echo "Response class: " . get_class($response) . "\n";
if (method_exists($response, 'getTargetUrl')) {
    echo "Redirect target: " . $response->getTargetUrl() . "\n";
}

$savedEmp = \App\Models\Employee::where('employee_code', $code)->first();
if ($savedEmp) {
    echo "[PASS] Successfully saved employee {$savedEmp->full_name} with code {$savedEmp->employee_code}!\n";
    echo "Category: {$savedEmp->employment_category}, Exp Years: {$savedEmp->experience_years}\n";
    
    // Clean up test employee
    if ($savedEmp->user_id) {
        \App\Models\User::where('id', $savedEmp->user_id)->forceDelete();
    }
    \App\Models\EmployeeQualification::where('employee_id', $savedEmp->id)->delete();
    $savedEmp->forceDelete();
    echo "Cleaned up test record successfully.\n";
} else {
    echo "[FAIL] Employee was not found in database!\n";
    if (session('error')) {
        echo "Session Error: " . session('error') . "\n";
    }
}
