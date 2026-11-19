<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Employee;
use App\Models\Section;
use App\Models\Subject;
use App\Models\TeacherLevelEligibility;
use App\Models\TeacherSubjectAllocation;
use App\Models\TeachingLevel;
use App\Models\Timetable;
use Illuminate\Support\Facades\DB;

class StaffSyncService
{
    public static function run(): array
    {
        $log = [];
        $currentYear = AcademicYear::current() ?? AcademicYear::first();
        $academicYearId = $currentYear?->id ?? 1;

        // 1. Fetch or create teaching levels
        $kgLevel = TeachingLevel::where('code', 'kg')->orWhere('name', 'ilike', '%kindergarten%')->first();
        $priLevel = TeachingLevel::where('code', 'primary')->orWhere('name', 'ilike', '%primary%')->first();
        $secLevel = TeachingLevel::where('code', 'secondary')->orWhere('name', 'ilike', '%secondary%')->first();
        $hsLevel = TeachingLevel::where('code', 'higher_sec')->orWhere('name', 'ilike', '%higher%')->first();

        // 2. Clear out old allocations and unbind staff from timetable & sections
        DB::table('teacher_subject_allocations')->delete();
        DB::table('teacher_level_eligibilities')->delete();
        if (\Illuminate\Support\Facades\Schema::hasTable('timetables')) {
            DB::table('timetables')->update(['teacher_id' => null]);
        }
        if (\Illuminate\Support\Facades\Schema::hasTable('sections')) {
            DB::table('sections')->update(['class_teacher_id' => null, 'co_class_teacher_id' => null]);
        }
        $log[] = "Cleared existing teacher allocations and timetable links.";

        // 3. Completely delete ALL old staff records (hard purge without affecting students)
        $oldCount = DB::table('employees')->count();
        if (\Illuminate\Support\Facades\Schema::hasColumn('students', 'parent_employee_id')) {
            DB::table('students')->update(['parent_employee_id' => null]);
        }
        DB::table('employees')->delete();
        $log[] = "Completely removed {$oldCount} old staff records. Zero remaining.";

        // 4. Define 42 real teachers
        $teachersData = [
            // KG Teachers (8)
            ['code' => 'EPS-T001', 'name' => 'VINUYAGAVI', 'level' => $kgLevel, 'dept' => 'Kindergarten', 'designation' => 'Kindergarten (Pre-KG, LKG, UKG)'],
            ['code' => 'EPS-T002', 'name' => 'PREETHI', 'level' => $kgLevel, 'dept' => 'Kindergarten', 'designation' => 'Kindergarten (Pre-KG, LKG, UKG)'],
            ['code' => 'EPS-T003', 'name' => 'DIVYA', 'level' => $kgLevel, 'dept' => 'Kindergarten', 'designation' => 'Kindergarten (Pre-KG, LKG, UKG)'],
            ['code' => 'EPS-T004', 'name' => 'PERCIA PRIYADHARSHINI', 'level' => $kgLevel, 'dept' => 'Kindergarten', 'designation' => 'Kindergarten (Pre-KG, LKG, UKG)'],
            ['code' => 'EPS-T005', 'name' => 'BAGHAMITHA NAGAR', 'level' => $kgLevel, 'dept' => 'Kindergarten', 'designation' => 'Kindergarten (Pre-KG, LKG, UKG)'],
            ['code' => 'EPS-T006', 'name' => 'SOWNDARYA', 'level' => $kgLevel, 'dept' => 'Kindergarten', 'designation' => 'Kindergarten (Pre-KG, LKG, UKG)'],
            ['code' => 'EPS-T007', 'name' => 'DHARANI', 'level' => $kgLevel, 'dept' => 'Kindergarten', 'designation' => 'Kindergarten (Pre-KG, LKG, UKG)'],
            ['code' => 'EPS-T008', 'name' => 'KAVIYA', 'level' => $kgLevel, 'dept' => 'Kindergarten', 'designation' => 'Kindergarten (Pre-KG, LKG, UKG)'],

            // Primary Teachers (16)
            ['code' => 'EPS-T009', 'name' => 'YASODHA', 'level' => $priLevel, 'dept' => 'English', 'designation' => 'Primary (Grades I–V)'],
            ['code' => 'EPS-T010', 'name' => 'BALAHARINI', 'level' => $priLevel, 'dept' => 'Science', 'designation' => 'Primary (Grades I–V)'],
            ['code' => 'EPS-T011', 'name' => 'HARI PRIYA', 'level' => $priLevel, 'dept' => 'English', 'designation' => 'Primary (Grades I–V)'],
            ['code' => 'EPS-T012', 'name' => 'RITHIKA SHREE', 'level' => $priLevel, 'dept' => 'English', 'designation' => 'Primary (Grades I–V)'],
            ['code' => 'EPS-T013', 'name' => 'MANJU', 'level' => $priLevel, 'dept' => 'Tamil', 'designation' => 'Primary (Grades I–V)'],
            ['code' => 'EPS-T014', 'name' => 'LAVANYA', 'level' => $priLevel, 'dept' => 'Tamil', 'designation' => 'Primary (Grades I–V)'],
            ['code' => 'EPS-T015', 'name' => 'KAVITHA', 'level' => $priLevel, 'dept' => 'Tamil', 'designation' => 'Primary (Grades I–V)'],
            ['code' => 'EPS-T016', 'name' => 'SNEHA', 'level' => $priLevel, 'dept' => 'Mathematics', 'designation' => 'Primary (Grades I–V)'],
            ['code' => 'EPS-T017', 'name' => 'MOHANA SUNDARI', 'level' => $priLevel, 'dept' => 'Mathematics', 'designation' => 'Primary (Grades I–V)'],
            ['code' => 'EPS-T018', 'name' => 'PRIYANKA', 'level' => $priLevel, 'dept' => 'Mathematics', 'designation' => 'Primary (Grades I–V)'],
            ['code' => 'EPS-T019', 'name' => 'RASIGA PRIYA', 'level' => $priLevel, 'dept' => 'Science', 'designation' => 'Primary (Grades I–V)'],
            ['code' => 'EPS-T020', 'name' => 'GEETHA BHARATHI', 'level' => $priLevel, 'dept' => 'Social Science', 'designation' => 'Primary (Grades I–V)'],
            ['code' => 'EPS-T021', 'name' => 'SHOBIKA', 'level' => $priLevel, 'dept' => 'Science', 'designation' => 'Primary (Grades I–V)'],
            ['code' => 'EPS-T022', 'name' => 'RAJESWARI', 'level' => $priLevel, 'dept' => 'Social Science', 'designation' => 'Primary (Grades I–V)'],
            ['code' => 'EPS-T023', 'name' => 'SARANYA', 'level' => $priLevel, 'dept' => 'Hindi', 'designation' => 'Primary (Grades I–V)'],
            ['code' => 'EPS-T024', 'name' => 'VALARMATHI', 'level' => $priLevel, 'dept' => 'Computer Science', 'designation' => 'Primary (Grades I–V)'],

            // Middle, Secondary & Senior Secondary (18)
            ['code' => 'EPS-T025', 'name' => 'MAHALAXMI', 'level' => $secLevel, 'dept' => 'Tamil', 'designation' => 'Secondary (Grades VI–X)'],
            ['code' => 'EPS-T026', 'name' => 'VIMAL', 'level' => $secLevel, 'dept' => 'Tamil', 'designation' => 'Secondary (Grades VI–X)'],
            ['code' => 'EPS-T027', 'name' => 'VASUGHI', 'level' => $secLevel, 'dept' => 'English', 'designation' => 'Secondary (Grades VI–X)'],
            ['code' => 'EPS-T028', 'name' => 'SUBHASHINI', 'level' => $secLevel, 'dept' => 'English', 'designation' => 'Secondary (Grades VI–X)'],
            ['code' => 'EPS-T029', 'name' => 'LOGACHITHRA', 'level' => $secLevel, 'dept' => 'English', 'designation' => 'Secondary (Grades VI–X)'],
            ['code' => 'EPS-T030', 'name' => 'DEEPIKA', 'level' => $secLevel, 'dept' => 'Mathematics', 'designation' => 'Secondary (Grades VI–X)'],
            ['code' => 'EPS-T031', 'name' => 'BINDHYA', 'level' => $secLevel, 'dept' => 'Science', 'designation' => 'Secondary (Grades VI–X)'],
            ['code' => 'EPS-T032', 'name' => 'NAZREEN SULTHANA', 'level' => $secLevel, 'dept' => 'Mathematics', 'designation' => 'Secondary (Grades VI–X)'],
            ['code' => 'EPS-T033', 'name' => 'CHITHRA', 'level' => $secLevel, 'dept' => 'Science', 'designation' => 'Secondary (Grades VI–X)'],
            ['code' => 'EPS-T034', 'name' => 'DIVYADHARSHINI', 'level' => $secLevel, 'dept' => 'Social Science', 'designation' => 'Secondary (Grades VI–X)'],
            ['code' => 'EPS-T035', 'name' => 'ROSHINI', 'level' => $secLevel, 'dept' => 'Social Science', 'designation' => 'Secondary (Grades VI–X)'],
            ['code' => 'EPS-T036', 'name' => 'VIGNESH', 'level' => $secLevel, 'dept' => 'Commerce', 'designation' => 'Secondary (Grades VI–X)'],
            ['code' => 'EPS-T037', 'name' => 'RATHI', 'level' => $secLevel, 'dept' => 'Hindi', 'designation' => 'Secondary (Grades VI–X)'],
            ['code' => 'EPS-T038', 'name' => 'GOBIKA', 'level' => $secLevel, 'dept' => 'Computer Science', 'designation' => 'Secondary (Grades VI–X)'],
            ['code' => 'EPS-T039', 'name' => 'AMMULAKSHMI', 'level' => $secLevel, 'dept' => 'Computer Science', 'designation' => 'Secondary (Grades VI–X)'],
            ['code' => 'EPS-T040', 'name' => 'ELAKKIYA', 'level' => $secLevel, 'dept' => 'Science', 'designation' => 'Secondary (Grades VI–X)'],
            ['code' => 'EPS-T041', 'name' => 'SAMIR SAHU', 'level' => $secLevel, 'dept' => 'Biology', 'designation' => 'Secondary (Grades VI–X)'],
            ['code' => 'EPS-T042', 'name' => 'VADIVEL', 'level' => $secLevel, 'dept' => 'Physical Education', 'designation' => 'Secondary (Grades VI–X)'],
        ];

        $teacherMap = [];
        $teacherUserMap = [];
        foreach ($teachersData as $t) {
            $cleanTName = strtoupper(trim($t['name']));
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $cleanTName));
            $user = \App\Models\User::firstOrCreate(
                ['email' => "{$slug}@erodepublicschool.edu.in"],
                [
                    'name'     => ucwords(strtolower($cleanTName)),
                    'password' => \Illuminate\Support\Facades\Hash::make('Teacher@123'),
                ]
            );
            if (method_exists($user, 'assignRole')) {
                $user->assignRole('teacher');
            }

