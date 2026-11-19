@extends('layouts.app')
@section('title','Teacher Subject Allocation & Teaching Levels')
@section('content')
<div class="space-y-6">

  {{-- Header --}}
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
        <span>Academics</span>
        <span>/</span>
        <span class="text-indigo-600">Teaching Assignments</span>
      </div>
      <h1 class="page-title">Teacher Subject &amp; Level Allocation</h1>
      <p class="text-sm text-slate-500 mt-1">Allocate teachers to subjects, configure teaching level eligibility, and enforce workload limits.</p>
    </div>
    <div class="flex items-center gap-2">
      <a href="{{ route('academics.allocation.history') }}" class="btn btn-secondary btn-sm">View History →</a>
    </div>
  </div>

  @if(session('success'))
    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-2">
      <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
      <span>{{ session('success') }}</span>
    </div>
  @endif

  @if(session('error'))
    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center gap-2">
      <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      <span>{{ session('error') }}</span>
    </div>
  @endif

  {{-- Teaching Level Overview Badges --}}
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
    @foreach($teachingLevels as $lvl)
      <div class="bg-white rounded-xl border border-slate-200 p-3 shadow-xs flex items-center justify-between">
        <div>
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Level {{ $lvl->id }}</span>
          <p class="text-sm font-bold text-slate-800">{{ $lvl->name }}</p>
          <p class="text-[11px] text-slate-400">{{ $lvl->code === 'kg' ? 'Activity-based' : 'Period-based' }}</p>
        </div>
        <span class="w-2.5 h-2.5 rounded-full {{ $lvl->code === 'kg' ? 'bg-amber-400' : ($lvl->code === 'primary' ? 'bg-emerald-400' : ($lvl->code === 'secondary' ? 'bg-sky-400' : 'bg-purple-400')) }}"></span>
      </div>
    @endforeach
  </div>

  {{-- Class Teacher (Section In-Charge) Allocation --}}
  <div class="card p-5 border border-indigo-200 bg-gradient-to-r from-indigo-50/40 via-white to-blue-50/30 rounded-2xl shadow-xs space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-indigo-100">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center shadow-md shadow-indigo-100">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        </div>
        <div>
          <h2 class="text-base font-bold text-slate-800">Class Teacher (Section In-Charge) Allocation</h2>
          <p class="text-xs text-slate-500 mt-0.5">Allocate official class teachers to standards and sections. Changes immediately reflect on the Classes &amp; Timetables dashboard.</p>
        </div>
      </div>
      <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
        Reflects Live in Classes &amp; Timetable
      </span>
    </div>

    {{-- Quick Allocation Form --}}
    <form method="POST" action="{{ route('academics.class-teacher.save') }}" id="classTeacherForm" class="space-y-3 bg-white p-4 rounded-xl border border-slate-200 shadow-2xs">
      @csrf
      <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
        <div class="md:col-span-5">
          <label class="label font-bold text-slate-700 text-xs">Standard &amp; Section <span class="text-red-500">*</span></label>
          <select name="section_id" id="ct_section_id" class="select text-xs font-semibold" required onchange="checkClassTeacherConflict()">
            <option value="">-- Choose Class &amp; Section --</option>
            @foreach($classesWithSections as $cls)
              <optgroup label="Standard: {{ $cls->name }}">
                @foreach($cls->sections as $sec)
                  <option value="{{ $sec->id }}" data-current-teacher="{{ $sec->class_teacher_id }}">
                    {{ $cls->name }} — Section {{ $sec->name }} (Current: {{ $sec->classTeacher?->name ?? 'Not Assigned' }})
                  </option>
                @endforeach
              </optgroup>
            @endforeach
          </select>
        </div>

        <div class="md:col-span-5">
          <label class="label font-bold text-slate-700 text-xs">Class Teacher <span class="text-red-500">*</span></label>
          <select name="employee_id" id="ct_employee_id" class="select text-xs font-semibold" onchange="checkClassTeacherConflict()">
            <option value="">-- Unassign (Mark as Not Assigned) --</option>
            @foreach($teachers->groupBy('teaching_wing_label') as $wingLabel => $wingTeachers)
              <optgroup label="{{ $wingLabel }}">
                @foreach($wingTeachers as $t)
                  @php
                    $isAssigned = isset($activeClassTeacherMap[$t->id]);
                    $assignedSecId = $isAssigned ? $activeClassTeacherMap[$t->id]['section_id'] : '';
                    $assignedLabel = $isAssigned ? $activeClassTeacherMap[$t->id]['label'] : '';
                  @endphp
                  <option value="{{ $t->id }}"
                          data-assigned-section="{{ $assignedSecId }}"
                          data-assigned-label="{{ $assignedLabel }}"
                          class="{{ $isAssigned ? 'text-amber-800 font-medium' : '' }}">
                    {{ $t->full_name }} ({{ $t->employee_code }}) &bull; {{ $t->department }}
                    @if($isAssigned)
                      [Assigned: {{ $assignedLabel }}]
                    @endif
                  </option>
                @endforeach
              </optgroup>
            @endforeach
          </select>
        </div>

        <div class="md:col-span-2">
          <button type="submit" class="btn btn-primary w-full text-xs font-bold py-2.5 shadow-md shadow-indigo-100 flex items-center justify-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Save Class Teacher
          </button>
        </div>
      </div>

      {{-- Conflict Warning Alert (Shown if teacher is already assigned to another class) --}}
      <div id="ctConflictWarning" class="hidden p-3 rounded-xl bg-amber-50 border border-amber-300 text-amber-900 text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 animate-fadeIn">
        <div class="flex items-center gap-2">
          <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
          <span id="ctConflictText"></span>
        </div>
        <label class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-900 cursor-pointer shrink-0 bg-white px-2.5 py-1 rounded-lg border border-amber-300 shadow-2xs">
          <input type="checkbox" name="transfer_confirmed" value="1" id="transferConfirmedCheck" class="rounded border-amber-300 text-indigo-600 focus:ring-indigo-500">
          <span>Transfer from other class</span>
        </label>
      </div>
    </form>

    <script>
      function checkClassTeacherConflict() {
        const secSelect = document.getElementById('ct_section_id');
        const empSelect = document.getElementById('ct_employee_id');
        const warningBox = document.getElementById('ctConflictWarning');
        const warningText = document.getElementById('ctConflictText');
        const transferCheck = document.getElementById('transferConfirmedCheck');

        if (!secSelect || !empSelect || !warningBox) return;

        const selectedSecId = secSelect.value;
        const selectedEmpOption = empSelect.options[empSelect.selectedIndex];
        const assignedSecId = selectedEmpOption ? selectedEmpOption.getAttribute('data-assigned-section') : '';
        const assignedLabel = selectedEmpOption ? selectedEmpOption.getAttribute('data-assigned-label') : '';

        if (assignedSecId && selectedSecId && assignedSecId !== selectedSecId) {
          warningText.innerHTML = `<strong>Strict 1-to-1 Rule:</strong> This faculty member is already the Class Teacher for <strong>${assignedLabel}</strong>. Checking the box will transfer them to the new class and clear their old assignment.`;
          warningBox.classList.remove('hidden');
        } else {
          warningBox.classList.add('hidden');
          if (transferCheck) transferCheck.checked = false;
        }
      }
    </script>

    {{-- Overview of All Classes & Sections Roster --}}
    <div class="space-y-2 pt-2">
      <div class="flex items-center justify-between">
        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Current Class Teacher Roster by Standard</h4>
        <span class="text-[11px] text-slate-400">Unique 1-to-1 section allocation</span>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 max-h-72 overflow-y-auto p-1">
        @foreach($classesWithSections as $cls)
          <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-2xs space-y-2">
            <div class="flex items-center justify-between pb-1.5 border-b border-slate-100">
              <span class="font-bold text-slate-800 text-xs">{{ $cls->name }}</span>
              <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 font-semibold">{{ count($cls->sections) }} Sec</span>
            </div>
            <div class="space-y-1.5">
              @foreach($cls->sections as $sec)
                <div class="flex items-center justify-between text-[11px]">
                  <span class="font-semibold text-slate-600">Sec {{ $sec->name }}:</span>
                  @php
                    $teacherDisplayName = $sec->classTeacher?->name;
                    if (!$teacherDisplayName && $sec->class_teacher_id) {
                      $emp = \App\Models\Employee::find($sec->class_teacher_id);
                      if ($emp) $teacherDisplayName = $emp->full_name;
                    }
                    if (!$teacherDisplayName) {
                      $asgn = \App\Models\StaffClassTeacherAssignment::where('section_id', $sec->id)->where('is_current', true)->with('employee')->first();
                      if ($asgn?->employee) $teacherDisplayName = $asgn->employee->full_name;
                    }
                  @endphp
                  @if($teacherDisplayName)
                    <span class="font-bold text-indigo-700 bg-indigo-50 border border-indigo-200/60 px-2 py-0.5 rounded-md truncate max-w-[130px]" title="{{ $teacherDisplayName }}">
                      {{ $teacherDisplayName }}
                    </span>
                  @else
                    <span class="text-slate-400 italic bg-slate-50 px-2 py-0.5 rounded-md">Not Assigned</span>
                  @endif
                </div>
              @endforeach
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Assign Form --}}
    <form method="POST" action="{{ route('academics.allocation.save') }}" class="card space-y-4">
      @csrf
      <h3 class="font-semibold text-slate-800 pb-2 border-b border-slate-100 flex items-center justify-between">
        <span>Allocate Subject &amp; Class</span>
        <span class="text-xs text-indigo-600 font-medium">Eligible levels validated</span>
      </h3>

      <div>
        <label class="label">Teacher <span class="text-red-500">*</span></label>
        <select name="employee_id" class="select" required>
          <option value="">Select teacher</option>
          @foreach($teachers->groupBy('teaching_wing_label') as $wingLabel => $wingTeachers)
            <optgroup label="{{ $wingLabel }}">
              @foreach($wingTeachers as $t)
                @php
                  $approvedLevels = $t->eligibleTeachingLevels->pluck('name')->join(', ');
                @endphp
                <option value="{{ $t->id }}">
                  {{ $t->full_name }} ({{ $t->employee_code }}) {{ $approvedLevels ? '— [' . $approvedLevels . ']' : '' }}
                </option>
              @endforeach
            </optgroup>
          @endforeach
        </select>
      </div>

      <div>
        <label class="label">Subject <span class="text-red-500">*</span></label>
        <select name="subject_id" class="select" required>
          <option value="">Select subject</option>
          @foreach($subjects->groupBy('category_group') as $categoryName => $groupedSubjects)
            <optgroup label="── {{ $categoryName }} ──">
              @foreach($groupedSubjects as $s)
                <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->code ?? ucfirst($s->type ?? 'theory') }})</option>
              @endforeach
            </optgroup>
          @endforeach
        </select>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="label">Class <span class="text-red-500">*</span></label>
          <select name="class_id" class="select" required>
            <option value="">Select class</option>
            @foreach($classes as $c)
              <option value="{{ $c->id }}">
                {{ $c->name }} {{ $c->teachingLevel ? '— ' . $c->teachingLevel->name : '' }}
              </option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="label">Section</label>
          <select name="section_id" class="select">
            <option value="">All sections</option>
            @foreach($sections as $s)
              <option value="{{ $s->id }}">{{ $s->name }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-500 space-y-1">
        <p class="font-semibold text-slate-700 flex items-center gap-1.5">
          <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          Academic Validation Rules:
        </p>
        <ul class="list-disc list-inside space-y-0.5 text-[11px] text-slate-500">
          <li>Teachers must be approved for the academic teaching level of the class.</li>
          <li>System enforces maximum weekly periods workload limits if configured.</li>
          <li>Timetable slots automatically pull verified allocations.</li>
        </ul>
      </div>

      <button type="submit" class="btn btn-primary">Save Allocation</button>
    </form>

    {{-- Current Allocations Table --}}
    <div class="card space-y-3">
      <h3 class="font-semibold text-slate-800 pb-2 border-b border-slate-100 flex items-center justify-between">
        <span>Current Active Allocations</span>
        <span class="text-xs font-bold text-slate-400 font-mono">{{ $allocations->total() }} Records</span>
      </h3>

      {{-- Filters --}}
      <form method="GET" class="flex gap-2 flex-wrap">
        <select name="academic_year_id" class="select text-xs py-1.5 w-36" onchange="this.form.submit()">
          <option value="">All Years</option>
          @foreach($academicYears as $y)
            <option value="{{ $y->id }}" @selected($filterYearId==$y->id)>{{ $y->name }}</option>
          @endforeach
        </select>
        <select name="teacher_id" class="select text-xs py-1.5 w-44" onchange="this.form.submit()">
          <option value="">All Teachers</option>
          @foreach($teachers->groupBy('teaching_wing_label') as $wingLabel => $wingTeachers)
            <optgroup label="{{ $wingLabel }}">
              @foreach($wingTeachers as $t)
                <option value="{{ $t->id }}" @selected(request('teacher_id')==$t->id)>{{ $t->full_name }}</option>
              @endforeach
            </optgroup>
          @endforeach
        </select>
        <select name="class_id" class="select text-xs py-1.5 w-32" onchange="this.form.submit()">
          <option value="">All Classes</option>
          @foreach($classes as $c)
            <option value="{{ $c->id }}" @selected(request('class_id')==$c->id)>{{ $c->name }}</option>
          @endforeach
        </select>
      </form>

      <div class="overflow-y-auto max-h-96 divide-y divide-slate-100">
        @forelse($allocations as $a)
          <div class="flex items-start justify-between py-2.5">
            <div>
              <div class="flex items-center gap-1.5">
                <p class="text-sm font-bold text-slate-900">{{ $a->employee?->full_name }}</p>
                @if($a->class?->teachingLevel)
                  <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                    {{ $a->class->teachingLevel->name }}
                  </span>
                @endif
              </div>
              <p class="text-xs text-slate-500 mt-0.5">
                <span class="font-semibold text-slate-700">{{ $a->subject?->name }}</span> &bull; 
                {{ $a->class?->name }} {{ $a->section ? 'Section ' . $a->section->name : '(All Sections)' }}
                @if($a->academicYear)
                  <span class="ml-1 text-indigo-600 font-mono">[{{ $a->academicYear->name }}]</span>
                @endif
              </p>
            </div>
            <form method="POST" action="{{ route('academics.allocation.delete', $a->id) }}" onsubmit="return confirm('Remove this subject allocation?');">
              @csrf @method('DELETE')
              <button type="submit" class="text-red-500 hover:text-red-700 text-xs ml-3 font-semibold shrink-0">Remove</button>
            </form>
          </div>
        @empty
          <p class="text-slate-400 text-sm text-center py-6">No allocations for the selected filters.</p>
        @endforelse
      </div>
      @if($allocations->hasPages())
        <div class="text-xs mt-2">{{ $allocations->links() }}</div>
      @endif
    </div>

  </div>

  {{-- Section 14: Teacher Level Eligibility & Workload Configuration --}}
  <div class="card p-5 border border-slate-200 space-y-4">
    <div>
      <h3 class="text-base font-bold text-slate-900">Teaching Level Eligibility &amp; Workload Configuration (Section 14)</h3>
      <p class="text-xs text-slate-500 mt-0.5">Configure which teaching levels (KG, Primary, Secondary, Higher Secondary) each teacher is eligible to instruct, and set maximum weekly periods.</p>
    </div>

    <form method="POST" action="{{ route('academics.teacher-eligibility.save') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
      @csrf

      <div>
        <label class="label">Teacher <span class="text-red-500">*</span></label>
        <select name="employee_id" class="select" required>
          <option value="">Select teacher to configure</option>
          @foreach($teachers as $t)
            <option value="{{ $t->id }}">
              {{ $t->full_name }} ({{ $t->employee_code }})
            </option>
          @endforeach
        </select>
      </div>

      <div class="md:col-span-2">
        <label class="label">Approved Teaching Levels <span class="text-red-500">*</span></label>
        <div class="grid grid-cols-2 gap-2 mt-1">
          @foreach($teachingLevels as $lvl)
            <label class="flex items-center gap-2 p-2 rounded-lg bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 cursor-pointer hover:bg-slate-100">
              <input type="checkbox" name="teaching_levels[]" value="{{ $lvl->id }}" class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
              <span>{{ $lvl->name }}</span>
            </label>
          @endforeach
        </div>
      </div>

      <div>
        <label class="label">Max Weekly Periods</label>
        <input type="number" name="max_weekly_periods" min="1" max="60" placeholder="e.g. 28 periods" class="input">
      </div>

      <div class="md:col-span-4 flex justify-end">
        <button type="submit" class="btn btn-secondary text-xs">
          Save Teacher Level Eligibility &amp; Workload
        </button>
      </div>
    </form>
  </div>

</div>
@endsection
