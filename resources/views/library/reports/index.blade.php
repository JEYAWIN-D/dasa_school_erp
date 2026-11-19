@extends('layouts.app')
@section('title', 'Library Reports & Analytics')

@section('content')
<div class="space-y-6">

  {{-- Header --}}
  <div class="flex items-center justify-between flex-wrap gap-4">
    <div>
      <h1 class="page-title text-2xl font-bold text-slate-900">Reports, Analytics & Data Export</h1>
      <p class="page-subtitle text-slate-500 text-sm mt-0.5">Circulation Trends, Financial Audit & Inventory Data Exports</p>
    </div>
    <div class="flex gap-2 flex-wrap">
      <a href="{{ route('library.reports.export', 'catalog') }}" class="btn btn-secondary btn-sm flex items-center gap-1.5">
        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Export Catalogue (.CSV)
      </a>
      <a href="{{ route('library.reports.export', 'circulation') }}" class="btn btn-secondary btn-sm flex items-center gap-1.5">
        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Export Circulation (.CSV)
      </a>
      <a href="{{ route('library.reports.export', 'fines') }}" class="btn btn-secondary btn-sm flex items-center gap-1.5">
        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Export Fine Ledger (.CSV)
      </a>
    </div>
  </div>

  {{-- Sub-Navigation --}}
  @include('library.partials.nav')

  {{-- Summary KPI Ribbon --}}
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    <div class="card p-4 text-center">
      <p class="text-xs text-slate-500">Total Book Titles</p>
      <p class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($kpis['total_books'] ?? 0) }}</p>
      <p class="text-[11px] text-slate-400 mt-0.5">{{ number_format($kpis['total_copies'] ?? 0) }} copies in library</p>
    </div>

    <div class="card p-4 text-center">
      <p class="text-xs text-slate-500">Total Active Borrowers</p>
      <p class="text-2xl font-bold text-indigo-600 mt-1">{{ number_format($kpis['active_borrowers'] ?? 0) }}</p>
      <p class="text-[11px] text-slate-400 mt-0.5">Students & Employees</p>
    </div>

    <div class="card p-4 text-center">
      <p class="text-xs text-slate-500">Fine Revenue Collected</p>
      <p class="text-2xl font-bold text-emerald-600 mt-1">₹{{ number_format($kpis['fine_collected'] ?? 0, 2) }}</p>
      <p class="text-[11px] text-slate-400 mt-0.5">₹{{ number_format($kpis['pending_fines'] ?? 0, 2) }} outstanding</p>
    </div>

    <div class="card p-4 text-center">
      <p class="text-xs text-slate-500">Periodicals & Hardware</p>
      <p class="text-2xl font-bold text-slate-800 mt-1">{{ ($kpis['magazine_subscriptions'] ?? 0) + ($kpis['newspaper_subscriptions'] ?? 0) + ($kpis['library_assets_count'] ?? 0) }}</p>
      <p class="text-[11px] text-slate-400 mt-0.5">Serials & Facilities</p>
    </div>
  </div>

  {{-- Reports Hub Directory --}}
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
    <a href="{{ route('library.currently-issued') }}" class="card p-5 hover:shadow-md transition space-y-2 border-t-4 border-t-primary-600">
      <div class="flex items-center justify-between">
        <h3 class="font-bold text-slate-900 text-sm">Currently Issued Register</h3>
        <span class="text-xs font-bold text-primary-600">{{ $kpis['issued_copies'] ?? 0 }} items</span>
      </div>
      <p class="text-xs text-slate-500">Detailed list of all books currently checked out with due dates and patron contact details.</p>
    </a>

    <a href="{{ route('library.overdue-report') }}" class="card p-5 hover:shadow-md transition space-y-2 border-t-4 border-t-red-600">
      <div class="flex items-center justify-between">
        <h3 class="font-bold text-slate-900 text-sm">Overdue Return Register</h3>
        <span class="text-xs font-bold text-red-600">{{ $kpis['overdue_copies'] ?? 0 }} items</span>
      </div>
      <p class="text-xs text-slate-500">Overdue books past permissible loan window with fine calculation and email reminder triggers.</p>
    </a>

    <a href="{{ route('library.fine-report') }}" class="card p-5 hover:shadow-md transition space-y-2 border-t-4 border-t-emerald-600">
      <div class="flex items-center justify-between">
        <h3 class="font-bold text-slate-900 text-sm">Fine & Waiver Audit Ledger</h3>
        <span class="text-xs font-bold text-emerald-600">₹{{ number_format($kpis['fine_collected'] ?? 0, 0) }}</span>
      </div>
      <p class="text-xs text-slate-500">Complete financial collection receipts, fee module transfers, and authorized fine waiver records.</p>
    </a>

    <a href="{{ route('library.acquisition-report') }}" class="card p-5 hover:shadow-md transition space-y-2 border-t-4 border-t-blue-600">
      <div class="flex items-center justify-between">
        <h3 class="font-bold text-slate-900 text-sm">Acquisition Register</h3>
        <span class="text-xs font-bold text-blue-600">Acc. Log</span>
      </div>
      <p class="text-xs text-slate-500">Sequential accession register showing procurement source, invoice numbers, and purchase dates.</p>
    </a>

    <a href="{{ route('library.deaccession-register') }}" class="card p-5 hover:shadow-md transition space-y-2 border-t-4 border-t-slate-600">
      <div class="flex items-center justify-between">
        <h3 class="font-bold text-slate-900 text-sm">Deaccession & Disposal Log</h3>
        <span class="text-xs font-bold text-slate-600">{{ $kpis['written_off_copies'] ?? 0 }} items</span>
      </div>
      <p class="text-xs text-slate-500">Historical register of all written-off, scrapped, and recycled library copies with justification notes.</p>
    </a>

    <a href="{{ route('library.most-borrowed') }}" class="card p-5 hover:shadow-md transition space-y-2 border-t-4 border-t-amber-600">
      <div class="flex items-center justify-between">
        <h3 class="font-bold text-slate-900 text-sm">Popularity & Reading Trends</h3>
        <span class="text-xs font-bold text-amber-600">Analytics</span>
      </div>
      <p class="text-xs text-slate-500">Most borrowed titles, high-demand subjects, and class-wise borrowing frequency statistics.</p>
    </a>
  </div>

</div>
@endsection

