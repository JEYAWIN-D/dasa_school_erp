@extends('layouts.app')
@section('title', 'Scan Verification — ' . $session->session_code)

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

  <div class="flex items-center justify-between flex-wrap gap-4">
    <div class="flex items-center gap-3">
      <a href="{{ route('library.stock-verification.index') }}" class="btn btn-secondary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Sessions
      </a>
      <div>
        <h1 class="page-title text-xl font-bold text-slate-900">{{ $session->title }}</h1>
        <p class="text-xs text-slate-500 font-mono mt-0.5">Session Code: {{ $session->session_code }} · Started {{ $session->start_date?->format('d M Y') }}</p>
      </div>
    </div>

    @if($session->status === 'in_progress')
    <form action="{{ route('library.stock-verification.complete', $session->id) }}" method="POST" onsubmit="return confirm('Reconcile and close this stock audit session? Unscanned items will be flagged as missing.');">
      @csrf
      <button type="submit" class="btn btn-primary btn-sm text-xs">
        Complete & Reconcile Discrepancies
      </button>
    </form>
    @endif
  </div>

  @if(session('success'))
    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">
      {{ session('success') }}
    </div>
  @endif

  @if(session('error'))
    <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm">
      {{ session('error') }}
    </div>
  @endif

  {{-- Metrics Cards --}}
  <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
    <div class="card p-3.5 text-center">
      <p class="text-xs text-slate-500">Expected Total</p>
      <p class="text-xl font-bold text-slate-800 mt-1">{{ $session->total_expected_copies }}</p>
    </div>
    <div class="card p-3.5 text-center">
      <p class="text-xs text-slate-500">Scanned So Far</p>
      <p class="text-xl font-bold text-blue-600 mt-1">{{ $session->total_scanned_copies }}</p>
    </div>
    <div class="card p-3.5 text-center">
      <p class="text-xs text-slate-500">Matched Present</p>
      <p class="text-xl font-bold text-emerald-600 mt-1">{{ $session->matched_count }}</p>
    </div>
    <div class="card p-3.5 text-center">
      <p class="text-xs text-slate-500">Misplaced Shelves</p>
      <p class="text-xl font-bold text-amber-600 mt-1">{{ $session->misplaced_count }}</p>
    </div>
    <div class="card p-3.5 text-center">
      <p class="text-xs text-slate-500">Damaged Flagged</p>
      <p class="text-xl font-bold text-rose-600 mt-1">{{ $session->damaged_count }}</p>
    </div>
  </div>

  {{-- Scanner Input Station --}}
  @if($session->status === 'in_progress')
  <div class="card p-5 border-l-4 border-l-primary-600">
    <h3 class="font-bold text-slate-900 text-sm mb-1">Live Shelf Barcode Scanner</h3>
    <p class="text-xs text-slate-500 mb-4">Aim USB / Bluetooth scanner at book copy barcodes to log them continuously.</p>

    <form action="{{ route('library.stock-verification.process-scan', $session->id) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
      @csrf
      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Current Shelf Being Audited</label>
        <select name="shelf_id" class="input w-full text-xs">
          <option value="">Shelf Not Specified</option>
          @foreach($shelves as $sh)
          <option value="{{ $sh->id }}">Rack {{ $sh->rack?->rack_number }} / Shelf {{ $sh->shelf_number }}</option>
          @endforeach
        </select>
      </div>

      <div class="sm:col-span-2 flex items-end gap-2">
        <div class="flex-1">
          <label class="block text-xs font-semibold text-slate-700 mb-1">Scan Barcode / Accession *</label>
          <input type="text" name="barcode" autofocus placeholder="Scan barcode or press enter..." class="input w-full text-sm font-mono border-primary-400" required>
        </div>
        <button type="submit" class="btn btn-primary btn-sm flex-shrink-0 text-xs py-2 px-4">
          Log Scan
        </button>
      </div>
    </form>
  </div>
  @endif

  {{-- Scanned Log Table --}}
  <div class="card overflow-hidden">
    <div class="p-4 border-b border-slate-100 flex items-center justify-between">
      <h3 class="text-sm font-bold text-slate-900">Scanned Items Ledger ({{ $session->items->count() }})</h3>
      <span class="text-xs text-slate-500">Ordered by most recent scan</span>
    </div>

    <div class="table-wrap">
      <table class="w-full text-left text-sm">
        <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase text-slate-600">
          <tr>
            <th class="py-3 px-4">Scan Time</th>
            <th class="py-3 px-4">Accession #</th>
            <th class="py-3 px-4">Barcode</th>
            <th class="py-3 px-4">Book Title</th>
            <th class="py-3 px-4">Expected Shelf</th>
            <th class="py-3 px-4">Audit Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @forelse($session->items->sortByDesc('id') as $it)
          <tr class="hover:bg-slate-50/80 transition text-xs">
            <td class="py-3 px-4 text-slate-500 whitespace-nowrap">{{ $it->scanned_at?->format('H:i:s') }}</td>
            <td class="py-3 px-4 font-mono font-bold text-primary-600">{{ $it->copy?->accession_number }}</td>
            <td class="py-3 px-4 font-mono text-slate-600">{{ $it->copy?->barcode }}</td>
            <td class="py-3 px-4 font-semibold text-slate-800 max-w-xs truncate">{{ $it->copy?->book?->title }}</td>
            <td class="py-3 px-4 text-slate-600">
              {{ $it->expectedShelf ? "Rack {$it->expectedShelf->rack?->rack_number} / Shelf {$it->expectedShelf->shelf_number}" : 'Unassigned' }}
            </td>
            <td class="py-3 px-4 whitespace-nowrap">
              @if($it->verification_status === 'verified_present')
                <span class="badge badge-green text-[10px]">Verified Present</span>
              @elseif($it->verification_status === 'misplaced')
                <span class="badge badge-amber text-[10px]">Misplaced Shelf</span>
              @elseif($it->verification_status === 'missing')
                <span class="badge badge-red text-[10px]">Missing</span>
              @elseif($it->verification_status === 'damaged')
                <span class="badge bg-rose-50 text-rose-700 text-[10px]">Damaged</span>
              @else
                <span class="badge bg-slate-100 text-slate-700 text-[10px]">{{ $it->verification_status }}</span>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6" class="text-center py-8 text-slate-400 text-xs">No items scanned in this session yet.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection

