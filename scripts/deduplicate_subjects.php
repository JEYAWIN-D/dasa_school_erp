<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== STARTING SUBJECT DEDUPLICATION & CLEANUP ===\n";

$tables = [
    'competencies' => 'subject_id',
    'exam_schedules' => 'subject_id',
    'homework' => 'subject_id',
    'lesson_plans' => 'subject_id',
    'question_bank' => 'subject_id',
    'question_papers' => 'subject_id',
    'syllabus' => 'subject_id',
    'teacher_subject_allocations' => 'subject_id',
    'timetables' => 'subject_id',
    'period_reports' => 'subject_id',
    'extra_classes' => 'subject_id',
    'staff_test_reminders' => 'subject_id',
];

// Helper to merge duplicate IDs into canonical ID
function mergeSubject($canonicalId, array $duplicateIds, $tables) {
    foreach ($duplicateIds as $dupId) {
        if ($dupId == $canonicalId) continue;
        foreach ($tables as $table => $col) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, $col)) {
                // For tables with unique constraints, handle potential conflict
                if ($table === 'teacher_subject_allocations') {
                    // Check if canonical already has allocation for same employee + class + section + year
                    $existingAllocations = DB::table($table)->where($col, $dupId)->get();
                    foreach ($existingAllocations as $ea) {
                        $exists = DB::table($table)
                            ->where('employee_id', $ea->employee_id)
                            ->where('class_id', $ea->class_id)
                            ->where('section_id', $ea->section_id)
                            ->where('academic_year_id', $ea->academic_year_id)
                            ->where($col, $canonicalId)
                            ->exists();
                        if ($exists) {
                            DB::table($table)->where('id', $ea->id)->delete();
                        } else {
                            DB::table($table)->where('id', $ea->id)->update([$col => $canonicalId]);
                        }
                    }
                } else {
                    DB::table($table)->where($col, $dupId)->update([$col => $canonicalId]);
                }
            }
        }
        // Delete the duplicate subject
        DB::table('subjects')->where('id', $dupId)->delete();
        echo "  - Merged duplicate ID $dupId into canonical ID $canonicalId and deleted $dupId\n";
    }
}

// 1. Tamil: Canonical ID 1, Dups: 18, 49, 58, 106
mergeSubject(1, [18, 49, 58, 106], $tables);

// 2. English: Canonical ID 2, Dups: 107
mergeSubject(2, [107], $tables);

// 3. Mathematics: Canonical ID 3, Dups: 13, 51, 108
mergeSubject(3, [13, 51, 108], $tables);

// 4. Science: Canonical ID 4, Dups: 109
mergeSubject(4, [109], $tables);

// 5. Social Science: Canonical ID 5, Dups: 15, 110
mergeSubject(5, [15, 110], $tables);

// 6. Hindi: Canonical ID 6, Dups: 111
mergeSubject(6, [111], $tables);

// 7. Computer Science: Canonical ID 7, Dups: 112
mergeSubject(7, [112], $tables);

// 8. General Knowledge: Canonical ID 102, Dups: 114
mergeSubject(102, [114], $tables);

// 9. Physical & Health Education: Canonical ID 16, Dups: 65
mergeSubject(16, [65], $tables);

// 10. KG Rhymes: Canonical ID 84, Dups: 85..95
mergeSubject(84, [85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95], $tables);

// 11. Mathematics Test: Canonical ID 83, Dups: 96
mergeSubject(83, [96], $tables);

// 12. Environmental Studies (EVS): Canonical ID 68, Dups: 52
mergeSubject(68, [52], $tables);

// 13. Library: Canonical ID 105, Dups: 115
mergeSubject(105, [115], $tables);

// Delete mock/duplicate dummy subject (ID 82)
DB::table('subjects')->where('name', 'Duplicate Subject')->delete();
echo "  - Deleted 'Duplicate Subject'\n";

// Now, verify remaining duplicates
$remainingDups = DB::table('subjects')
    ->select('name', DB::raw('count(*) as count'))
    ->groupBy('name')
    ->havingRaw('count(*) > 1')
    ->get();

echo "\nRemaining duplicate subject names: " . count($remainingDups) . "\n";
echo "Total subjects remaining: " . DB::table('subjects')->count() . "\n";

// Clear timetables table so it is clean for manual assignment as requested
echo "\n=== CLEARING TIMETABLES FOR MANUAL ASSIGNMENT ===\n";
DB::table('timetables')->delete();
echo "Timetable slots cleared. Current count: " . DB::table('timetables')->count() . "\n";

\Illuminate\Support\Facades\Cache::flush();
echo "Cache flushed successfully.\n";

