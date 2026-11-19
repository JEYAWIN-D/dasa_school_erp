@extends('layouts.app')
@section('title', 'Physical Stock Verification Sessions')

@section('content')
<div class="space-y-6">

  {{-- Header --}}
  <div class="flex items-center justify-between flex-wrap gap-4">
    <div>
      <h1 class="page-title text-2xl font-bold text-slate-900">Stock Verification & Shelf Audit</h1>
      <p class="page-subtitle text-slate-500 text-sm mt-0.5">Physical Inventory Audit, Discrepancy Reconciliation & Loss Audit</p>
    </div>
  </div>

  {{-- Sub-Navigation --}}
  @include('library.partials.nav')

  @if(session('success'))
    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">
      {{ session('success') }}
    </div>
  @endif

  {{-- Start Session Card --}}
  <div class="card p-5 border-l-4 border-l-primary-600" x-data="{ openSession: false }">
    <div class="flex items-center justify-between">
      <div>
        <h3 class="font-bold text-slate-900 text-sm">Initiate Physical Stock Audit Session</h3>
        <p class="text-xs text-slate-500">Take an inventory snapshot and begin live barcode scanning across library racks.</p>
      </div>
      <button type="button" @click="openSession = !openSession" class="btn btn-primary btn-sm text-xs" x-text="openSession ? 'Close' : '+ Start Audit Session'"></button>
    </div>

    <form x-show="openSession" x-transition action="{{ route('library.stock-verification.start') }}" method="POST" class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-3 gap-3.5 items-end">
      @csrf
      <div class="sm:col-span-2">
        <label class="block text-xs font-semibold text-slate-700 mb-1">Audit Session Title *</label>
        <input type="text" name="title" value="Annual Library Stock Verification {{ date('Y') }}" class="input w-full text-xs" required>
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Academic Year</label>
        <select name="academic_year_id" class="input w-full text-xs">
          @foreach($activeYears as $ay)
          <option value="{{ $ay->id }}">{{ $ay->name ?? $ay->year_name }}</option>
          @endforeach
        </select>
      </div>

      <div class="sm:col-span-3 flex justify-end">
        <button type="submit" class="btn btn-primary btn-sm text-xs font-semibold">
          Begin Session & Open Scanner
        </button>
      </div>
    </form>
  </div>

  {{-- Audit Sessions History --}}
  <div class="card overflow-hidden">
    <div class="p-4 border-b border-slate-100 flex items-center justify-between">
      <h3 class="text-sm font-bold text-slate-900">Audit Sessions History</h3>
      <span class="text-xs text-slate-500">{{ $sessions->total() }} Total Sessions</span>
    </div>

    <div class="table-wrap">
      <table class="w-full text-left text-sm">
        <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase text-slate-600">
          <tr>
            <th class="py-3 px-4">Session Code</th>
            <th class="py-3 px-4">Session Title</th>
            <th class="py-3 px-4">Dates</th>
            <th class="py-3 px-4 text-center">Expected</th>
            <th class="py-3 px-4 text-center">Scanned</th>
            <th class="py-3 px-4 text-center">Matched</th>
            <th class="py-3 px-4 text-center">Missing</th>
            <th class="py-3 px-4 text-center">Misplaced</th>
            <th class="py-3 px-4">Status</th>
            <th class="py-3 px-4 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @forelse($sessions as $sess)
          <tr class="hover:bg-slate-50/80 transition text-xs">
            <td class="py-3.5 px-4 font-mono font-bold text-primary-600">{{ $sess->session_code }}</td>
            <td class="py-3.5 px-4 font-semibold text-slate-800">{{ $sess->title }}</td>
            <td class="py-3.5 px-4 text-slate-500 whitespace-nowrap">
              {{ $sess->start_date?->format('d M Y') }}
              @if($sess->end_date) — {{ $sess->end_date->format('d M Y') }} @endif
            </td>
            <td class="py-3.5 px-4 text-center font-bold text-slate-700">{{ $sess->total_expected_copies }}</td>
            <td class="py-3.5 px-4 text-center font-bold text-blue-600">{{ $sess->total_scanned_copies }}</td>
            <td class="py-3.5 px-4 text-center font-bold text-emerald-600">{{ $sess->matched_count }}</td>
            <td class="py-3.5 px-4 text-center font-bold text-red-600">{{ $sess->missing_count }}</td>
            <td class="py-3.5 px-4 text-center font-bold text-amber-600">{{ $sess->misplaced_count }}</td>
            <td class="py-3.5 px-4 whitespace-nowrap">
              <span class="badge {{ $sess->status === 'completed' ? 'badge-green' : 'badge-blue' }} text-xs">
                {{ ucfirst(str_replace('_', ' ', $sess->status)) }}
              </span>
            </td>
            <td class="py-3.5 px-4 text-right whitespace-nowrap">
              <a href="{{ route('library.stock-verification.scan', $sess->id) }}" class="btn btn-secondary btn-xs text-xs">
                {{ $sess->status === 'in_progress' ? 'Resume Scan' : 'View Audit' }}
              </a>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="10" class="text-center py-8 text-slate-400 text-xs">No stock verification sessions conducted yet.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection

