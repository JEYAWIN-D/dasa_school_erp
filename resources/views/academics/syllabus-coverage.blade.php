@extends('layouts.app')
@section('title', 'Syllabus Coverage & Completion Report')

@push('head')
<style>
  .coverage-hero {
    background: linear-gradient(135deg, #4f46e5 0%, #6366f1 50%, #8b5cf6 100%);
    border-radius: 1.25rem;
    padding: 1.75rem 2rem;
    color: white;
    box-shadow: 0 10px 30px -5px rgba(79, 70, 229, 0.3);
    position: relative;
    overflow: hidden;
  }
  .coverage-hero::before {
    content: '';
    position: absolute;
    top: -40%;
    right: -10%;
    width: 260px;
    height: 260px;
    background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
    border-radius: 50%;
  }
  .class-pill {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 0.65rem 1rem;
    border-radius: 1rem;
    background: white;
    border: 2px solid #e2e8f0;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    min-width: 90px;
    text-align: center;
  }
  .class-pill:hover {
    border-color: #818cf8;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(99, 102, 241, 0.12);
  }
  .class-pill.active {
    background: linear-gradient(135deg, #4f46e5, #6366f1);
    border-color: #4338ca;
    color: white !important;
    box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);
    transform: translateY(-2px);
  }
  .class-pill.active .class-sub-text { color: rgba(255, 255, 255, 0.8) !important; }
  .class-pill.active .class-pct-badge { background: rgba(255, 255, 255, 0.25); color: white; }

  .term-coverage-card {
    background: white;
    border: 1.5px solid #e2e8f0;
    border-radius: 1rem;
    padding: 1.25rem 1.5rem;
    transition: all 0.2s ease;
  }
  .term-coverage-card:hover {
    border-color: #c7d2fe;
    box-shadow: 0 8px 22px rgba(99, 102, 241, 0.08);
    transform: translateY(-2px);
  }
</style>
@endpush

@section('content')
<div class="space-y-6">

  {{-- ── 1. Top Standard Selector ────────────────────────── --}}
  <div class="card p-4">
    <div class="flex items-center justify-between mb-3">
      <div class="flex items-center gap-2">
        <span class="w-2.5 h-2.5 rounded-full bg-indigo-600 animate-pulse"></span>
        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500">Select Standard for Coverage Report</h2>
      </div>
      @if($selectedClass)
        <a href="{{ route('academics.syllabus', ['class_id' => $selectedClass->id]) }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
          <span>Manage Curriculum</span>
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
      @endif
    </div>

    <div class="flex items-center gap-2.5 overflow-x-auto pb-2 pt-1 no-scrollbar">
      @foreach($classes as $c)
        @php
          $isActive = ($selectedClass && $selectedClass->id === $c->id);
          $pct = $c->progress_pct ?? 0;
          $pctColor = $pct >= 75 ? 'text-emerald-600 bg-emerald-50' : ($pct >= 40 ? 'text-amber-600 bg-amber-50' : 'text-slate-500 bg-slate-100');
        @endphp
        <a href="{{ route('academics.syllabus-coverage', ['class_id' => $c->id]) }}"
           class="class-pill {{ $isActive ? 'active' : '' }} flex-shrink-0">
          <span class="font-extrabold text-sm tracking-tight {{ $isActive ? 'text-white' : 'text-slate-800' }}">
            {{ $c->name }}
          </span>
          <span class="class-sub-text text-[11px] {{ $isActive ? 'text-indigo-100' : 'text-slate-400' }} mt-0.5 font-medium">
            {{ $c->total_chapters }} chaps
          </span>
          <span class="class-pct-badge text-[10px] font-bold px-1.5 py-0.5 rounded-full mt-1.5 {{ $isActive ? '' : $pctColor }}">
            {{ $pct }}%
          </span>
        </a>
      @endforeach
    </div>
  </div>

  @if($selectedClass)
    {{-- ── 2. Hero Section ────────────────────────────────── --}}
    @php
      $grandTotal     = $coverage->sum('total');
      $grandCompleted = $coverage->sum('completed');
      $grandInProgress= $coverage->sum('in_progress');
      $grandPending   = $coverage->sum('pending');
      $overallPct     = $grandTotal > 0 ? round(($grandCompleted / $grandTotal) * 100) : 0;
    @endphp

    <div class="coverage-hero">
      <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div>
          <div class="flex items-center gap-2 mb-1.5">
            <span class="px-2.5 py-0.5 rounded-full bg-white/20 text-white text-xs font-bold backdrop-blur-sm">
              Standard: {{ $selectedClass->name }}
            </span>
            <span class="text-indigo-100 text-xs font-medium">
              {{ $coverage->count() }} Subjects Analyzed
            </span>
          </div>
          <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
            {{ $selectedClass->display_name ?? 'Class ' . $selectedClass->name }} Syllabus Coverage
          </h1>
          <p class="text-indigo-100 text-xs sm:text-sm mt-1 max-w-xl">
            Real-time syllabus completion statistics across Term 1, Term 2, and Term 3
          </p>

          <!-- Overall Progress -->
          <div class="mt-4 max-w-md">
            <div class="flex items-center justify-between text-xs font-bold text-white mb-1.5">
              <span>Overall Completion Progress</span>
              <span>{{ $grandCompleted }} / {{ $grandTotal }} Chapters ({{ $overallPct }}%)</span>
            </div>
            <div class="h-2.5 bg-white/20 rounded-full overflow-hidden flex backdrop-blur-sm p-0.5">
              <div class="bg-emerald-400 h-full rounded-full transition-all" style="width: {{ $overallPct }}%"></div>
              <div class="bg-amber-300 h-full transition-all" style="width: {{ $grandTotal > 0 ? round(($grandInProgress / $grandTotal) * 100) : 0 }}%"></div>
            </div>
          </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
          <a href="{{ route('academics.syllabus', ['class_id' => $selectedClass->id]) }}"
             class="px-4 py-2 rounded-xl bg-white text-indigo-700 text-xs font-extrabold hover:bg-indigo-50 transition shadow-sm flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Edit Syllabus
          </a>

          <a href="{{ route('academics.syllabus.print', ['class_id' => $selectedClass->id]) }}"
             target="_blank"
             class="px-3.5 py-2 rounded-xl bg-white/15 hover:bg-white/25 text-white text-xs font-bold backdrop-blur-sm border border-white/20 transition flex items-center gap-1.5 shadow-sm">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print Sheet
          </a>
        </div>
      </div>
    </div>

    {{-- ── 3. Term-wise Coverage Breakdown Cards ─────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      @php
        $termMeta = [
          'Term 1' => ['label' => 'Term 1', 'subtitle' => 'Jun – Sep (Quarterly)', 'border' => 'border-l-indigo-500', 'badge' => 'text-indigo-700 bg-indigo-50'],
          'Term 2' => ['label' => 'Term 2', 'subtitle' => 'Oct – Dec (Half-Yearly)', 'border' => 'border-l-amber-500', 'badge' => 'text-amber-700 bg-amber-50'],
          'Term 3' => ['label' => 'Term 3', 'subtitle' => 'Jan – Apr (Annual)', 'border' => 'border-l-emerald-500', 'badge' => 'text-emerald-700 bg-emerald-50'],
        ];
      @endphp

      @foreach(['Term 1', 'Term 2', 'Term 3'] as $t)
        @php
          $tStats = $termCoverage[$t] ?? ['total' => 0, 'completed' => 0, 'in_progress' => 0, 'pct' => 0];
          $meta = $termMeta[$t];
        @endphp
        <div class="term-coverage-card border-l-4 {{ $meta['border'] }}">
          <div class="flex items-center justify-between mb-2">
            <div>
              <h4 class="font-extrabold text-sm text-slate-800">{{ $meta['label'] }}</h4>
              <p class="text-[11px] text-slate-400 mt-0.5">{{ $meta['subtitle'] }}</p>
            </div>
            <span class="text-xs font-extrabold px-2.5 py-0.5 rounded-full {{ $meta['badge'] }}">
              {{ $tStats['pct'] }}%
            </span>
          </div>

          <div class="h-2 bg-slate-100 rounded-full overflow-hidden flex mt-3">
            <div class="h-full bg-emerald-500 transition-all" style="width: {{ $tStats['pct'] }}%"></div>
            <div class="h-full bg-amber-400 transition-all" style="width: {{ $tStats['total'] > 0 ? round(($tStats['in_progress'] / $tStats['total']) * 100) : 0 }}%"></div>
          </div>

          <div class="flex justify-between text-[11px] text-slate-500 font-medium mt-2.5">
            <span class="text-emerald-700 font-bold">{{ $tStats['completed'] }} Done</span>
            <span class="text-amber-700 font-bold">{{ $tStats['in_progress'] }} In-Progress</span>
            <span class="text-slate-400 font-semibold">{{ $tStats['pending'] }} Pending</span>
          </div>
        </div>
      @endforeach
    </div>

    {{-- ── 4. Subject-wise Coverage Breakdown Table ──────── --}}
    <div class="card overflow-hidden">
      <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
        <div>
          <h3 class="font-bold text-slate-800 text-sm">Subject-wise Syllabus Coverage Breakdown</h3>
          <p class="text-[11px] text-slate-400 mt-0.5">Click any subject to view and edit its term chapters</p>
        </div>
        <span class="text-xs font-bold text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-lg border border-indigo-100">
          {{ $coverage->count() }} Subjects
        </span>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-white border-b border-slate-100">
            <tr>
              <th class="text-left px-5 py-3 text-slate-500 font-bold text-xs uppercase tracking-wide">Subject</th>
              <th class="text-center px-4 py-3 text-slate-500 font-bold text-xs uppercase tracking-wide">Total Chapters</th>
              <th class="text-center px-4 py-3 text-slate-500 font-bold text-xs uppercase tracking-wide">Completed</th>
              <th class="text-center px-4 py-3 text-slate-500 font-bold text-xs uppercase tracking-wide">In Progress</th>
              <th class="text-center px-4 py-3 text-slate-500 font-bold text-xs uppercase tracking-wide">Pending</th>
              <th class="text-right px-5 py-3 text-slate-500 font-bold text-xs uppercase tracking-wide">Coverage</th>
              <th class="px-5 py-3 text-slate-500 font-bold text-xs uppercase tracking-wide w-48">Progress</th>
              <th class="text-right px-5 py-3 text-slate-500 font-bold text-xs uppercase tracking-wide">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @forelse($coverage as $row)
              @php
                $pct = $row['percentage'];
                $barColor = $pct >= 75 ? 'bg-emerald-500' : ($pct >= 30 ? 'bg-indigo-500' : ($pct > 0 ? 'bg-amber-400' : 'bg-slate-200'));
                $badgeBg = $pct >= 75 ? 'text-emerald-700 bg-emerald-50' : ($pct >= 30 ? 'text-indigo-700 bg-indigo-50' : ($pct > 0 ? 'text-amber-700 bg-amber-50' : 'text-slate-500 bg-slate-100'));
              @endphp
              <tr class="hover:bg-slate-50/80 transition group">
                <td class="px-5 py-3.5">
                  <a href="{{ route('academics.syllabus', ['class_id' => $selectedClass->id, 'subject_id' => $row['subject']?->id]) }}"
                     class="flex items-center gap-2 group-hover:text-indigo-600 font-bold text-slate-800 transition">
                    <span class="w-2.5 h-2.5 rounded-full {{ $barColor }}"></span>
                    <span>{{ $row['subject']?->name }}</span>
                    <span class="text-[10px] uppercase font-bold text-slate-400 bg-slate-100 px-1.5 py-0.2 rounded-md">
                      {{ $row['subject']?->type ?? 'Subject' }}
                    </span>
                  </a>
                </td>
                <td class="px-4 py-3.5 text-center font-bold text-slate-700">{{ $row['total'] }}</td>
                <td class="px-4 py-3.5 text-center font-extrabold text-emerald-600">{{ $row['completed'] }}</td>
                <td class="px-4 py-3.5 text-center font-bold text-amber-500">{{ $row['in_progress'] }}</td>
                <td class="px-4 py-3.5 text-center text-slate-400 font-semibold">{{ $row['pending'] }}</td>
                <td class="px-5 py-3.5 text-right font-extrabold">
                  <span class="px-2 py-0.5 rounded-full text-xs {{ $badgeBg }}">
                    {{ $pct }}%
                  </span>
                </td>
                <td class="px-5 py-3.5">
                  <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full {{ $barColor }} rounded-full transition-all" style="width: {{ $pct }}%"></div>
                  </div>
                </td>
                <td class="px-5 py-3.5 text-right">
                  <a href="{{ route('academics.syllabus', ['class_id' => $selectedClass->id, 'subject_id' => $row['subject']?->id]) }}"
                     class="text-xs font-bold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-2.5 py-1 rounded-lg border border-indigo-100 transition inline-flex items-center gap-1">
                    <span>View</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="text-center py-8 text-slate-400">
                  No syllabus entries found for this standard.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  @else
    <div class="card text-center py-16">
      <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
      <h3 class="text-lg font-bold text-slate-700">Please Select a Standard to View Coverage Report</h3>
      <p class="text-xs text-slate-400 mt-1">Choose from Pre-KG, LKG, UKG, or Class I - XII in the top bar.</p>
    </div>
  @endif

</div>
@endsection