            $emp = Employee::withTrashed()->updateOrCreate(
                ['employee_code' => $t['code']],
                [
                    'user_id'         => $user->id,
                    'first_name'      => $t['name'],
                    'last_name'       => '',
                    'employee_type'   => 'teaching',
                    'department'      => $t['dept'],
                    'designation'     => $t['designation'],
                    'gender'          => in_array($t['name'], ['VIMAL', 'VIGNESH', 'SAMIR SAHU', 'VADIVEL']) ? 'male' : 'female',
                    'is_active'       => true,
                    'approval_status' => 'approved',
                    'joining_date'    => '2024-06-01',
                    'deleted_at'      => null,
                ]
            );
            $emp->update(['user_id' => $user->id]);

            // Grant eligibility for their level
            if ($t['level']) {
                TeacherLevelEligibility::updateOrCreate(
                    ['employee_id' => $emp->id, 'teaching_level_id' => $t['level']->id],
                    ['is_approved' => true]
                );
            }
            if ($hsLevel && in_array($t['name'], ['LOGACHITHRA', 'NAZREEN SULTHANA', 'CHITHRA', 'ELAKKIYA', 'SAMIR SAHU', 'ROSHINI', 'VIGNESH', 'AMMULAKSHMI', 'VADIVEL'])) {
                TeacherLevelEligibility::updateOrCreate(
                    ['employee_id' => $emp->id, 'teaching_level_id' => $hsLevel->id],
                    ['is_approved' => true]
                );
            }
            if ($secLevel && $t['name'] === 'PRIYANKA') {
                TeacherLevelEligibility::updateOrCreate(
                    ['employee_id' => $emp->id, 'teaching_level_id' => $secLevel->id],
                    ['is_approved' => true]
                );
            }

