<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AcademicYear;
use Illuminate\Support\Facades\DB;

$currentYear = AcademicYear::current();
echo "========================================================\n";
echo "1. TIMETABLE DATABASE AUDIT FOR CURRENT YEAR ID {$currentYear->id} ({$currentYear->name})\n";
echo "========================================================\n\n";

// 1. Total slots count
$totalSlots = DB::table('timetables')->where('academic_year_id', $currentYear->id)->count();
echo "Total timetable entries for {$currentYear->name}: {$totalSlots}\n";

// 2. Category & Class Breakdown
$classes = DB::table('classes')->orderBy('sort_order')->get();
echo "\nBreakdown by Class / Category:\n";
foreach ($classes as $c) {
    $slotCount = DB::table('timetables')->where('academic_year_id', $currentYear->id)->where('class_id', $c->id)->count();
    $sectionsCount = DB::table('sections')->where('class_id', $c->id)->count();
    $assignedTeachers = DB::table('timetables')
        ->where('academic_year_id', $currentYear->id)
        ->where('class_id', $c->id)
        ->whereNotNull('teacher_id')
        ->count();
    $unassigned = $slotCount - $assignedTeachers;
    echo sprintf(
        " - Standard %-15s (ID %2d, Cat: %-15s): %2d sections | %4d slots (%4d assigned, %2d unassigned)\n",
        $c->name,
        $c->id,
        $c->category ?? 'N/A',
        $sectionsCount,
        $slotCount,
        $assignedTeachers,
        $unassigned
    );
}

// 3. Duplicate slot check: same class, section, day, period
echo "\nChecking for duplicate slots (class + section + day + period):\n";
$duplicates = DB::table('timetables')
    ->where('academic_year_id', $currentYear->id)
    ->select('class_id', 'section_id', 'day_of_week', 'period_number', DB::raw('count(*) as cnt'))
    ->groupBy('class_id', 'section_id', 'day_of_week', 'period_number')
    ->havingRaw('count(*) > 1')
    ->get();

echo "Duplicate slot groups found: " . $duplicates->count() . "\n";
if ($duplicates->count() > 0) {
    foreach ($duplicates->take(10) as $d) {
        echo "  Duplicate: Class {$d->class_id}, Sec {$d->section_id}, Day {$d->day_of_week}, Period {$d->period_number} (Count: {$d->cnt})\n";
    }
}

// 4. Teacher Conflict Check: Same teacher assigned to different class/section on same day & period
echo "\nChecking for Teacher Assignment Conflicts (same teacher, same day, same period in > 1 section):\n";
$conflicts = DB::table('timetables as t1')
    ->join('timetables as t2', function ($join) {
        $join->on('t1.academic_year_id', '=', 't2.academic_year_id')
             ->on('t1.teacher_id', '=', 't2.teacher_id')
             ->on('t1.day_of_week', '=', 't2.day_of_week')
             ->on('t1.period_number', '=', 't2.period_number')
             ->whereRaw('t1.id < t2.id');
    })
    ->where('t1.academic_year_id', $currentYear->id)
    ->whereNotNull('t1.teacher_id')
    ->select(
        't1.teacher_id',
        't1.day_of_week',
        't1.period_number',
        't1.class_id as c1',
        't1.section_id as s1',
        't2.class_id as c2',
        't2.section_id as s2'
    )
    ->get();

echo "Teacher conflicts found: " . $conflicts->count() . "\n";
if ($conflicts->count() > 0) {
    foreach ($conflicts->take(10) as $cf) {
        $emp = DB::table('employees')->find($cf->teacher_id);
        $name = $emp ? ($emp->first_name . ' ' . $emp->last_name) : 'ID '.$cf->teacher_id;
        echo "  Conflict: Teacher {$name} on Day {$cf->day_of_week}, Period {$cf->period_number} -> Class {$cf->c1} vs Class {$cf->c2}\n";
    }
}

// 5. Check integrity of foreign keys
echo "\nChecking Foreign Key Integrity:\n";
$orphanedClasses = DB::table('timetables as t')
    ->leftJoin('classes as c', 'c.id', '=', 't.class_id')
    ->whereNull('c.id')
    ->count();
$orphanedSections = DB::table('timetables as t')
    ->leftJoin('sections as s', 's.id', '=', 't.section_id')
    ->whereNull('s.id')
    ->count();
$orphanedSubjects = DB::table('timetables as t')
    ->leftJoin('subjects as sub', 'sub.id', '=', 't.subject_id')
    ->whereNotNull('t.subject_id')
    ->whereNull('sub.id')
    ->count();
$orphanedTeachers = DB::table('timetables as t')
    ->leftJoin('employees as e', 'e.id', '=', 't.teacher_id')
    ->whereNotNull('t.teacher_id')
    ->whereNull('e.id')
    ->count();

echo "Orphaned Class refs: {$orphanedClasses}\n";
echo "Orphaned Section refs: {$orphanedSections}\n";
echo "Orphaned Subject refs: {$orphanedSubjects}\n";
echo "Orphaned Teacher refs: {$orphanedTeachers}\n";

