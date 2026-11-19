@extends('layouts.app')
@section('title', 'Library Management System')

@section('content')
<div class="space-y-6">

  {{-- Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="page-title text-2xl font-bold text-slate-900">School Library Management System</h1>
      <p class="page-subtitle text-slate-500 text-sm mt-0.5">
        Enterprise Central Catalog · {{ $kpis['total_books'] ?? $stats['total_books'] }} Titles · {{ $kpis['total_copies'] ?? $stats['total_copies'] }} Physical Copies · {{ $kpis['active_borrowers'] ?? 0 }} Active Patrons
      </p>
    </div>
    <div class="flex gap-2 flex-wrap">
      <a href="{{ route('library.books.create') }}" class="btn btn-secondary btn-sm flex items-center gap-1.5">
        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Book
      </a>
      <a href="{{ route('library.procurement.grn.create') }}" class="btn btn-secondary btn-sm flex items-center gap-1.5">
        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Receive GRN
      </a>
      <a href="{{ route('library.issue') }}" class="btn btn-primary btn-sm flex items-center gap-1.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
        Issue / Return
      </a>
    </div>
  </div>

  {{-- Sub-Navigation --}}
  @include('library.partials.nav')

  {{-- Overdue Alert Banner --}}
  @if(($kpis['overdue_copies'] ?? $stats['overdue']) > 0)
  <div class="rounded-xl border border-red-200 bg-red-50/90 p-4 shadow-sm flex items-start justify-between gap-4">
    <div class="flex items-start gap-3">
      <div class="w-9 h-9 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0 text-red-600">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
      </div>
      <div>
        <h4 class="font-semibold text-red-900 text-sm">
          {{ $kpis['overdue_copies'] ?? $stats['overdue'] }} Overdue Book{{ ($kpis['overdue_copies'] ?? $stats['overdue']) > 1 ? 's' : '' }} Pending Return
        </h4>
        <p class="text-xs text-red-700 mt-0.5">
          Outstanding Fine Amount: <strong class="font-semibold">₹{{ number_format($kpis['pending_fines'] ?? $totalFinesPending, 2) }}</strong>. Immediate notice required.
        </p>
        @if(isset($overdueIssues) && $overdueIssues->count())
        <div class="mt-2.5 flex flex-wrap gap-2">
          @foreach($overdueIssues->take(4) as $oi)
          <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-white border border-red-200 text-xs text-red-800">
            <strong>{{ $oi->borrowerName() }}</strong>: {{ \Illuminate\Support\Str::limit($oi->book?->title, 22) }}
            <span class="text-red-500 text-[10px]">(Due {{ $oi->due_date?->format('d M') }})</span>
          </span>
          @endforeach
        </div>
        @endif
      </div>
    </div>
    <div class="flex items-center gap-2 flex-shrink-0">
      <form action="{{ route('library.send-overdue-reminders') }}" method="POST" onsubmit="return confirm('Send automated email alerts to overdue patrons?');">
        @csrf
        <button type="submit" class="btn btn-sm btn-secondary text-xs">Notify Borrowers</button>
      </form>
      <a href="{{ route('library.overdue-report') }}" class="btn btn-sm bg-red-600 hover:bg-red-700 text-white text-xs">View Overdue Register</a>
    </div>
  </div>
  @endif

  {{-- 21 Enterprise KPI Grid --}}
  <div class="space-y-3">
    <div class="flex items-center justify-between">
      <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Circulation & Inventory Metrics</h3>
      <span class="text-xs text-slate-400">Real-time synchronized with PostgreSQL</span>
    </div>

    {{-- Main Circulation Row --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
      <a href="{{ route('library.books') }}" class="card p-4 hover:shadow-md transition border-l-4 border-l-indigo-500">
        <p class="text-xs font-medium text-slate-500">Total Titles</p>
        <p class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($kpis['total_books'] ?? 0) }}</p>
        <p class="text-[11px] text-slate-400 mt-0.5">{{ number_format($kpis['total_copies'] ?? 0) }} physical copies</p>
      </a>

      <a href="{{ route('library.books', ['available' => 1]) }}" class="card p-4 hover:shadow-md transition border-l-4 border-l-emerald-500">
        <p class="text-xs font-medium text-slate-500">Available Copies</p>
        <p class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($kpis['available_copies'] ?? 0) }}</p>
        <p class="text-[11px] text-slate-400 mt-0.5">Ready for loan</p>
      </a>

      <a href="{{ route('library.currently-issued') }}" class="card p-4 hover:shadow-md transition border-l-4 border-l-blue-500">
        <p class="text-xs font-medium text-slate-500">Issued Books</p>
        <p class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($kpis['issued_copies'] ?? 0) }}</p>
        <p class="text-[11px] text-slate-400 mt-0.5">In patron possession</p>
      </a>

      <a href="{{ route('library.reservations') }}" class="card p-4 hover:shadow-md transition border-l-4 border-l-amber-500">
        <p class="text-xs font-medium text-slate-500">Reserved Copies</p>
        <p class="text-2xl font-bold text-amber-600 mt-1">{{ number_format($kpis['reserved_copies'] ?? 0) }}</p>
        <p class="text-[11px] text-slate-400 mt-0.5">Waiting list queue</p>
      </a>

      <a href="{{ route('library.overdue-report') }}" class="card p-4 hover:shadow-md transition border-l-4 border-l-red-500">
        <p class="text-xs font-medium text-slate-500">Overdue Books</p>
        <p class="text-2xl font-bold {{ ($kpis['overdue_copies'] ?? 0) > 0 ? 'text-red-600' : 'text-slate-800' }} mt-1">{{ number_format($kpis['overdue_copies'] ?? 0) }}</p>
        <p class="text-[11px] text-slate-400 mt-0.5">Past due date</p>
      </a>

      <a href="{{ route('library.wastage.index') }}" class="card p-4 hover:shadow-md transition border-l-4 border-l-rose-500">
        <p class="text-xs font-medium text-slate-500">Damaged / Lost</p>
        <p class="text-2xl font-bold text-rose-600 mt-1">{{ number_format(($kpis['damaged_copies'] ?? 0) + ($kpis['lost_copies'] ?? 0)) }}</p>
        <p class="text-[11px] text-slate-400 mt-0.5">{{ $kpis['damaged_copies'] ?? 0 }} damaged · {{ $kpis['lost_copies'] ?? 0 }} lost</p>
      </a>
    </div>

    {{-- Member & Daily Operations Row --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
      <a href="{{ route('library.members') }}" class="card p-4 hover:shadow-md transition">
        <p class="text-xs font-medium text-slate-500">Students Registered</p>
        <p class="text-xl font-bold text-slate-800 mt-1">{{ number_format($kpis['total_students'] ?? 0) }}</p>
        <p class="text-[11px] text-slate-400 mt-0.5">Active school patrons</p>
      </a>

      <a href="{{ route('library.members') }}" class="card p-4 hover:shadow-md transition">
        <p class="text-xs font-medium text-slate-500">Staff Registered</p>
        <p class="text-xl font-bold text-slate-800 mt-1">{{ number_format($kpis['total_staff'] ?? 0) }}</p>
        <p class="text-[11px] text-slate-400 mt-0.5">Teaching & non-teaching</p>
      </a>

      <div class="card p-4">
        <p class="text-xs font-medium text-slate-500">Active Borrowers</p>
        <p class="text-xl font-bold text-indigo-600 mt-1">{{ number_format($kpis['active_borrowers'] ?? 0) }}</p>
        <p class="text-[11px] text-slate-400 mt-0.5">Patrons holding books</p>
      </div>

      <div class="card p-4">
        <p class="text-xs font-medium text-slate-500">Today's Issues</p>
        <p class="text-xl font-bold text-slate-800 mt-1">{{ number_format($kpis['today_issued'] ?? 0) }}</p>
        <p class="text-[11px] text-emerald-600 mt-0.5">Today circulation</p>
      </div>

      <div class="card p-4">
        <p class="text-xs font-medium text-slate-500">Today's Returns</p>
        <p class="text-xl font-bold text-slate-800 mt-1">{{ number_format($kpis['today_returned'] ?? 0) }}</p>
        <p class="text-[11px] text-blue-600 mt-0.5">Returned to desk</p>
      </div>

      <a href="{{ route('library.wastage.index') }}" class="card p-4 hover:shadow-md transition">
        <p class="text-xs font-medium text-slate-500">Written-off Copies</p>
        <p class="text-xl font-bold text-slate-600 mt-1">{{ number_format($kpis['written_off_copies'] ?? 0) }}</p>
        <p class="text-[11px] text-slate-400 mt-0.5">Archived / Scrapped</p>
      </a>
    </div>

    {{-- Financial & Ancillary Row --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5">
      <a href="{{ route('library.fine-report') }}" class="card p-4 hover:shadow-md transition bg-gradient-to-br from-white to-red-50/30">
        <p class="text-xs font-medium text-slate-500">Pending Fines</p>
        <p class="text-xl font-bold text-red-600 mt-1">₹{{ number_format($kpis['pending_fines'] ?? 0, 2) }}</p>
        <p class="text-[11px] text-slate-400 mt-0.5">Unpaid overdue & damage</p>
      </a>

      <a href="{{ route('library.fine-report') }}" class="card p-4 hover:shadow-md transition bg-gradient-to-br from-white to-emerald-50/30">
        <p class="text-xs font-medium text-slate-500">Fines Collected</p>
        <p class="text-xl font-bold text-emerald-600 mt-1">₹{{ number_format($kpis['fine_collected'] ?? 0, 2) }}</p>
        <p class="text-[11px] text-slate-400 mt-0.5">Total revenue receipted</p>
      </a>

      <a href="{{ route('library.procurement.index') }}" class="card p-4 hover:shadow-md transition">
        <p class="text-xs font-medium text-slate-500">Monthly Purchases</p>
        <p class="text-xl font-bold text-slate-800 mt-1">{{ number_format($kpis['monthly_book_purchases'] ?? 0) }} copies</p>
        <p class="text-[11px] text-slate-400 mt-0.5">GRN accessioned this month</p>
      </a>

      <a href="{{ route('library.periodicals.index') }}" class="card p-4 hover:shadow-md transition">
        <p class="text-xs font-medium text-slate-500">Periodicals & Papers</p>
        <p class="text-xl font-bold text-slate-800 mt-1">{{ ($kpis['magazine_subscriptions'] ?? 0) + ($kpis['newspaper_subscriptions'] ?? 0) }}</p>
        <p class="text-[11px] text-slate-400 mt-0.5">{{ $kpis['newspaper_subscriptions'] ?? 0 }} papers · {{ $kpis['magazine_subscriptions'] ?? 0 }} magazines</p>
      </a>

      <a href="{{ route('library.assets.index') }}" class="card p-4 hover:shadow-md transition">
        <p class="text-xs font-medium text-slate-500">Library Assets</p>
        <p class="text-xl font-bold text-slate-800 mt-1">{{ number_format($kpis['library_assets_count'] ?? 0) }}</p>
        <p class="text-[11px] text-slate-400 mt-0.5">Hardware & Furnishings</p>
      </a>
    </div>
  </div>

  {{-- Interactive Panels: Trends & Categories --}}
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Borrowing Trends Bar Chart --}}
    <div class="lg:col-span-2 card p-5 flex flex-col justify-between">
      <div class="flex items-center justify-between mb-4">
        <div>
          <h3 class="font-bold text-slate-900 text-sm">Monthly Borrowing Trends</h3>
          <p class="text-xs text-slate-500">Issue and return circulation volumes (Last 6 Months)</p>
        </div>
        <div class="flex items-center gap-3 text-xs">
          <span class="inline-flex items-center gap-1.5 text-slate-600"><span class="w-3 h-3 rounded-sm bg-primary-600 inline-block"></span> Issued</span>
          <span class="inline-flex items-center gap-1.5 text-slate-600"><span class="w-3 h-3 rounded-sm bg-emerald-500 inline-block"></span> Returned</span>
        </div>
      </div>

      <div class="h-56 flex items-end gap-4 pt-6 border-b border-slate-100">
        @if(isset($trends['labels']) && count($trends['labels']))
          @php
            $maxVal = max(1, max(max($trends['issued']), max($trends['returned'])));
          @endphp
          @foreach($trends['labels'] as $idx => $label)
          <div class="flex-1 flex flex-col items-center h-full justify-end group">
            <div class="w-full flex items-end justify-center gap-1.5 h-44">
              {{-- Issued Bar --}}
              <div class="w-5 bg-primary-500 rounded-t transition-all group-hover:bg-primary-600 relative" 
                   style="height: {{ max(4, round(($trends['issued'][$idx] / $maxVal) * 100)) }}%;">
                <span class="opacity-0 group-hover:opacity-100 absolute -top-6 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-[10px] py-0.5 px-1.5 rounded transition whitespace-nowrap">
                  {{ $trends['issued'][$idx] }} issued
                </span>
              </div>
              {{-- Returned Bar --}}
              <div class="w-5 bg-emerald-400 rounded-t transition-all group-hover:bg-emerald-500 relative" 
                   style="height: {{ max(4, round(($trends['returned'][$idx] / $maxVal) * 100)) }}%;">
                <span class="opacity-0 group-hover:opacity-100 absolute -top-6 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-[10px] py-0.5 px-1.5 rounded transition whitespace-nowrap">
                  {{ $trends['returned'][$idx] }} returned
                </span>
              </div>
            </div>
            <span class="text-[11px] text-slate-500 mt-2 font-medium truncate w-full text-center">{{ $label }}</span>
          </div>
          @endforeach
        @else
          <div class="w-full text-center text-slate-400 py-12 text-sm">No circulation history recorded yet.</div>
        @endif
      </div>
    </div>

    {{-- Category Distribution Breakdown --}}
    <div class="card p-5">
      <div class="flex items-center justify-between mb-3">
        <h3 class="font-bold text-slate-900 text-sm">Category Availability</h3>
        <a href="{{ route('library.books') }}" class="text-xs text-primary-600 hover:underline">View All</a>
      </div>

      <div class="space-y-3 mt-4">
        @if(isset($categories) && count($categories))
          @foreach(collect($categories)->take(6) as $cat)
          <div>
            <div class="flex justify-between text-xs font-medium text-slate-700 mb-1">
              <span>{{ $cat->name }}</span>
              <span class="text-slate-500">{{ $cat->available_count }} / {{ $cat->copy_count }} avail</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
              @php
                $pct = $cat->copy_count > 0 ? round(($cat->available_count / $cat->copy_count) * 100) : 0;
              @endphp
              <div class="bg-primary-600 h-1.5 rounded-full" style="width: {{ $pct }}%;"></div>
            </div>
          </div>
          @endforeach
        @else
          <div class="text-center text-slate-400 py-8 text-sm">No categorized books found.</div>
        @endif
      </div>
    </div>
  </div>

  {{-- Leaderboard & Recent Activity --}}
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Most Borrowed Books --}}
    <div class="card p-5">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-slate-900 text-sm">Most Borrowed Books</h3>
        <a href="{{ route('library.most-borrowed') }}" class="text-xs text-primary-600 hover:underline">Full List</a>
      </div>
      <div class="divide-y divide-slate-100">
        @forelse($topBooks ?? [] as $tb)
        <div class="py-2.5 flex items-center justify-between gap-3 text-xs">
          <div class="min-w-0">
            <p class="font-semibold text-slate-800 truncate">{{ $tb->title }}</p>
            <p class="text-slate-400 text-[11px] truncate">{{ $tb->author ?: 'Unknown Author' }}</p>
          </div>
          <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 font-semibold rounded-full text-[11px] flex-shrink-0">
            {{ $tb->issues_count }} issues
          </span>
        </div>
        @empty
        <p class="text-slate-400 text-center py-6 text-xs">No issue trends recorded yet.</p>
        @endforelse
      </div>
    </div>

    {{-- Most Active Borrowers --}}
    <div class="card p-5">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-slate-900 text-sm">Top Active Patrons</h3>
        <a href="{{ route('library.members') }}" class="text-xs text-primary-600 hover:underline">All Members</a>
      </div>
      <div class="divide-y divide-slate-100">
        @forelse($activeBorrowers['students'] ?? [] as $st)
        <div class="py-2.5 flex items-center justify-between gap-3 text-xs">
          <div class="min-w-0">
            <p class="font-semibold text-slate-800 truncate">{{ $st->first_name }} {{ $st->last_name }}</p>
            <p class="text-slate-400 text-[11px]">Student · {{ $st->admission_number }}</p>
          </div>
          <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 font-semibold rounded-full text-[11px] flex-shrink-0">
            {{ $st->book_issues_count }} loans
          </span>
        </div>
        @empty
        <p class="text-slate-400 text-center py-6 text-xs">No active borrowers yet.</p>
        @endforelse
      </div>
    </div>

    {{-- Recent Activities Audit Log --}}
    <div class="card p-5">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-slate-900 text-sm">Recent Activity Log</h3>
        <span class="text-[11px] text-slate-400">Live Audit Trail</span>
      </div>
      <div class="space-y-3">
        @forelse($recentLogs ?? [] as $log)
        <div class="flex items-start gap-2.5 text-xs">
          <div class="w-1.5 h-1.5 rounded-full bg-primary-500 mt-1.5 flex-shrink-0"></div>
          <div class="flex-1 min-w-0">
            <p class="text-slate-700 truncate font-medium">{{ $log->remarks ?: $log->action }}</p>
            <p class="text-[10px] text-slate-400">{{ $log->created_at?->diffForHumans() }} · by {{ $log->user?->name ?: 'System' }}</p>
          </div>
        </div>
        @empty
        <p class="text-slate-400 text-center py-6 text-xs">No activity logged yet.</p>
        @endforelse
      </div>
    </div>
  </div>

</div>
@endsection
