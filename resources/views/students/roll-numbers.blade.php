@extends('layouts.app')

@section('title', 'Roll Number Manager')

@section('content')
@php
  $preloadedRollsMap = [];
  if ($initialPreview && !empty($initialPreview['sections'])) {
      foreach ($initialPreview['sections'] as $sec) {
          foreach ($sec['students'] as $st) {
              $preloadedRollsMap[$st['student_id']] = $st['roll_number'];
          }
      }
  }
@endphp

<div class="space-y-5" x-data="rollNumberManager()">

  {{-- ── Top Navigation & Title ───────────────────────────────────────── --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-1 border-b border-slate-100">
    <div>
      <nav class="flex items-center gap-1.5 text-xs text-slate-400 font-medium mb-1">
        <a href="{{ route('students.index') }}" class="hover:text-slate-700 transition">Students</a>
        <span>/</span>
        <span class="text-slate-600">Roll Number Manager</span>
      </nav>
      <div class="flex items-center gap-2.5">
        <h1 class="page-title text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
          Roll Number Manager &amp; Generator
        </h1>
        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
          Editable / Type directly
        </span>
      </div>
      <p class="page-subtitle text-xs text-slate-500 mt-0.5">
        Preloaded with school roster roll numbers (<span class="font-mono font-bold text-slate-700">PA01, PB01, JRA01, IA01</span>). You can type directly in the boxes below to edit or auto-generate.
      </p>
    </div>

    <div class="flex items-center gap-2 shrink-0 flex-wrap">
      <a href="{{ route('students.index') }}" class="btn btn-secondary btn-sm">
        &larr; Back to Students
      </a>
      <a href="{{ route('students.section-allotment.index', array_filter(['class_id' => $selectedClassId, 'academic_year_id' => $selectedYearId])) }}" class="btn btn-secondary btn-sm">
        Section Allotment
      </a>
      <a href="{{ route('students.house-allotment.index', array_filter(['academic_year_id' => $selectedYearId])) }}" class="btn btn-secondary btn-sm">
        House Allotment
      </a>
      <a href="{{ route('students.roll-numbers.print', array_filter(['class_id' => $selectedClassId, 'section_id' => $selectedSectionId, 'academic_year_id' => $selectedYearId])) }}" target="_blank" class="btn btn-secondary btn-sm flex items-center gap-1 font-semibold">
        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Print Roster
      </a>
      <a href="{{ route('students.roll-numbers.export', array_filter(['class_id' => $selectedClassId, 'section_id' => $selectedSectionId, 'academic_year_id' => $selectedYearId])) }}" class="btn btn-ghost btn-sm text-slate-600 border border-slate-200 hover:bg-slate-50">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Export CSV
      </a>
    </div>
  </div>

  {{-- ── Quick Feature Banner ─────────────────────────────────────────── --}}
  <div class="bg-gradient-to-r from-slate-50 via-indigo-50/30 to-emerald-50/30 border border-slate-200/80 rounded-2xl p-4 text-xs text-slate-600 space-y-2">
    <div class="flex items-center gap-2">
      <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-[#8C2826] text-white text-[10px] font-bold">✎</span>
      <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wide">Live Typing &amp; Preloaded Roll Numbers</h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-0.5 text-[11.5px] leading-relaxed">
      <div class="p-2.5 rounded-xl bg-white border border-slate-200/70 shadow-2xs space-y-1">
        <p class="font-bold text-slate-800 flex items-center gap-1.5">
          <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
          1. Preloaded from School Roster
        </p>
        <p class="text-slate-500">All current roll numbers (e.g. <strong>PA01, PA02, JRA01, IA01</strong>) are preloaded directly in the input boxes.</p>
      </div>
      <div class="p-2.5 rounded-xl bg-white border border-slate-200/70 shadow-2xs space-y-1">
        <p class="font-bold text-slate-800 flex items-center gap-1.5">
          <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
          2. Type to Edit Anytime
        </p>
        <p class="text-slate-500">Click any box to type custom roll numbers. Press <kbd class="px-1 py-0.5 rounded bg-slate-100 border text-[10px] font-mono">Enter</kbd> or click <strong>Save</strong> to persist immediately.</p>
      </div>
      <div class="p-2.5 rounded-xl bg-white border border-slate-200/70 shadow-2xs space-y-1">
        <p class="font-bold text-slate-800 flex items-center gap-1.5">
          <span class="w-2 h-2 rounded-full bg-rose-500"></span>
          3. Section Auto-Fill &amp; Bulk Save
        </p>
        <p class="text-slate-500">Use <strong>Auto-Sequence</strong> to auto-fill (01..N) or <strong>Save All</strong> to update the whole section at once.</p>
      </div>
    </div>
  </div>

  {{-- ── Class & Section Selector Card ────────────────────────────────── --}}
  <div class="card p-4 sm:p-5 space-y-3">
    <form method="GET" action="{{ route('students.roll-numbers.index') }}">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
        {{-- Academic Year --}}
        <div>
          <label class="label text-[11px] text-slate-600 font-semibold mb-1 block">Academic Year</label>
          <select name="academic_year_id" class="select text-xs w-full">
            @foreach($academicYears as $yr)
              <option value="{{ $yr->id }}" @selected($selectedYearId == $yr->id)>
                {{ $yr->name }} {{ $yr->id === $currentYear?->id ? '(Current Year)' : '' }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- Class --}}
        <div>
          <label class="label text-[11px] text-slate-600 font-semibold mb-1 block">Class <span class="text-rose-500">*</span></label>
          <select name="class_id" class="select text-xs w-full" onchange="this.form.submit()">
            @foreach($classes as $c)
              <option value="{{ $c->id }}" @selected($selectedClassId == $c->id)>
                Class {{ $c->name }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- Section --}}
        <div>
          <label class="label text-[11px] text-slate-600 font-semibold mb-1 block">Section</label>
          <select name="section_id" class="select text-xs w-full">
            <option value="">All Sections</option>
            @foreach($sections as $s)
              <option value="{{ $s->id }}" @selected($selectedSectionId == $s->id)>
                Section {{ $s->name }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- Roll Number Style & Submit --}}
        <div class="flex items-center gap-2">
          <div class="flex-1">
            <label class="label text-[11px] text-slate-600 font-semibold mb-1 block">Roll Number Style</label>
            <select name="prefix_style" class="select text-xs w-full" x-model="prefixStyle">
              <option value="roman">Grade Roman / KG (PA01, JRA01, IA01, VIA01)</option>
              <option value="standard">Numeric Format (1A01, 6A01)</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary btn-sm shrink-0 self-end">
            Load Students
          </button>
        </div>
      </div>
    </form>
  </div>

  {{-- ── Preview Results & Action Bar ─────────────────────────────────── --}}
  @if($initialPreview && $initialPreview['total_students'] > 0)
    <div class="space-y-5">

      {{-- Global Overview Bar --}}
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3.5 rounded-2xl bg-white border border-slate-200 shadow-2xs">
        <div class="flex flex-wrap items-center gap-2 text-xs">
          <span class="font-bold text-slate-900 text-sm">Class {{ $initialPreview['class_name'] }}</span>
          <span class="text-slate-300">&bull;</span>
          <span class="text-slate-600 font-semibold">{{ $initialPreview['total_students'] }} Enrolled Students</span>
          <span class="text-slate-300">&bull;</span>
          <span class="text-slate-500">{{ count($initialPreview['sections']) }} Active Section(s)</span>
          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 text-[11px] ml-1">
            ✓ Preloaded Roster Active
          </span>
        </div>

        <div class="flex items-center gap-2">
          <button type="button"
                  @click="confirmModal = true"
                  class="btn btn-secondary btn-sm inline-flex items-center gap-1.5 shrink-0 text-slate-700 hover:text-slate-900">
            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            <span>Batch Re-sequence A–Z</span>
          </button>
        </div>
      </div>

      {{-- Section Tables --}}
      @foreach($initialPreview['sections'] as $sec)
        @php
          $studentIdsInSec = array_column($sec['students'], 'student_id');
        @endphp
        <div class="card overflow-hidden border border-slate-200/90 shadow-2xs space-y-0">
          {{-- Section Card Header with Action Buttons --}}
          <div class="px-4 py-3 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
              <span class="w-3 h-3 rounded-full bg-[#8C2826] shrink-0"></span>
              <div>
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                  <span>Class {{ $initialPreview['class_name'] }} &mdash; Section {{ $sec['section_name'] }}</span>
                  <span class="text-xs font-semibold text-slate-500 font-normal">({{ $sec['student_count'] }} students)</span>
                </h3>
                <span class="text-[11px] font-mono text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-200">
                  Format Prefix: {{ $sec['class_code'] }}{{ $sec['section_code'] }} (e.g. {{ $sec['class_code'] }}{{ $sec['section_code'] }}01, {{ $sec['class_code'] }}{{ $sec['section_code'] }}02..)
                </span>
              </div>
            </div>

            {{-- Quick Section Controls: Auto-fill, Reset, Save All --}}
            <div class="flex items-center gap-2 flex-wrap">
              <button type="button"
                      @click="autoFillSequence(@js($sec['students']), '{{ $sec['class_code'] }}', '{{ $sec['section_code'] }}')"
                      class="btn btn-ghost btn-xs text-indigo-700 hover:bg-indigo-50 border border-indigo-200"
                      title="Pre-fill sequential numbering for all students in this section">
                ⚡ Auto-Sequence ({{ $sec['class_code'] }}{{ $sec['section_code'] }}01..)
              </button>
              <button type="button"
                      @click="resetPreloaded(@js($sec['students']))"
                      class="btn btn-ghost btn-xs text-slate-600 hover:bg-slate-100 border border-slate-200"
                      title="Reset all inputs in this section to the preloaded roll numbers from the original sheet">
                ↺ Reset Preloaded
              </button>
              <button type="button"
                      @click="saveBulk({{ $sec['section_id'] }}, @js($studentIdsInSec))"
                      :disabled="bulkSaving"
                      class="btn btn-primary btn-xs flex items-center gap-1.5 shadow-2xs font-bold">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                <span>Save All Sec {{ $sec['section_name'] }}</span>
              </button>
            </div>
          </div>

          {{-- Students Table --}}
          <div class="table-wrap">
            <table class="w-full text-xs">
              <thead>
                <tr>
                  <th class="th w-12 text-center">#</th>
                  <th class="th">Student Name</th>
                  <th class="th">Permanent Adm. No.</th>
                  <th class="th">Date of Birth</th>
                  <th class="th">Gender</th>
                  <th class="th w-64">Roll Number (Type to Edit)</th>
                  <th class="th text-right">Status</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                @foreach($sec['students'] as $idx => $st)
                  <tr class="tr hover:bg-slate-50/70 transition-colors">
                    <td class="td text-center font-mono font-bold text-slate-400">
                      {{ $idx + 1 }}
                    </td>
                    <td class="td">
                      <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center font-bold text-[10px] text-slate-700 shrink-0 border border-slate-200 overflow-hidden">
                          @if($st['photo'])
                            <img src="{{ asset('storage/' . $st['photo']) }}" class="w-full h-full object-cover">
                          @else
                            {{ strtoupper(substr($st['first_name'] ?: 'S', 0, 1) . substr($st['last_name'] ?: '', 0, 1)) }}
                          @endif
                        </div>
                        <div>
                          <span class="font-bold text-slate-900 text-xs">{{ $st['full_name'] }}</span>
                        </div>
                      </div>
                    </td>
                    <td class="td">
                      <div class="flex items-center gap-1.5">
                        <span class="font-mono text-xs font-semibold text-slate-700">
                          {{ $st['admission_no'] }}
                        </span>
                        <span class="text-[9px] text-emerald-700 bg-emerald-50 border border-emerald-200 px-1 py-0.2 rounded font-medium">
                          Adm
                        </span>
                      </div>
                    </td>
                    <td class="td text-slate-500 whitespace-nowrap">
                      {{ $st['dob'] }}
                    </td>
                    <td class="td text-slate-500">
                      {{ $st['gender'] }}
                    </td>

                    {{-- ── TYPEABLE ROLL NUMBER INPUT ── --}}
                    <td class="td">
                      <div class="flex items-center gap-2">
                        <div class="relative">
                          <input type="text"
                                 x-model="rolls[{{ $st['student_id'] }}]"
                                 @keydown.enter.prevent="saveSingle({{ $st['student_id'] }})"
                                 class="font-mono font-bold text-xs px-2.5 py-1 w-28 uppercase rounded-lg border border-slate-300 focus:border-rose-900 focus:ring-2 focus:ring-rose-900/20 bg-white text-slate-900 shadow-2xs transition"
                                 placeholder="{{ $sec['class_code'] }}{{ $sec['section_code'] }}01">
                        </div>
                        <button type="button"
                                @click="saveSingle({{ $st['student_id'] }})"
                                :disabled="saving[{{ $st['student_id'] }}]"
                                class="px-2.5 py-1 rounded-lg text-[11px] font-bold border transition inline-flex items-center gap-1 cursor-pointer"
                                :class="saved[{{ $st['student_id'] }}] 
                                  ? 'bg-emerald-50 text-emerald-700 border-emerald-300 shadow-2xs font-black' 
                                  : 'bg-slate-100 hover:bg-rose-900 hover:text-white text-slate-700 border-slate-200'">
                          <template x-if="saving[{{ $st['student_id'] }}]">
                            <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                          </template>
                          <template x-if="!saving[{{ $st['student_id'] }}] && saved[{{ $st['student_id'] }}]">
                            <span>✓ Saved</span>
                          </template>
                          <template x-if="!saving[{{ $st['student_id'] }}] && !saved[{{ $st['student_id'] }}]">
                            <span>Save</span>
                          </template>
                        </button>
                      </div>
                    </td>

                    <td class="td text-right">
                      <template x-if="rolls[{{ $st['student_id'] }}] !== initialRolls[{{ $st['student_id'] }}]">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                          Modified
                        </span>
                      </template>
                      <template x-if="rolls[{{ $st['student_id'] }}] === initialRolls[{{ $st['student_id'] }}]">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium text-slate-400 bg-slate-50 border border-slate-200">
                          Saved
                        </span>
                      </template>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      @endforeach

    </div>
  @elseif($initialPreview && $initialPreview['total_students'] === 0)
    <div class="card p-8 text-center text-slate-400 space-y-1.5">
      <p class="text-sm font-bold text-slate-700">No active students found in Class {{ $initialPreview['class_name'] }} for Academic Year {{ $initialPreview['academic_year'] }}.</p>
      <p class="text-xs text-slate-500">Please choose a different class (e.g. Class I or Pre-KG) or academic year above.</p>
    </div>
  @else
    <div class="card p-8 text-center text-slate-400 space-y-1.5">
      <p class="text-sm font-bold text-slate-700">Select a Class and Section above and click "Load Students".</p>
      <p class="text-xs text-slate-500">All student roll numbers will be preloaded for direct editing.</p>
    </div>
  @endif

  {{-- ── Confirmation Modal for Batch Alphabetical Generation ─────────── --}}
  @if($initialPreview && $initialPreview['total_students'] > 0)
  <div x-show="confirmModal"
       x-cloak
       class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-2xs"
       x-transition:enter="transition ease-out duration-150"
       x-transition:enter-start="opacity-0"
       x-transition:enter-end="opacity-100"
       x-transition:leave="transition ease-in duration-100"
       x-transition:leave-start="opacity-100"
       x-transition:leave-end="opacity-0">

    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl border border-slate-200 p-5 sm:p-6 space-y-4"
         @click.away="confirmModal = false">

      <div>
        <h3 class="text-base font-bold text-slate-900">Batch Re-sequence to Alphabetical Order</h3>
        <p class="text-xs text-slate-500 mt-0.5">
          Class {{ $initialPreview['class_name'] }} &bull; {{ $initialPreview['academic_year'] }}
        </p>
      </div>

      <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 text-xs text-slate-600 space-y-2">
        <div class="flex items-center justify-between">
          <span>Total Students:</span>
          <strong class="font-bold text-slate-800">{{ $initialPreview['total_students'] }} students</strong>
        </div>
        <div class="flex items-center justify-between pt-1 border-t border-slate-200/60">
          <span>Permanent Admission Numbers:</span>
          <strong class="font-bold text-emerald-700">100% Protected (Never Modified)</strong>
        </div>
      </div>

      <form method="POST" action="{{ route('students.roll-numbers.generate') }}" class="space-y-3.5">
        @csrf
        <input type="hidden" name="class_id" value="{{ $initialPreview['class_id'] }}">
        <input type="hidden" name="section_id" value="{{ $initialPreview['section_id'] }}">
        <input type="hidden" name="academic_year_id" value="{{ $initialPreview['academic_year_id'] }}">
        <input type="hidden" name="prefix_style" value="{{ $initialPreview['prefix_style'] }}">
        <input type="hidden" name="start_seq" value="{{ $initialPreview['start_seq'] }}">
        <input type="hidden" name="confirm" value="1">

        <div class="flex items-start gap-2 pt-1">
          <input type="checkbox" id="confirm_agree" x-model="confirmAgreed" class="mt-0.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
          <label for="confirm_agree" class="text-xs text-slate-600 select-none">
            I confirm updating all students in this section strictly in alphabetical order.
          </label>
        </div>

        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
          <button type="button" @click="confirmModal = false" class="btn btn-secondary btn-sm">
            Cancel
          </button>
          <button type="submit"
                  :disabled="!confirmAgreed"
                  class="btn btn-primary btn-sm disabled:opacity-50 disabled:cursor-not-allowed">
            Apply Alphabetical Order
          </button>
        </div>
      </form>
    </div>
  </div>
  @endif

  {{-- ── Toast Notification Popper ─────────────────────────────────────── --}}
  <div x-show="toast.show"
       x-cloak
       x-transition:enter="transition ease-out duration-200 transform"
       x-transition:enter-start="opacity-0 translate-y-3"
       x-transition:enter-end="opacity-100 translate-y-0"
       x-transition:leave="transition ease-in duration-150 transform"
       x-transition:leave-start="opacity-100 translate-y-0"
       x-transition:leave-end="opacity-0 translate-y-3"
       class="fixed bottom-5 right-5 z-50 max-w-sm p-4 rounded-xl shadow-xl flex items-center gap-3 text-xs font-semibold text-white"
       :class="toast.type === 'error' ? 'bg-rose-700' : (toast.type === 'info' ? 'bg-indigo-700' : 'bg-emerald-700')">
    <template x-if="toast.type === 'success'">
      <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    </template>
    <template x-if="toast.type === 'error'">
      <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </template>
    <template x-if="toast.type === 'info'">
      <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </template>
    <span x-text="toast.message"></span>
  </div>

