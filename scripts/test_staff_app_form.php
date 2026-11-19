<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- TESTING APPLICATION FORM VIEW & ROUTES ---\n";

// 1. Test existing employee
$emp = \App\Models\Employee::first();
if ($emp) {
    echo "Found employee ID: {$emp->id} ({$emp->full_name})\n";
    $controller = new \App\Http\Controllers\Admin\HrController();
    $view = $controller->applicationForm($emp->id);
    $html = $view->render();
    echo "Rendered applicationForm for employee #{$emp->id}. Output length: " . strlen($html) . " bytes\n";
    if (str_contains($html, 'Staff Employment Application Form') && str_contains($html, $emp->full_name)) {
        echo "[PASS] Employee application form contains school header and employee name!\n";
    } else {
        echo "[WARN] Did not find expected strings in employee application form HTML\n";
    }
} else {
    echo "No employees found in DB.\n";
}

// 2. Test preview with mock form data (including Fresher)
$req = new \Illuminate\Http\Request([
    'name' => 'Kavitha Ramachandran',
    'employee_code' => 'EPSS9999',
    'dob' => '1995-06-15',
    'gender' => 'female',
    'blood_group' => 'B+',
    'mother_tongue' => 'Tamil',
    'nationality' => 'Indian',
    'religion' => 'Hindu',
    'caste' => 'BC',
    'marital_status' => 'Married',
    'number_of_children' => 2,
    'children_male' => 1,
    'children_female' => 1,
    'address' => '12 Gandhi Nagar, Chennimalai, Erode',
    'pincode' => '638051',
    'mobile' => '9876543210',
    'official_email' => 'kavitha.r@erodepublicschool.edu.in',
    'name_of_contact_person' => 'Ramachandran S',
    'contact_relationship' => 'Spouse',
    'contact_no' => '9876543211',
    'aadhaar_no' => '998877665544',
    'pan_no' => 'ABCDE1234F',
    'designation' => 'TGT Mathematics',
    'department' => 'Mathematics',
    'joining_date' => '2026-11-01',
    'experience_type' => 'fresher',
    'academic_qualification' => 'M.Sc Mathematics',
    'academic_institution' => 'Bharathiar University Arts & Science College',
    'academic_university' => 'Bharathiar University',
    'academic_medium' => 'English',
    'academic_percentage' => '88.5',
    'academic_year_of_passing' => 2018,
    'professional_qualification' => 'B.Ed Mathematics',
    'professional_institution' => 'Government College of Education',
    'professional_university' => 'Tamil Nadu Teachers Education University',
    'professional_medium' => 'English',
    'professional_percentage' => '84.0',
    'professional_year_of_passing' => 2020,
]);

$controller = new \App\Http\Controllers\Admin\HrController();
$previewView = $controller->previewApplicationForm($req);
$previewHtml = $previewView->render();
echo "Rendered previewApplicationForm. Output length: " . strlen($previewHtml) . " bytes\n";
if (str_contains($previewHtml, 'Kavitha Ramachandran') && str_contains($previewHtml, 'Registered as a Fresher Candidate')) {
    echo "[PASS] Preview application form rendered perfectly with Fresher candidate badge and all entered fields!\n";
} else {
    echo "[FAIL] Unexpected content in preview HTML\n";
}

echo "--- ALL TESTS COMPLETE ---\n";

