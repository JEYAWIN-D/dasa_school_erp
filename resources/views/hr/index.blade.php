@extends('layouts.app')
@section('title', 'HR & Payroll')

@section('content')
<div class="space-y-6">

  {{-- ── 1. Page Header ────────────────────────────────────────── --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
    <div class="flex items-center gap-3.5">
      <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-blue-600 flex items-center justify-center text-white shadow-md shadow-indigo-100 flex-shrink-0">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
      </div>
      <div>
        <h1 class="page-title text-xl font-black text-slate-800" style="font-family:'Plus Jakarta Sans',sans-serif;">HR & Payroll</h1>
        <p class="text-xs font-semibold text-slate-500 mt-0.5">Staff & Attendance Management &bull; {{ \Carbon\Carbon::parse($today ?? now())->format('l, d F Y') }}</p>
      </div>
    </div>

    <div class="flex items-center gap-2 flex-wrap">
      <a href="{{ route('staff-timetable.index') }}" class="btn btn-secondary btn-sm flex items-center gap-1.5 text-xs font-bold text-indigo-700 bg-indigo-50 border-indigo-200 hover:bg-indigo-100 shadow-2xs">
        <svg class="w-4 h-4 text-indigo-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        Staff Timetable
      </a>
      <a href="{{ route('hr.id-card-studio') }}" class="btn btn-secondary btn-sm flex items-center gap-1.5 text-xs font-bold text-rose-900 bg-rose-50 border-rose-200 hover:bg-rose-100 shadow-2xs">
        <svg class="w-4 h-4 text-rose-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
        Staff ID Cards
      </a>
      <a href="{{ route('hr.employees') }}" class="btn btn-secondary btn-sm flex items-center gap-1.5 text-xs font-bold">
        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
        </svg>
        Staff Directory
      </a>
      <a href="{{ route('hr.employees.create') }}" class="btn btn-primary btn-sm flex items-center gap-1.5 text-xs font-bold shadow-xs">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
        </svg>
        Add Staff
      </a>
    </div>
  </div>

  {{-- ── 2. Live Summary Metrics (Executive KPI Overview) ── --}}
  <div class="space-y-3">
    <div class="flex items-center justify-between pb-1 flex-wrap gap-3">
      <div class="flex items-center gap-2">
        <h2 class="text-sm font-bold text-slate-700 tracking-tight flex items-center gap-2" style="font-family:'Plus Jakarta Sans',sans-serif;">
          <span>Key Executive Metrics</span>
          <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200/70">
            Real-time Overview
          </span>
        </h2>
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

      {{-- Card 1: Total Staff --}}
      <div class="card p-4 relative group flex flex-col justify-between hover:shadow-md hover:border-slate-300 transition-all duration-200 min-h-[140px]">
        <div>
          <div class="flex items-start justify-between">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white shadow-xs">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
            </div>
            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200/60">Active Dossiers</span>
          </div>

          <div class="mt-3">
            <p class="stat-number text-slate-800 tracking-tight text-2xl font-black">{{ $totalStaff }}</p>
            <p class="text-xs text-slate-500 mt-0.5 font-medium">Total Registered Staff Members</p>
          </div>
        </div>

        <a href="{{ route('hr.employees') }}" class="mt-3 pt-2 border-t border-slate-100 text-xs text-indigo-600 font-semibold hover:text-indigo-800 inline-flex items-center gap-1 group/link transition-colors">
          <span>View staff directory &rarr;</span>
        </a>
      </div>

      {{-- Card 2: Today's Attendance --}}
      <div class="card p-4 relative group flex flex-col justify-between hover:shadow-md hover:border-slate-300 transition-all duration-200 min-h-[140px]">
        <div>
          <div class="flex items-start justify-between">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white shadow-xs">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
              {{ $totalStaff > 0 ? round(($presentToday / $totalStaff) * 100) : 0 }}% Present
            </span>
          </div>

          <div class="mt-3">
            <p class="stat-number text-emerald-600 tracking-tight text-2xl font-black">
              {{ $presentToday }}<span class="text-xs font-semibold text-slate-400 font-sans ml-1">/ {{ $totalStaff }} Present</span>
            </p>
            <div class="flex items-center gap-2 mt-1 flex-wrap">
              <span class="text-[11px] font-semibold text-rose-600">{{ $absentToday }} Absent</span>
              <span class="text-slate-300">&bull;</span>
              <span class="text-[11px] font-semibold text-amber-600">{{ $onLeaveToday + $onDutyToday }} On Leave/OD</span>
            </div>
          </div>
        </div>

        <a href="{{ route('hr.attendance.mark') }}" class="mt-3 pt-2 border-t border-slate-100 text-xs text-emerald-600 font-semibold hover:text-emerald-800 inline-flex items-center gap-1 group/link transition-colors">
          <span>Manage attendance register &rarr;</span>
        </a>
      </div>

      {{-- Card 3: Staff on Leave / OD Today --}}
      <div class="card p-4 relative group flex flex-col justify-between hover:shadow-md hover:border-slate-300 transition-all duration-200 min-h-[140px]">
        <div>
          <div class="flex items-start justify-between">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center text-white shadow-xs">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200/60">
              Absence &amp; OD
            </span>
          </div>

          <div class="mt-3">
            <p class="stat-number text-slate-800 tracking-tight text-2xl font-black">
              {{ $onLeaveToday + $onDutyToday }}
            </p>
            <div class="flex items-center gap-2 mt-1 flex-wrap">
              <span class="text-[11px] font-semibold text-purple-700">{{ $onLeaveToday }} On Leave</span>
              <span class="text-slate-300">&bull;</span>
              <span class="text-[11px] font-semibold text-indigo-600">{{ $onDutyToday }} On Duty (OD)</span>
            </div>
          </div>
        </div>

        <a href="{{ route('hr.attendance.mark') }}" class="mt-3 pt-2 border-t border-slate-100 text-xs text-purple-600 font-semibold hover:text-purple-800 inline-flex items-center gap-1 group/link transition-colors">
          <span>View daily roster status &rarr;</span>
        </a>
      </div>

      {{-- Card 4: Action Approvals Desk --}}
      @php
        $totalPendingAction = ($pendingStaffApprovals ?? 0) + ($pendingLeaves ?? 0);
      @endphp
      <div class="card p-4 relative group flex flex-col justify-between hover:shadow-md hover:border-slate-300 transition-all duration-200 min-h-[140px]">
        <div>
          <div class="flex items-start justify-between">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white shadow-xs">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
              </svg>
            </div>
            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $totalPendingAction > 0 ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-slate-100 text-slate-600' }}">
              {{ $totalPendingAction > 0 ? $totalPendingAction . ' Pending' : 'Clear' }}
            </span>
          </div>

          <div class="mt-3">
            <p class="stat-number {{ $totalPendingAction > 0 ? 'text-amber-600' : 'text-slate-800' }} tracking-tight text-2xl font-black">{{ $totalPendingAction }}</p>
            <div class="flex items-center gap-2 mt-1 flex-wrap">
              <span class="text-[11px] font-semibold text-slate-500">{{ $pendingLeaves }} Leaves</span>
              <span class="text-slate-300">&bull;</span>
              <span class="text-[11px] font-semibold text-slate-500">{{ $pendingStaffApprovals }} Profiles</span>
            </div>
          </div>
        </div>

        <a href="{{ route('hr.leave-approvals') }}" class="mt-3 pt-2 border-t border-slate-100 text-xs text-amber-600 font-semibold hover:text-amber-800 inline-flex items-center gap-1 group/link transition-colors">
          <span>Review approvals desk &rarr;</span>
        </a>
      </div>

    </div>
  </div>

  {{-- ── 4. Two-Column Row: Today's Attendance by Category & Upcoming Events ── --}}
  <div class="grid grid-cols-1 {{ config('hr_features.events', false) ? 'lg:grid-cols-12' : '' }} gap-6">

    {{-- Column A: Today's Attendance Grouped by Staff Category --}}
    <div class="{{ config('hr_features.events', false) ? 'lg:col-span-7' : 'w-full' }} bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs space-y-4">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <div>
          <h2 class="font-extrabold text-sm text-slate-900">Today's Attendance by Category</h2>
          <p class="text-xs text-slate-400 mt-0.5">Live category-wise attendance distribution for {{ \Carbon\Carbon::parse($today ?? now())->format('d M Y') }}</p>
        </div>
        <a href="{{ route('hr.attendance.mark') }}" class="btn btn-secondary btn-xs font-bold text-indigo-600 hover:text-indigo-700">
          Mark Attendance &rarr;
        </a>
      </div>

      <div class="space-y-3.5 pt-1">
        @forelse($categoriesAttendance as $cat)
          <div class="p-3.5 rounded-xl border border-slate-100 bg-slate-50/50 hover:bg-slate-50 transition-colors">
            <div class="flex items-center justify-between gap-2 mb-2">
              <div class="flex items-center gap-2">
                <span class="font-bold text-xs text-slate-800">{{ $cat['category'] }}</span>
                <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-slate-200 text-slate-700 font-bold">
                  {{ $cat['total'] }} staff
                </span>
              </div>
              <div class="text-right">
                <span class="text-xs font-black font-mono {{ $cat['present'] > 0 ? 'text-emerald-600' : 'text-slate-600' }}">
                  {{ $cat['present'] }} / {{ $cat['total'] }} Present
                </span>
                <span class="text-[11px] font-bold text-slate-400 ml-1.5">({{ $cat['pct'] }}%)</span>
              </div>
            </div>

            {{-- Progress Bar --}}
            <div class="w-full bg-slate-200/80 rounded-full h-2 overflow-hidden flex">
              <div class="h-2 rounded-full bg-emerald-500 transition-all duration-300" style="width: {{ $cat['pct'] }}%"></div>
            </div>

            {{-- Sub Details --}}
            <div class="flex items-center justify-between text-[11px] text-slate-500 mt-2 font-medium">
              <span class="flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                Present: <strong class="text-slate-700">{{ $cat['present'] }}</strong>
              </span>
              <span class="flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-rose-500 inline-block"></span>
                Absent: <strong class="text-slate-700">{{ $cat['absent'] }}</strong>
              </span>
              <span class="flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-amber-500 inline-block"></span>
                On Leave: <strong class="text-slate-700">{{ $cat['on_leave'] }}</strong>
              </span>
              <span class="flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-slate-300 inline-block"></span>
                Not Marked: <strong class="text-slate-600">{{ $cat['not_marked'] }}</strong>
              </span>
            </div>
          </div>
        @empty
          <div class="py-8 text-center text-slate-400 text-xs font-medium">
            No attendance marked for today.
          </div>
        @endforelse
      </div>
    </div>

    {{-- Column B: Upcoming Staff Events (Shown only when events feature is enabled) --}}
    @if(config('hr_features.events', false))
      <div class="lg:col-span-5 bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs space-y-4 flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
              <span class="text-base">🎉</span>
              <h2 class="font-extrabold text-sm text-slate-900">Upcoming Staff Events</h2>
            </div>
            <a href="{{ route('hr.events') }}" class="text-xs text-rose-600 hover:text-rose-700 font-bold hover:underline">
              Manage &rarr;
            </a>
          </div>

          <div class="space-y-2.5 pt-2">
            @forelse($upcomingEvents as $ev)
              <div class="p-3 rounded-xl border border-slate-100 bg-slate-50/50 hover:bg-rose-50/30 transition-colors flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                  <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm font-bold shrink-0 {{ $ev['badge_class'] ?? 'bg-indigo-50 text-indigo-700 border-indigo-200' }}">
                    @if($ev['type'] === 'birthday') 🎂
                    @elseif($ev['type'] === 'wedding') 💍
                    @elseif($ev['type'] === 'wedding_anniversary') 💐
                    @elseif($ev['type'] === 'joining_anniversary') 🏆
                    @else 🎈
                    @endif
                  </div>
                  <div class="min-w-0">
                    <p class="font-bold text-xs text-slate-900 truncate">{{ $ev['staff_name'] }}</p>
                    <p class="text-[11px] text-slate-500 truncate">{{ $ev['title'] }}</p>
                  </div>
                </div>
                <div class="text-right shrink-0">
                  <span class="text-xs font-bold font-mono text-slate-700 block">{{ $ev['date_label'] }}</span>
                  <span class="text-[10px] font-semibold text-slate-400">
                    @if($ev['days_left'] == 0)
                      <span class="text-emerald-600 font-extrabold">Today!</span>
                    @elseif($ev['days_left'] == 1)
                      Tomorrow
                    @else
                      in {{ $ev['days_left'] }} days
                    @endif
                  </span>
                </div>
              </div>
            @empty
              <div class="py-12 text-center text-slate-400 space-y-1">
                <p class="text-2xl">🗓️</p>
                <p class="text-xs font-semibold text-slate-500">No upcoming staff events.</p>
                <p class="text-[11px] text-slate-400">Add birthdays, anniversaries, or celebrations.</p>
                <a href="{{ route('hr.events') }}" class="btn btn-secondary btn-xs mt-2 font-bold inline-block">
                  + Add Event
                </a>
              </div>
            @endforelse
          </div>
        </div>

        {{-- Recent Joiners Footer --}}
        @if($recentHires->count() > 0)
          <div class="border-t border-slate-100 pt-3 mt-4">
            <div class="flex items-center justify-between mb-2">
              <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Recent Joiners (Last 30 Days)</span>
              <a href="{{ route('hr.employees') }}" class="text-[11px] text-indigo-600 hover:underline font-bold">All &rarr;</a>
            </div>
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-1">
              @foreach($recentHires as $hire)
                <div class="flex items-center gap-2 bg-slate-50 border border-slate-100 px-2.5 py-1.5 rounded-xl shrink-0">
                  <div class="w-6 h-6 rounded-lg bg-indigo-100 text-indigo-700 font-bold text-[10px] flex items-center justify-center">
                    {{ strtoupper(substr($hire->first_name, 0, 1)) }}
                  </div>
                  <div class="text-[11px]">
                    <p class="font-bold text-slate-800 leading-tight">{{ $hire->first_name }} {{ $hire->last_name }}</p>
                    <p class="text-[10px] text-slate-400 leading-tight">{{ $hire->category_label }}</p>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        @endif

      </div>
    @endif
  </div>

</div>
@endsection