</div>

<script>
function rollNumberManager() {
  const initialData = @json($preloadedRollsMap);
  return {
    confirmModal: false,
    confirmAgreed: false,
    selectedClassId: '{{ $selectedClassId }}',
    selectedSectionId: '{{ $selectedSectionId }}',
    selectedYearId: '{{ $selectedYearId }}',
    prefixStyle: '{{ request('prefix_style', 'roman') }}',
    rolls: { ...initialData },
    initialRolls: { ...initialData },
    saving: {},
    saved: {},
    bulkSaving: false,
    toast: { show: false, message: '', type: 'success' },

    showToast(message, type = 'success') {
      this.toast.message = message;
      this.toast.type = type;
      this.toast.show = true;
      setTimeout(() => { this.toast.show = false; }, 3500);
    },

    async saveSingle(studentId) {
      const roll = (this.rolls[studentId] || '').trim().toUpperCase();
      if (!roll) {
        this.showToast('Please enter a roll number', 'error');
        return;
      }
      this.rolls[studentId] = roll;
      this.saving[studentId] = true;
      this.saved[studentId] = false;

      try {
        const resp = await fetch('{{ route('students.roll-numbers.update-single') }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({
            student_id: studentId,
            academic_year_id: this.selectedYearId,
            roll_number: roll
          })
        });
        const data = await resp.json();
        if (resp.ok && data.success) {
          this.saved[studentId] = true;
          this.initialRolls[studentId] = roll;
          this.showToast(`Roll number updated to ${roll}`, 'success');
          setTimeout(() => { this.saved[studentId] = false; }, 3000);
        } else {
          this.showToast(data.message || 'Failed to update roll number', 'error');
        }
      } catch (e) {
        this.showToast('Network error while saving roll number', 'error');
      } finally {
        this.saving[studentId] = false;
      }
    },

    async saveBulk(sectionId, studentIds) {
      const items = studentIds.map(id => ({
        student_id: id,
        roll_number: (this.rolls[id] || '').trim().toUpperCase()
      }));

      const emptyItem = items.find(i => !i.roll_number);
      if (emptyItem) {
        this.showToast('Some students have empty roll numbers. Please fill all before saving.', 'error');
        return;
      }

      this.bulkSaving = true;
      try {
        const resp = await fetch('{{ route('students.roll-numbers.update-bulk') }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({
            academic_year_id: this.selectedYearId,
            items: items
          })
        });
        const data = await resp.json();
        if (resp.ok && data.success) {
          studentIds.forEach(id => {
            this.initialRolls[id] = this.rolls[id];
            this.saved[id] = true;
          });
          this.showToast(data.message || 'All roll numbers updated successfully!', 'success');
          setTimeout(() => {
            studentIds.forEach(id => { this.saved[id] = false; });
          }, 3000);
        } else {
          this.showToast(data.message || 'Failed to save bulk roll numbers', 'error');
        }
      } catch (e) {
        this.showToast('Network error while saving bulk roll numbers', 'error');
      } finally {
        this.bulkSaving = false;
      }
    },

    autoFillSequence(students, classCode, sectionCode) {
      students.forEach((st, idx) => {
        const seqStr = String(idx + 1).padStart(2, '0');
        this.rolls[st.student_id] = `${classCode}${sectionCode}${seqStr}`;
      });
      this.showToast(`Pre-filled sequence ${classCode}${sectionCode}01..${String(students.length).padStart(2, '0')}. Click 'Save All' to apply.`, 'info');
    },

    resetPreloaded(students) {
      students.forEach(st => {
        this.rolls[st.student_id] = this.initialRolls[st.student_id];
      });
      this.showToast('Reset to original preloaded roll numbers.', 'info');
    }
  };
}
</script>
@endsection
