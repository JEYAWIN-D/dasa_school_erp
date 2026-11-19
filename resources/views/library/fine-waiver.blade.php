@extends('layouts.app')
@section('title', 'Library Fine Collection & Waiver')

@section('content')
<div class="space-y-6">
  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
      <h1 class="text-2xl font-bold text-slate-900">Fine Management & Waivers</h1>
      <p class="text-sm text-slate-500">Collect overdue fines directly, transfer to school fee module, or process authorized waivers.</p>
    </div>
  </div>

  @include('library.partials.nav')

  @if(session('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm font-medium">
      {{ session('success') }}
    </div>
  @endif
  @if(session('error'))
    <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-lg text-sm font-medium">
      {{ session('error') }}
    </div>
  @endif

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="card space-y-4">
      <h3 class="font-semibold text-slate-800 pb-2 border-b border-slate-100 flex items-center justify-between">
        <span>Search Overdue Issue / Fine</span>
        <span class="text-xs text-slate-500">By borrower name or admission number</span>
      </h3>
      <form method="GET" class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}" class="input flex-1" placeholder="Student name or admission no...">
        <button type="submit" class="btn btn-secondary btn-sm">Search</button>
      </form>

      @if(isset($issues) && $issues->count())
      <div class="space-y-4">
        @foreach($issues as $issue)
        <div class="border border-slate-200 rounded-xl p-4 bg-white shadow-sm space-y-3">
          <div class="flex items-start justify-between">
            <div>
              <p class="font-bold text-slate-800 text-sm">{{ $issue->book?->title }}</p>
              <p class="text-xs text-slate-500">
                Borrower: <strong>{{ $issue->student ? ($issue->student->first_name . ' ' . $issue->student->last_name) : ($issue->employee->name ?? 'Staff') }}</strong>
                @if($issue->copy)
                  | Copy: <code class="font-mono text-indigo-600">{{ $issue->copy->accession_number }}</code>
                @endif
              </p>
              <p class="text-xs text-slate-400">
                Due: {{ $issue->due_date?->format('d M Y') }} | Overdue: <span class="text-rose-600 font-bold">{{ $issue->days_overdue }} days</span>
              </p>
            </div>
            <div class="text-right">
              <span class="text-xs block text-slate-400">Fine Balance</span>
              <span class="text-rose-600 font-bold text-lg">₹{{ number_format($issue->fine_amount, 2) }}</span>
            </div>
          </div>

          @if($issue->fine_amount > 0 && !$issue->fine_paid && !$issue->fine_waived)
          <div class="pt-3 border-t border-slate-100 grid grid-cols-1 md:grid-cols-2 gap-3">
            <!-- Direct Library Payment Form -->
            <div class="p-3 bg-slate-50 rounded-lg space-y-2 border border-slate-200">
              <p class="text-xs font-bold text-slate-700">1. Direct Library Collection</p>
              <form method="POST" action="{{ route('library.fine.pay', $issue->id) }}" class="space-y-2">
                @csrf
                <div>
                  <label class="text-[11px] text-slate-500 font-medium">Amount to Pay</label>
                  <input type="number" step="0.01" max="{{ $issue->fine_amount }}" name="amount_paid" value="{{ $issue->fine_amount }}" class="input text-xs h-8" required>
                </div>
                <div>
                  <label class="text-[11px] text-slate-500 font-medium">Payment Method</label>
                  <select name="payment_method" class="select text-xs h-8">
                    <option value="cash">Cash Counter</option>
                    <option value="upi">UPI / QR Code</option>
                    <option value="card">Debit / Credit Card</option>
                    <option value="bank_transfer">Bank Transfer</option>
                  </select>
                </div>
                <div>
                  <label class="text-[11px] text-slate-500 font-medium">Ref / Remarks</label>
                  <input type="text" name="reference_number" placeholder="Txn ID (optional)" class="input text-xs h-8">
                </div>
                <button type="submit" class="btn btn-primary btn-xs w-full">Collect & Issue Receipt</button>
              </form>
            </div>

            <!-- Fine Waiver Form -->
            <div class="p-3 bg-amber-50/50 rounded-lg space-y-2 border border-amber-200">
              <p class="text-xs font-bold text-amber-900">2. Authorized Fine Waiver</p>
              <form method="POST" action="{{ route('library.fine.waive', $issue->id) }}" class="space-y-2">
                @csrf
                <div>
                  <label class="text-[11px] text-amber-800 font-medium">Waive Reason (Mandatory)</label>
                  <input type="text" name="waiver_reason" class="input text-xs h-8 border-amber-300" required placeholder="e.g. Medical leave, Principal approved">
                </div>
                <div>
                  <label class="text-[11px] text-amber-800 font-medium">Waive Amount (Max ₹{{ number_format($issue->fine_amount, 2) }})</label>
                  <input type="number" name="waiver_amount" class="input text-xs h-8 border-amber-300" max="{{ $issue->fine_amount }}" step="0.01" value="{{ $issue->fine_amount }}">
                </div>
                <button type="submit" class="btn btn-warning btn-xs w-full">Apply Fine Waiver</button>
              </form>
            </div>
          </div>
          @elseif($issue->fine_paid)
          <div class="p-2 bg-emerald-50 text-emerald-700 text-xs rounded-lg font-medium">
            ✓ Fine fully settled.
          </div>
          @elseif($issue->fine_waived)
          <div class="p-2 bg-slate-100 text-slate-600 text-xs rounded-lg">
            Fine waived. Reason: <em>{{ $issue->waiver_reason }}</em>
          </div>
          @endif
        </div>
        @endforeach
      </div>
      @elseif(request('search'))
      <p class="text-sm text-slate-400 py-6 text-center">No overdue records found matching "{{ request('search') }}".</p>
      @endif
    </div>

    <!-- Recent Waivers Audit Trail -->
    <div class="card space-y-4">
      <h3 class="font-semibold text-slate-800 pb-2 border-b border-slate-100 flex items-center justify-between">
        <span>Recent Fine Waivers Audit Trail</span>
        <span class="text-xs text-slate-500">Authorized Adjustments</span>
      </h3>
      @if(isset($recentWaivers) && count($recentWaivers))
      <div class="divide-y divide-slate-100">
        @foreach($recentWaivers as $w)
        <div class="py-3 flex items-start justify-between">
          <div>
            <p class="text-sm font-semibold text-slate-800">
              {{ $w->student ? ($w->student->first_name . ' ' . $w->student->last_name) : ($w->employee->name ?? 'Staff') }}
            </p>
            <p class="text-xs text-slate-500">{{ $w->book?->title }}</p>
            <p class="text-xs text-slate-400 mt-0.5">Reason: <em>{{ $w->waiver_reason }}</em></p>
          </div>
          <div class="text-right">
            <p class="font-bold text-amber-600">₹{{ number_format($w->waiver_amount, 2) }}</p>
            <p class="text-[11px] text-slate-400">By {{ $w->waivedBy?->name ?? 'Admin' }}</p>
            <p class="text-[10px] text-slate-400">{{ $w->updated_at?->format('d M Y, h:i A') }}</p>
          </div>
        </div>
        @endforeach
      </div>
      @else
      <p class="text-slate-400 text-sm text-center py-8">No fine waiver records found.</p>
      @endif
    </div>
  </div>
</div>
@endsection