            $teacherMap[$cleanTName] = $emp;
            $teacherUserMap[$cleanTName] = $user;
        }

        // Aliases
        $teacherMap['KAVITHA.R'] = $teacherMap['KAVITHA'];
        $teacherMap['HARIPRIYA'] = $teacherMap['HARI PRIYA'];
        $teacherMap['SAMIR'] = $teacherMap['SAMIR SAHU'];

        $teacherUserMap['KAVITHA.R'] = $teacherUserMap['KAVITHA'];
        $teacherUserMap['HARIPRIYA'] = $teacherUserMap['HARI PRIYA'];
        $teacherUserMap['SAMIR'] = $teacherUserMap['SAMIR SAHU'];

        $log[] = "Registered and verified 42 real teachers.";

        // 5. Helper function to ensure subject exists
        $ensureSubject = function ($subjectName) {
            return Subject::firstOrCreate(
                ['name' => $subjectName],
                [
                    'code'      => strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $subjectName), 0, 6)),
                    'is_active' => true,
                    'type'      => 'theory',
                ]
            );
        };

        // 6. Helper function to find class and section
        $getClassAndSection = function ($className, $sectionName = null) use ($academicYearId) {
            $class = Classes::where('name', $className)->first();
            if (!$class) {
                // Try roman to numeric or case-insensitive
                $class = Classes::whereRaw('LOWER(name) = ?', [strtolower($className)])->first();
            }

            $section = null;
            if ($class && $sectionName) {
                $section = Section::where('class_id', $class->id)
                    ->whereRaw('LOWER(name) = ?', [strtolower($sectionName)])
                    ->first();
                if (!$section) {
                    $section = Section::create([
                        'class_id'         => $class->id,
                        'name'             => strtoupper($sectionName),
                        'academic_year_id' => $academicYearId,
                        'capacity'         => 40,
                        'is_active'        => true,
                    ]);
                }
            }

            return [$class, $section];
        };

        // 7. Helper to assign single allocation
        $assign = function ($teacherName, $subjectName, $className, $sectionName = null) use (&$teacherMap, &$ensureSubject, &$getClassAndSection, $academicYearId, &$log) {
            $cleanTName = strtoupper(trim($teacherName));
            if (!isset($teacherMap[$cleanTName])) {
                return;
            }
            $emp = $teacherMap[$cleanTName];
            $subject = $ensureSubject($subjectName);
            [$class, $section] = $getClassAndSection($className, $sectionName);

            if (!$class) {
                return;
            }

            TeacherSubjectAllocation::firstOrCreate([
                'employee_id'      => $emp->id,
                'subject_id'       => $subject->id,
                'class_id'         => $class->id,
                'section_id'       => $section?->id,
                'academic_year_id' => $academicYearId,
            ]);
        };

        // ════════════════════════════════════════════════════════════
        // 8. KG ALLOTMENTS
        // ════════════════════════════════════════════════════════════
        $kgAllotments = [
            ['VINUYAGAVI', 'Pre-KG', 'A'],
            ['PREETHI', 'Pre-KG', 'B'],
            ['DIVYA', 'LKG', 'A'],
            ['PERCIA PRIYADHARSHINI', 'LKG', 'B'],
            ['BAGHAMITHA NAGAR', 'LKG', 'C'],
            ['SOWNDARYA', 'UKG', 'A'],
            ['DHARANI', 'UKG', 'B'],
            ['KAVIYA', 'UKG', 'C'],
        ];

        $kgSubjects = ['Literacy Skills', 'Numeracy Skills', 'General Awareness', 'Tamil', 'Hindi'];
        foreach ($kgAllotments as [$tName, $cls, $sec]) {
            foreach ($kgSubjects as $subj) {
                $assign($tName, $subj, $cls, $sec);
            }
            // Set incharge class teacher
            [$c, $s] = $getClassAndSection($cls, $sec);
            if ($s && isset($teacherMap[$tName])) {
                $s->update(['class_teacher_id' => $teacherMap[$tName]->user_id]);
            }
        }
        $log[] = "Allocated Kindergarten teachers (Pre-KG, LKG, UKG).";

        // ════════════════════════════════════════════════════════════
        // 9. PRIMARY ALLOTMENTS (Grades 1 to 5)
        // ════════════════════════════════════════════════════════════
        $primaryMatrix = [
            // Subject => [Class, Section, Teacher]
            'English' => [
                ['I', 'A', 'YASODHA'], ['I', 'B', 'YASODHA'], ['I', 'C', 'BALAHARINI'],
                ['II', 'A', 'YASODHA'], ['II', 'B', 'HARI PRIYA'],
                ['III', 'A', 'HARI PRIYA'], ['III', 'B', 'HARI PRIYA'], ['III', 'C', 'HARI PRIYA'],
                ['IV', 'A', 'RITHIKA SHREE'], ['IV', 'B', 'RITHIKA SHREE'],
                ['V', 'A', 'RITHIKA SHREE'], ['V', 'B', 'RITHIKA SHREE'],
            ],
            'Tamil' => [
                ['I', 'A', 'MANJU'], ['I', 'B', 'MANJU'], ['I', 'C', 'MANJU'],
                ['II', 'A', 'LAVANYA'], ['II', 'B', 'LAVANYA'],
                ['III', 'A', 'LAVANYA'], ['III', 'B', 'LAVANYA'], ['III', 'C', 'KAVITHA'],
                ['IV', 'A', 'KAVITHA'], ['IV', 'B', 'KAVITHA'],
                ['V', 'A', 'KAVITHA'], ['V', 'B', 'KAVITHA'],
            ],
            'Mathematics' => [
                ['I', 'A', 'SNEHA'], ['I', 'B', 'SNEHA'], ['I', 'C', 'SNEHA'],
                ['II', 'A', 'SNEHA'], ['II', 'B', 'MOHANA SUNDARI'],
                ['III', 'A', 'MOHANA SUNDARI'], ['III', 'B', 'MOHANA SUNDARI'], ['III', 'C', 'MOHANA SUNDARI'],
                ['IV', 'A', 'PRIYANKA'], ['IV', 'B', 'PRIYANKA'],
                ['V', 'A', 'PRIYANKA'], ['V', 'B', 'PRIYANKA'],
            ],
            'EVS / Science' => [
                ['I', 'A', 'RASIGA PRIYA'], ['I', 'B', 'RASIGA PRIYA'], ['I', 'C', 'RASIGA PRIYA'],
                ['II', 'A', 'RASIGA PRIYA'], ['II', 'B', 'BALAHARINI'],
                ['III', 'A', 'GEETHA BHARATHI'], ['III', 'B', 'GEETHA BHARATHI'], ['III', 'C', 'BALAHARINI'],
                ['IV', 'A', 'SHOBIKA'], ['IV', 'B', 'SHOBIKA'],
                ['V', 'A', 'SHOBIKA'], ['V', 'B', 'SHOBIKA'],
            ],
            'Social Science' => [
                ['IV', 'A', 'GEETHA BHARATHI'], ['IV', 'B', 'GEETHA BHARATHI'],
                ['V', 'A', 'HARI PRIYA'], ['V', 'B', 'RAJESWARI'],
            ],
            'Hindi' => [
                ['I', 'A', 'SARANYA'], ['I', 'B', 'SARANYA'], ['I', 'C', 'SARANYA'],
                ['II', 'A', 'SARANYA'], ['II', 'B', 'SARANYA'],
                ['III', 'A', 'SARANYA'], ['III', 'B', 'SARANYA'], ['III', 'C', 'SARANYA'],
                ['IV', 'A', 'SARANYA'], ['IV', 'B', 'SARANYA'],
                ['V', 'A', 'SARANYA'], ['V', 'B', 'SARANYA'],
            ],
            'Computer Science' => [
                ['I', 'A', 'VALARMATHI'], ['I', 'B', 'VALARMATHI'], ['I', 'C', 'VALARMATHI'],
                ['II', 'A', 'VALARMATHI'], ['II', 'B', 'VALARMATHI'],
                ['III', 'A', 'VALARMATHI'], ['III', 'B', 'VALARMATHI'], ['III', 'C', 'VALARMATHI'],
                ['IV', 'A', 'VALARMATHI'], ['IV', 'B', 'VALARMATHI'],
                ['V', 'A', 'VALARMATHI'], ['V', 'B', 'VALARMATHI'],
            ],
            'General Knowledge' => [
                ['I', 'A', 'RASIGA PRIYA'], ['I', 'B', 'SNEHA'], ['I', 'C', 'BALAHARINI'],
                ['II', 'A', 'YASODHA'], ['II', 'B', 'SARANYA'],
                ['III', 'A', 'HARI PRIYA'], ['III', 'B', 'MOHANA SUNDARI'], ['III', 'C', 'MOHANA SUNDARI'],
                ['IV', 'A', 'GEETHA BHARATHI'], ['IV', 'B', 'VALARMATHI'],
                ['V', 'A', 'PRIYANKA'], ['V', 'B', 'RITHIKA SHREE'],
            ],
        ];

        foreach ($primaryMatrix as $subj => $allocList) {
            foreach ($allocList as [$cls, $sec, $tName]) {
                $assign($tName, $subj, $cls, $sec);
            }
        }
        $log[] = "Allocated Primary teachers (Grades I to V).";

        // ════════════════════════════════════════════════════════════
        // 10. MIDDLE, SECONDARY & SR. SECONDARY ALLOTMENTS (Grades 6 to 11)
        // ════════════════════════════════════════════════════════════
        $secondaryMatrix = [
            'Tamil' => [
                ['VI', 'A', 'MAHALAXMI'], ['VI', 'B', 'MAHALAXMI'], ['VI', 'C', 'MAHALAXMI'],
                ['VII', 'A', 'MAHALAXMI'], ['VII', 'B', 'MAHALAXMI'],
                ['VIII', null, 'VIMAL'],
                ['IX', 'A', 'VIMAL'], ['IX', 'B', 'VIMAL'],
                ['X', null, 'VIMAL'],
            ],
            'English' => [
                ['VI', 'A', 'VASUGHI'], ['VI', 'B', 'VASUGHI'], ['VI', 'C', 'VASUGHI'],
                ['VII', 'A', 'VASUGHI'], ['VII', 'B', 'VASUGHI'],
                ['VIII', null, 'SUBHASHINI'],
                ['IX', 'A', 'LOGACHITHRA'], ['IX', 'B', 'LOGACHITHRA'],
                ['X', null, 'LOGACHITHRA'],
                ['XI', 'A', 'LOGACHITHRA'], ['XI', 'B', 'LOGACHITHRA'], ['XI', 'C', 'LOGACHITHRA'],
            ],
            'Mathematics' => [
                ['VI', 'A', 'DEEPIKA'], ['VI', 'B', 'DEEPIKA'], ['VI', 'C', 'DEEPIKA'],
                ['VII', 'A', 'DEEPIKA'], ['VII', 'B', 'DEEPIKA'],
                ['VIII', null, 'BINDHYA'],
                ['IX', 'A', 'NAZREEN SULTHANA'], ['IX', 'B', 'NAZREEN SULTHANA'],
                ['X', null, 'NAZREEN SULTHANA'],
                ['XI', 'A', 'NAZREEN SULTHANA'], ['XI', 'B', 'NAZREEN SULTHANA'],
            ],
            'Science' => [
                ['VI', 'A', 'BINDHYA'], ['VI', 'B', 'BINDHYA'], ['VI', 'C', 'BINDHYA'],
                ['VII', 'A', 'BINDHYA'], ['VII', 'B', 'CHITHRA'],
            ],
            'Social Science' => [
                ['VI', 'A', 'DIVYADHARSHINI'], ['VI', 'B', 'DIVYADHARSHINI'], ['VI', 'C', 'DIVYADHARSHINI'],
                ['VII', 'A', 'DIVYADHARSHINI'], ['VII', 'B', 'DIVYADHARSHINI'],
                ['VIII', null, 'ROSHINI'],
                ['IX', 'A', 'VIGNESH'], ['IX', 'B', 'ROSHINI'],
                ['X', null, 'VIGNESH'],
            ],
            'Hindi' => [
                ['VI', 'A', 'RATHI'], ['VI', 'B', 'RATHI'], ['VI', 'C', 'RATHI'],
                ['VII', 'A', 'RATHI'], ['VII', 'B', 'RATHI'],
                ['VIII', null, 'RATHI'],
                ['IX', 'A', 'RATHI'], ['IX', 'B', 'RATHI'],
            ],
            'Computer Science / IT' => [
                ['VI', 'A', 'GOBIKA'], ['VI', 'B', 'GOBIKA'], ['VI', 'C', 'GOBIKA'],
                ['VII', 'A', 'GOBIKA'], ['VII', 'B', 'GOBIKA'],
                ['VIII', null, 'GOBIKA'],
                ['IX', 'A', 'AMMULAKSHMI'], ['IX', 'B', 'AMMULAKSHMI'],
                ['X', null, 'AMMULAKSHMI'],
                ['XI', 'A', 'AMMULAKSHMI'], ['XI', 'B', 'AMMULAKSHMI'], ['XI', 'C', 'AMMULAKSHMI'],
            ],
            'General Knowledge' => [
                ['VI', 'A', 'ELAKKIYA'], ['VI', 'B', 'ELAKKIYA'], ['VI', 'C', 'ELAKKIYA'],
                ['VII', 'A', 'ELAKKIYA'], ['VII', 'B', 'ELAKKIYA'],
                ['VIII', null, 'ELAKKIYA'],
            ],
            'Physics' => [
                ['VIII', null, 'CHITHRA'],
                ['IX', 'A', 'CHITHRA'], ['IX', 'B', 'CHITHRA'],
                ['X', null, 'CHITHRA'],
                ['XI', 'A', 'CHITHRA'], ['XI', 'B', 'CHITHRA'],
            ],
            'Chemistry' => [
                ['VIII', null, 'ELAKKIYA'],
                ['IX', 'A', 'ELAKKIYA'], ['IX', 'B', 'ELAKKIYA'],
                ['X', null, 'ELAKKIYA'],
                ['XI', 'A', 'ELAKKIYA'], ['XI', 'B', 'ELAKKIYA'],
            ],
            'Biology' => [
                ['VIII', null, 'SAMIR SAHU'],
                ['IX', 'A', 'SAMIR SAHU'], ['IX', 'B', 'SAMIR SAHU'],
                ['X', null, 'SAMIR SAHU'],
                ['XI', 'A', 'SAMIR SAHU'], ['XI', 'B', 'SAMIR SAHU'],
            ],
            'Entrepreneurship' => [
                ['XI', 'C', 'ROSHINI'],
            ],
            'Economics' => [
                ['XI', 'C', 'ROSHINI'],
            ],
            'Accountancy' => [
                ['XI', 'C', 'VIGNESH'],
            ],
            'Physical Education' => [
                ['VI', 'A', 'VADIVEL'], ['VI', 'B', 'VADIVEL'], ['VI', 'C', 'VADIVEL'],
                ['VII', 'A', 'VADIVEL'], ['VII', 'B', 'VADIVEL'],
                ['VIII', null, 'VADIVEL'],
                ['IX', 'A', 'VADIVEL'], ['IX', 'B', 'VADIVEL'],
                ['X', null, 'VADIVEL'],
                ['XI', 'A', 'VADIVEL'], ['XI', 'B', 'VADIVEL'], ['XI', 'C', 'VADIVEL'],
            ],
            'Library' => [
                ['VI', 'A', 'PRIYANKA'], ['VI', 'B', 'PRIYANKA'], ['VI', 'C', 'PRIYANKA'],
                ['VII', 'A', 'PRIYANKA'], ['VII', 'B', 'PRIYANKA'],
                ['VIII', null, 'PRIYANKA'],
            ],
        ];

        foreach ($secondaryMatrix as $subj => $allocList) {
            foreach ($allocList as [$cls, $sec, $tName]) {
                $assign($tName, $subj, $cls, $sec);
            }
        }
        $log[] = "Allocated Secondary & Senior Secondary teachers (Grades VI to XI).";

        $finalCount = Employee::where('employee_type', 'teaching')->where('is_active', true)->count();
        $totalAllocations = TeacherSubjectAllocation::count();

        $log[] = "Complete! Total active teaching staff: {$finalCount}. Total distinct subject allocations: {$totalAllocations}.";

        \Illuminate\Support\Facades\Cache::flush();

        return [
            'success' => true,
            'teaching_staff_count' => $finalCount,
            'allocations_count' => $totalAllocations,
            'log' => $log,
        ];
    }
}

