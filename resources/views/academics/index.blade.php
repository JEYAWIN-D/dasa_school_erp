@extends('layouts.app')
@section('title', ($isTeacher ?? false) ? 'My Classes & Attendance — Erode Public School' : 'Academic Operations & Curriculum Hub — Erode Public School')

@section('content')
@if($isTeacher ?? false)
  {{-- ── TEACHER ACADEMIC & ATTENDANCE DASHBOARD (VIEW ONLY OF RESPECTIVE CLASSES) ── --}}
  <div class="space-y-6">

    {{-- Teacher Header --}}
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div class="flex items-center gap-3.5">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#4A1525] to-[#2A0914] flex items-center justify-center text-amber-300 shadow-md shadow-rose-950/20 flex-shrink-0 border border-amber-400/20">
          <svg class="w-6 h-6 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
          </svg>
        </div>
        <div>
          <div class="flex items-center gap-2 flex-wrap">
            <h1 class="page-title text-xl font-black text-slate-900 tracking-tight" style="font-family:'Plus Jakarta Sans',sans-serif;">My Classes &amp; Student Attendance</h1>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-amber-50 text-amber-800 border border-amber-300">
              Teacher Desk
            </span>
          </div>
          <p class="text-xs font-semibold text-slate-500 mt-0.5">
            Welcome back, <span class="text-slate-800 font-bold">{{ auth()->user()->name }}</span> &bull; Academic Year: <span class="text-slate-800 font-bold">{{ $currentYear?->name ?? '2026-2027' }}</span>
          </p>
        </div>
      </div>

      <div class="flex items-center gap-2 flex-wrap">
        <a href="{{ route('attendance.mark') }}" class="btn btn-primary btn-sm flex items-center gap-1.5 text-xs font-bold shadow-xs">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
          <span>Mark Class Attendance</span>
        </a>
        <a href="{{ route('academics.timetable') }}" class="btn btn-secondary btn-sm flex items-center gap-1.5 text-xs font-bold text-slate-700">
          <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          <span>My Timetable</span>
        </a>
      </div>
    </div>

    {{-- Teacher Overview KPIs --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs flex items-center gap-3.5">
        <div class="w-12 h-12 rounded-xl bg-blue-50 border border-blue-200 flex items-center justify-center text-blue-600 flex-shrink-0">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        </div>
        <div>
          <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Allotted Classes</p>
          <p class="text-2xl font-black text-slate-800 mt-0.5">{{ $classes->count() }} <span class="text-xs font-semibold text-slate-400">Standards</span></p>
        </div>
      </div>

      <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs flex items-center gap-3.5">
        <div class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-200 flex items-center justify-center text-indigo-600 flex-shrink-0">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
        </div>
        <div>
          <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Assigned Sections</p>
          <p class="text-2xl font-black text-slate-800 mt-0.5">{{ $mySections->count() }} <span class="text-xs font-semibold text-slate-400">Classrooms</span></p>
        </div>
      </div>

      <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs flex items-center gap-3.5">
        <div class="w-12 h-12 rounded-xl bg-teal-50 border border-teal-200 flex items-center justify-center text-teal-600 flex-shrink-0">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        </div>
        <div>
          <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Enrolled Students</p>
          <p class="text-2xl font-black text-slate-800 mt-0.5">{{ $mySections->sum('student_count') }} <span class="text-xs font-semibold text-slate-400">Students</span></p>
        </div>
      </div>

      <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs flex items-center gap-3.5">
        <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 flex-shrink-0">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
          <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Today's Attendance</p>
          @php $markedCount = $mySections->where('attendance_marked', true)->count(); @endphp
          <p class="text-2xl font-black {{ $markedCount === $mySections->count() && $mySections->count() > 0 ? 'text-emerald-700' : 'text-amber-600' }} mt-0.5">
            {{ $markedCount }}/{{ $mySections->count() }} <span class="text-xs font-semibold text-slate-400">Marked</span>
          </p>
        </div>
      </div>
    </div>

    {{-- Cards Grid: My Allotted Classes & Sections --}}
    <div class="space-y-4">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-base font-extrabold text-slate-900 tracking-tight" style="font-family:'Plus Jakarta Sans',sans-serif;">My Allotted Classrooms &amp; Roll Call</h2>
          <p class="text-xs text-slate-500">Select any section to record daily classroom student attendance or view schedule</p>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($mySections as $sec)
          <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs hover:shadow-md transition-all p-5 flex flex-col justify-between">
            <div>
              <div class="flex items-start justify-between gap-2 mb-3">
                <div>
                  <h3 class="text-lg font-black text-slate-900 tracking-tight">
                    Class {{ $sec->class?->name }} <span class="text-amber-600">&bull;</span> Section {{ $sec->name }}
                  </h3>
                  <p class="text-xs text-slate-400 font-mono mt-0.5">Grade #{{ $sec->class_id }} &bull; Sec #{{ $sec->id }}</p>
                </div>
                @if($sec->is_class_teacher)
                  <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-100 text-amber-900 border border-amber-300">
                    Class Teacher
                  </span>
                @else
                  <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200">
                    Subject Teacher
                  </span>
                @endif
              </div>

              {{-- Attendance Status Pill --}}
              <div class="mb-4">
                @if($sec->attendance_marked)
                  <div class="flex items-center gap-2 p-2.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold">
                    <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Today's Attendance Recorded</span>
                  </div>
                @else
                  <div class="flex items-center gap-2 p-2.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold">
                    <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Today's Attendance Pending</span>
                  </div>
                @endif
              </div>

              <div class="space-y-1.5 text-xs text-slate-600">
                <div class="flex items-center justify-between py-1 border-b border-slate-100">
                  <span class="text-slate-400 font-semibold">Enrolled Students:</span>
                  <span class="font-extrabold text-slate-800">{{ $sec->student_count }} Students</span>
                </div>
                @if(!empty($sec->subjects_taught))
                <div class="flex items-start justify-between py-1 border-b border-slate-100 gap-2">
                  <span class="text-slate-400 font-semibold flex-shrink-0">My Subjects:</span>
                  <span class="font-bold text-slate-800 text-right">{{ implode(', ', $sec->subjects_taught) }}</span>
                </div>
                @endif
              </div>
            </div>

            {{-- Action Buttons --}}
            <div class="pt-4 mt-4 border-t border-slate-100 flex items-center gap-2">
              <a href="{{ route('attendance.mark', ['class_id' => $sec->class_id, 'section_id' => $sec->id]) }}"
                 class="btn btn-primary btn-sm flex-1 flex items-center justify-center gap-1.5 text-xs font-bold shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                <span>{{ $sec->attendance_marked ? 'Update Attendance' : 'Mark Attendance' }}</span>
              </a>
              <a href="{{ route('academics.timetable', ['class_id' => $sec->class_id, 'section_id' => $sec->id]) }}"
                 class="btn btn-secondary btn-sm flex items-center justify-center gap-1 text-xs font-bold text-slate-700"
                 title="View Timetable">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>Timetable</span>
              </a>
            </div>
          </div>
        @empty
          <div class="col-span-full bg-white p-8 rounded-2xl border border-slate-200 text-center">
            <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5z"/></svg>
            <h3 class="text-sm font-bold text-slate-800">No Classrooms Allotted Yet</h3>
            <p class="text-xs text-slate-400 mt-1">Please contact school administration to allocate your teaching standards and sections.</p>
          </div>
        @endforelse
      </div>
    </div>

    {{-- Class Hierarchy Table (Teacher View-Only) --}}
    @if($mySections->count())
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
      <div class="p-5 border-b border-slate-100 flex items-center justify-between">
        <div>
          <h3 class="font-black text-sm text-slate-900 uppercase tracking-wider" style="font-family:'Plus Jakarta Sans',sans-serif;">My Allotted Classes Roster</h3>
          <p class="text-xs text-slate-400 mt-0.5">Quick access to student attendance and period timetables</p>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
          <thead class="bg-slate-50/75 border-b border-slate-100 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">
            <tr>
              <th class="px-5 py-3.5">Standard &amp; Section</th>
              <th class="px-5 py-3.5 text-center">Role</th>
              <th class="px-5 py-3.5 text-center">Enrolled Students</th>
              <th class="px-5 py-3.5 text-center">Today's Attendance</th>
              <th class="px-5 py-3.5 text-right">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-medium text-xs">
            @foreach($mySections as $sec)
              <tr class="hover:bg-slate-50/60 transition-colors">
                <td class="px-5 py-3.5">
                  <span class="font-black text-slate-900 text-sm">Class {{ $sec->class?->name }}</span>
                  <span class="text-slate-400 font-bold ml-1">&mdash; Section {{ $sec->name }}</span>
                </td>
                <td class="px-5 py-3.5 text-center">
                  @if($sec->is_class_teacher)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-900 border border-amber-300">
                      Class Teacher
                    </span>
                  @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                      Subject Teacher
                    </span>
                  @endif
                </td>
                <td class="px-5 py-3.5 text-center font-bold text-slate-800">
                  {{ $sec->student_count }} Students
                </td>
                <td class="px-5 py-3.5 text-center">
                  @if($sec->attendance_marked)
                    <span class="badge-green text-[10px] font-bold px-2 py-0.5 rounded-full">Marked</span>
                  @else
                    <span class="badge-amber text-[10px] font-bold px-2 py-0.5 rounded-full">Pending</span>
                  @endif
                </td>
                <td class="px-5 py-3.5 text-right">
                  <a href="{{ route('attendance.mark', ['class_id' => $sec->class_id, 'section_id' => $sec->id]) }}"
                     class="btn btn-primary btn-xs inline-flex items-center gap-1 font-bold">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <span>Mark Attendance</span>
                  </a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    @endif
  </div>

@else
  {{-- ── ADMIN ACADEMIC OPERATIONS & CURRICULUM HUB ─────────────── --}}
  <div class="space-y-6">

    {{-- ── 1. Page Header (School Theme) ────────────────────────── --}}
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div class="flex items-center gap-3.5">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#4A1525] to-[#2A0914] flex items-center justify-center text-amber-300 shadow-md shadow-rose-950/20 flex-shrink-0 border border-amber-400/20">
          <svg class="w-6 h-6 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
          </svg>
        </div>
        <div>
          <div class="flex items-center gap-2 flex-wrap">
            <h1 class="page-title text-xl font-black text-slate-900 tracking-tight" style="font-family:'Plus Jakarta Sans',sans-serif;">Academic Operations &amp; Curriculum Hub</h1>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-amber-50 text-amber-800 border border-amber-300">
              CBSE Senior Secondary
            </span>
          </div>
          <p class="text-xs font-semibold text-slate-500 mt-0.5">
            Erode Public School &bull; Affiliation No. 1930811 &bull; Academic Year: <span class="text-slate-800 font-bold">{{ $currentYear?->name ?? '2026-2027' }}</span>
          </p>
        </div>
      </div>

      {{-- Header Action Shortcuts --}}
      <div class="flex items-center gap-2 flex-wrap">
        @can('view academics')
        <a href="{{ route('academics.sections.manage') }}" class="btn btn-primary btn-sm flex items-center gap-1.5 text-xs font-bold shadow-xs">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
          <span>Manage Sections</span>
        </a>
        @endcan
        <a href="{{ route('academics.timetable') }}" class="btn btn-secondary btn-sm flex items-center gap-1.5 text-xs font-bold text-slate-700">
          <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          <span>Timetable Hub</span>
        </a>
        <a href="{{ route('academics.terms') }}" class="btn btn-secondary btn-sm flex items-center gap-1.5 text-xs font-bold text-slate-700">
          <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          <span>Terms &amp; Semesters</span>
        </a>
        <a href="{{ route('academics.working-days') }}" class="btn btn-secondary btn-sm flex items-center gap-1.5 text-xs font-bold text-slate-700">
          <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
          <span>Working Days</span>
        </a>
      </div>
    </div>

    {{-- ── 2. Academic Core KPI Metrics ───────────────────────────── --}}
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      {{-- Card 1: Classes --}}
      <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs hover:shadow-md hover:border-slate-300 transition flex items-center gap-3.5">
        <div class="w-12 h-12 rounded-xl bg-blue-50 border border-blue-200 flex items-center justify-center text-blue-600 flex-shrink-0">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        </div>
        <div class="min-w-0">
          <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Standards</p>
          <p class="text-2xl font-black text-slate-800 mt-0.5">{{ $classes->count() }} <span class="text-xs font-semibold text-slate-400">Classes</span></p>
          <a href="{{ route('classes.index') }}" class="text-[11px] font-bold text-blue-600 hover:text-blue-700 inline-flex items-center gap-1 mt-0.5">Manage classes &rarr;</a>
        </div>
      </div>

      {{-- Card 2: Sections --}}
      <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs hover:shadow-md hover:border-slate-300 transition flex items-center gap-3.5">
        <div class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-200 flex items-center justify-center text-indigo-600 flex-shrink-0">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
        </div>
        <div class="min-w-0">
          <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Active Sections</p>
          <p class="text-2xl font-black text-slate-800 mt-0.5">{{ $classes->sum('sections_count') }} <span class="text-xs font-semibold text-slate-400">Classrooms</span></p>
          <a href="{{ route('academics.sections.manage') }}" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-700 inline-flex items-center gap-1 mt-0.5">Manage sections &rarr;</a>
        </div>
      </div>

      {{-- Card 3: Subjects --}}
      <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs hover:shadow-md hover:border-slate-300 transition flex items-center gap-3.5">
        <div class="w-12 h-12 rounded-xl bg-teal-50 border border-teal-200 flex items-center justify-center text-teal-600 flex-shrink-0">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        </div>
        <div class="min-w-0">
          <p class="text-xs font-bold uppercase tracking-wider text-slate-400">CBSE Curriculum</p>
          <p class="text-2xl font-black text-slate-800 mt-0.5">{{ $subjects }} <span class="text-xs font-semibold text-slate-400">Subjects</span></p>
          <span class="text-[11px] font-semibold text-slate-400 mt-0.5 block">Active Curriculum</span>
        </div>
      </div>

      {{-- Card 4: Teaching Faculty --}}
      <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs hover:shadow-md hover:border-slate-300 transition flex items-center gap-3.5">
        <div class="w-12 h-12 rounded-xl bg-purple-50 border border-purple-200 flex items-center justify-center text-purple-600 flex-shrink-0">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <div class="min-w-0">
          <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Faculty Roster</p>
          <p class="text-2xl font-black text-slate-800 mt-0.5">{{ $teachers }} <span class="text-xs font-semibold text-slate-400">Teachers</span></p>
          <a href="{{ route('academics.allocation') }}" class="text-[11px] font-bold text-purple-600 hover:text-purple-700 inline-flex items-center gap-1 mt-0.5">Allocations &rarr;</a>
        </div>
      </div>
    </div>

    {{-- ── 3. Academic Operations & Schedules Hub (Primary Modules) ── --}}
    <div class="space-y-3">
      <div class="flex items-center justify-between pb-1">
        <div>
          <h2 class="text-sm font-extrabold uppercase tracking-wider text-slate-800" style="font-family:'Plus Jakarta Sans',sans-serif;">Daily Teaching &amp; Timetable Hub</h2>
          <p class="text-xs text-slate-400">Class schedules, teacher assignments, student attendance, and evaluation</p>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Module: Class Timetables --}}
        <a href="{{ route('academics.timetable') }}" class="group bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs hover:shadow-md hover:border-indigo-400 transition-all flex flex-col justify-between">
          <div>
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-indigo-500 to-blue-600 text-white flex items-center justify-center shadow-xs mb-3 group-hover:scale-105 transition-transform">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <h3 class="font-bold text-sm text-slate-900 group-hover:text-indigo-600 transition-colors">Class Timetables</h3>
            <p class="text-xs text-slate-500 mt-1 leading-snug">Weekly class schedules, period slots, break times &amp; lab sessions</p>
          </div>
          <span class="text-xs font-bold text-indigo-600 mt-4 inline-flex items-center gap-1 group-hover:translate-x-0.5 transition-transform">
            Open Timetable &rarr;
          </span>
        </a>

        {{-- Module: Teacher Allocation --}}
        <a href="{{ route('academics.allocation') }}" class="group bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs hover:shadow-md hover:border-purple-400 transition-all flex flex-col justify-between">
          <div>
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-purple-500 to-indigo-600 text-white flex items-center justify-center shadow-xs mb-3 group-hover:scale-105 transition-transform">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <h3 class="font-bold text-sm text-slate-900 group-hover:text-purple-600 transition-colors">Teacher Allocation</h3>
            <p class="text-xs text-slate-500 mt-1 leading-snug">Assign subject teachers, class teachers, and teaching workload limits</p>
          </div>
          <span class="text-xs font-bold text-purple-600 mt-4 inline-flex items-center gap-1 group-hover:translate-x-0.5 transition-transform">
            Manage Allocations &rarr;
          </span>
        </a>

        {{-- Module: Student Attendance --}}
        <a href="{{ route('attendance.index') }}" class="group bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs hover:shadow-md hover:border-emerald-400 transition-all flex flex-col justify-between">
          <div>
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center shadow-xs mb-3 group-hover:scale-105 transition-transform">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="font-bold text-sm text-slate-900 group-hover:text-emerald-600 transition-colors">Student Attendance</h3>
            <p class="text-xs text-slate-500 mt-1 leading-snug">Daily classroom roll call, attendance register &amp; monthly percentages</p>
          </div>
          <span class="text-xs font-bold text-emerald-600 mt-4 inline-flex items-center gap-1 group-hover:translate-x-0.5 transition-transform">
            Attendance Portal &rarr;
          </span>
        </a>

        {{-- Module: Examinations & Results --}}
        <a href="{{ route('examinations.index') }}" class="group bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs hover:shadow-md hover:border-rose-400 transition-all flex flex-col justify-between">
          <div>
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-rose-500 to-red-600 text-white flex items-center justify-center shadow-xs mb-3 group-hover:scale-105 transition-transform">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <h3 class="font-bold text-sm text-slate-900 group-hover:text-rose-600 transition-colors">Examinations &amp; Results</h3>
            <p class="text-xs text-slate-500 mt-1 leading-snug">Assessments, marks entry, CBSE grading &amp; report cards</p>
          </div>
          <span class="text-xs font-bold text-rose-600 mt-4 inline-flex items-center gap-1 group-hover:translate-x-0.5 transition-transform">
            Examinations Hub &rarr;
          </span>
        </a>
      </div>
    </div>

    {{-- ── 4. Curriculum Planning & Content Delivery ────────────────── --}}
    <div class="space-y-3">
      <div class="flex items-center justify-between pb-1">
        <div>
          <h2 class="text-sm font-extrabold uppercase tracking-wider text-slate-800" style="font-family:'Plus Jakarta Sans',sans-serif;">Curriculum &amp; Pedagogical Planning</h2>
          <p class="text-xs text-slate-400">Structured lesson plans, pedagogical units, and school announcements</p>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        {{-- Card: Lesson Plans --}}
        <a href="{{ route('academics.lesson-plans') }}" class="group bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs hover:shadow-md hover:border-pink-400 transition-all flex flex-col justify-between">
          <div>
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-pink-500 to-rose-600 text-white flex items-center justify-center shadow-xs mb-3 group-hover:scale-105 transition-transform">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <h3 class="font-bold text-sm text-slate-900 group-hover:text-pink-600 transition-colors">Lesson Plans</h3>
            <p class="text-xs text-slate-500 mt-1 leading-snug">Weekly pedagogical unit plans, teaching aids, and learning outcomes</p>
          </div>
          <span class="text-xs font-bold text-pink-600 mt-4 inline-flex items-center gap-1 group-hover:translate-x-0.5 transition-transform">
            Browse Plans &rarr;
          </span>
        </a>

        {{-- Card: Academic Notices --}}
        <a href="{{ route('academics.notices') }}" class="group bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs hover:shadow-md hover:border-blue-400 transition-all flex flex-col justify-between">
          <div>
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 text-white flex items-center justify-center shadow-xs mb-3 group-hover:scale-105 transition-transform">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </div>
            <div class="flex items-center justify-between gap-1">
              <h3 class="font-bold text-sm text-slate-900 group-hover:text-blue-600 transition-colors">Academic Notices</h3>
              @if($activeNotices > 0)
                <span class="badge-blue text-[10px] font-extrabold px-2 py-0.5 rounded-full">{{ $activeNotices }} Active</span>
              @endif
            </div>
            <p class="text-xs text-slate-500 mt-1 leading-snug">Broadcast circulars, exam notifications, and school announcements</p>
          </div>
          <span class="text-xs font-bold text-blue-600 mt-4 inline-flex items-center gap-1 group-hover:translate-x-0.5 transition-transform">
            View Notices &rarr;
          </span>
        </a>
      </div>
    </div>

    {{-- ── 5. Standard-wise Class & Section Structure ─────────────── --}}
    @if($classes->count())
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
      <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
          <h3 class="font-black text-sm text-slate-900 uppercase tracking-wider" style="font-family:'Plus Jakarta Sans',sans-serif;">Class &amp; Section Hierarchy</h3>
          <p class="text-xs text-slate-400 mt-0.5">Active standards, classroom divisions, and academic jump actions</p>
        </div>
        <div class="flex items-center gap-2">
          <span class="badge-indigo text-xs font-bold px-3 py-1 rounded-full">
            {{ $classes->count() }} Standards Registered
          </span>
          @can('view academics')
          <a href="{{ route('academics.sections.manage') }}"
             class="btn btn-primary btn-xs inline-flex items-center gap-1.5 font-bold shadow-xs">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            <span>Manage Sections</span>
          </a>
          @endcan
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
          <thead class="bg-slate-50/75 border-b border-slate-100 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">
            <tr>
              <th class="px-5 py-3.5">Standard / Grade</th>
              <th class="px-5 py-3.5 text-center">Sections</th>
              <th class="px-5 py-3.5 text-center">Academic Track</th>
              <th class="px-5 py-3.5 text-right">Quick Operations</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-medium">
            @foreach($classes as $cls)
              <tr class="hover:bg-slate-50/60 transition-colors">
                <td class="px-5 py-3.5">
                  <span class="font-extrabold text-slate-900 text-sm">{{ $cls->name }}</span>
                  <span class="text-xs text-slate-400 ml-1.5 font-mono">#{{ $cls->id }}</span>
                </td>
                <td class="px-5 py-3.5 text-center">
                  <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                    {{ $cls->sections_count }} {{ Str::plural('Section', $cls->sections_count) }}
                  </span>
                </td>
                <td class="px-5 py-3.5 text-center">
                  <span class="text-xs font-bold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-md">
                    {{ $cls->numeric_value <= 0 ? 'Foundation (KG)' : ($cls->numeric_value <= 5 ? 'Primary Wing' : ($cls->numeric_value <= 8 ? 'Middle School' : ($cls->numeric_value <= 10 ? 'Secondary' : 'Senior Secondary'))) }}
                  </span>
                </td>
                <td class="px-5 py-3.5 text-right">
                  <div class="inline-flex items-center justify-end gap-2 flex-wrap">
                    <a href="{{ route('attendance.mark', ['class_id' => $cls->id]) }}"
                       class="btn btn-secondary btn-xs inline-flex items-center gap-1 font-bold text-emerald-700 bg-emerald-50 border-emerald-200 hover:bg-emerald-100"
                       title="Mark Class Attendance">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                      <span>Attendance</span>
                    </a>
                    <a href="{{ route('academics.timetable', ['class_id' => $cls->id]) }}"
                       class="btn btn-secondary btn-xs inline-flex items-center gap-1 font-bold text-indigo-700 bg-indigo-50 border-indigo-200 hover:bg-indigo-100"
                       title="View Timetable">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                      <span>Timetable</span>
                    </a>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    @endif
  </div>
@endif
@endsection
