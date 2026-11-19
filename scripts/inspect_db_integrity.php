<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "====================================================================\n";
echo " DASA EduERP — DATABASE INTEGRITY & ORPHANED RECORD INSPECTION\n";
echo "====================================================================\n\n";

// 1. Table Counts & Schema Overview
$tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' AND table_type = 'BASE TABLE' ORDER BY table_name");
echo "Total Tables in public schema: " . count($tables) . "\n\n";

$coreTables = [
    'students', 'student_enrollments', 'classes', 'sections', 'academic_years',
    'attendance_records', 'fee_structures', 'fee_heads', 'fee_invoices', 'fee_payments',
    'exams', 'exam_schedules', 'exam_marks', 'subjects', 'employees', 'salaries',
    'books', 'book_issues', 'vehicles', 'inventory_items', 'enquiries'
];

echo "Core Table Row Counts:\n";
foreach ($coreTables as $tbl) {
    try {
        $count = DB::table($tbl)->count();
        echo sprintf("- %-24s : %6d rows\n", $tbl, $count);
    } catch (\Throwable $e) {
        echo sprintf("- %-24s : TABLE NOT FOUND / ERROR (%s)\n", $tbl, $e->getMessage());
    }
}
echo "\n";

// 2. Orphaned Records Analysis
echo "Checking for Orphaned Foreign Keys:\n";

// A. student_enrollments -> students
$orphanedEnrollments = DB::select("
    SELECT count(*) as count 
    FROM student_enrollments se 
    LEFT JOIN students s ON se.student_id = s.id 
    WHERE s.id IS NULL
")[0]->count ?? 0;
echo "- student_enrollments without students: {$orphanedEnrollments}\n";

// B. student_enrollments -> classes
$orphanedClasses = DB::select("
    SELECT count(*) as count 
    FROM student_enrollments se 
    LEFT JOIN classes c ON se.class_id = c.id 
    WHERE c.id IS NULL
")[0]->count ?? 0;
echo "- student_enrollments without classes: {$orphanedClasses}\n";

// C. attendance_records -> students
$orphanedAttendance = DB::select("
    SELECT count(*) as count 
    FROM attendance_records ar 
    LEFT JOIN students s ON ar.student_id = s.id 
    WHERE s.id IS NULL
")[0]->count ?? 0;
echo "- attendance_records without students: {$orphanedAttendance}\n";

// D. fee_payments -> students
try {
    $orphanedFeePayments = DB::select("
        SELECT count(*) as count 
        FROM fee_payments fp 
        LEFT JOIN students s ON fp.student_id = s.id 
        WHERE s.id IS NULL
    ")[0]->count ?? 0;
    echo "- fee_payments without students: {$orphanedFeePayments}\n";
} catch (\Throwable $e) {
    echo "- fee_payments check: " . $e->getMessage() . "\n";
}

// E. exam_marks -> students
try {
    $orphanedMarks = DB::select("
        SELECT count(*) as count 
        FROM exam_marks em 
        LEFT JOIN students s ON em.student_id = s.id 
        WHERE s.id IS NULL
    ")[0]->count ?? 0;
    echo "- exam_marks without students: {$orphanedMarks}\n";
} catch (\Throwable $e) {
    echo "- exam_marks check: " . $e->getMessage() . "\n";
}

// F. enquiries -> classes
try {
    $orphanedEnquiries = DB::select("
        SELECT count(*) as count 
        FROM enquiries e 
        LEFT JOIN classes c ON e.class_id = c.id 
        WHERE e.class_id IS NOT NULL AND c.id IS NULL
    ")[0]->count ?? 0;
    echo "- enquiries with invalid class_id: {$orphanedEnquiries}\n";
} catch (\Throwable $e) {
    echo "- enquiries check: " . $e->getMessage() . "\n";
}

echo "\n";

// 3. Duplicate Key Checks
echo "Checking for Duplicate Unique Values:\n";

// A. Duplicate admission numbers
$dupAdmissionNos = DB::select("
    SELECT admission_no, count(*) as count 
    FROM students 
    WHERE admission_no IS NOT NULL AND admission_no != '' 
    GROUP BY admission_no 
    HAVING count(*) > 1
");
echo "- Duplicate admission_no in students: " . count($dupAdmissionNos) . "\n";

// B. Duplicate roll numbers in same section and academic year
$dupRollNumbers = DB::select("
    SELECT class_id, section_id, academic_year_id, roll_number, count(*) as count 
    FROM student_enrollments 
    WHERE roll_number IS NOT NULL AND roll_number != '' 
    GROUP BY class_id, section_id, academic_year_id, roll_number 
    HAVING count(*) > 1
");
echo "- Duplicate roll_number in same section/year: " . count($dupRollNumbers) . "\n";

// C. Duplicate employee codes
try {
    $dupEmpCodes = DB::select("
        SELECT employee_code, count(*) as count 
        FROM employees 
        WHERE employee_code IS NOT NULL AND employee_code != '' 
        GROUP BY employee_code 
        HAVING count(*) > 1
    ");
    echo "- Duplicate employee_code in employees: " . count($dupEmpCodes) . "\n";
} catch (\Throwable $e) {
    echo "- employees check: " . $e->getMessage() . "\n";
}

echo "\n";

// 4. Index Inspection on Foreign Keys & High-Traffic Filter Columns
echo "Checking Indexes on High-Traffic Columns:\n";
$indexes = DB::select("
    SELECT tablename, indexname, indexdef 
    FROM pg_indexes 
    WHERE schemaname = 'public' 
    ORDER BY tablename, indexname
");
echo "Total defined indexes in PostgreSQL: " . count($indexes) . "\n";

$targetIndexes = [
    'students' => ['admission_no', 'mobile', 'email', 'status', 'dob'],
    'student_enrollments' => ['student_id', 'class_id', 'section_id', 'academic_year_id', 'roll_number'],
    'attendance_records' => ['student_id', 'class_id', 'section_id', 'date', 'status'],
    'fee_payments' => ['student_id', 'payment_date', 'status'],
    'exam_marks' => ['student_id', 'exam_id', 'subject_id']
];

foreach ($targetIndexes as $tbl => $cols) {
    echo "Table: {$tbl}\n";
    $tblIndexes = array_filter($indexes, fn($idx) => $idx->tablename === $tbl);
    foreach ($cols as $col) {
        $hasIdx = false;
        foreach ($tblIndexes as $ti) {
            if (str_contains($ti->indexdef, "($col)") || str_contains($ti->indexdef, ", $col") || str_contains($ti->indexdef, "$col,")) {
                $hasIdx = true;
                break;
            }
        }
        echo sprintf("  - %-20s : %s\n", $col, $hasIdx ? 'INDEXED' : 'NO DEDICATED INDEX');
    }
}

