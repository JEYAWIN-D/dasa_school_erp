@extends('layouts.app')
@section('title', 'Fee Collection Register')
@section('content')
<div class="space-y-6">

  {{-- Interactive Print Controls --}}
  <x-print.controls title="Fee Collection Register" defaultSize="a4" defaultOrientation="landscape" :backUrl="route('dashboard')" />

  {{-- Print Watermark & Centralized Institutional Letterhead --}}
  <div class="print-only">
    <x-print.watermark />
    <x-print.header 
      title="FEE COLLECTION REGISTER" 
      subtitle="Period: {{ \Carbon\Carbon::parse($from)->format('d M Y') }} to {{ \Carbon\Carbon::parse($to)->format('d M Y') }}" 
      :academicYear="now()->year . '-' . (now()->year + 1)" 
    />
  </div>

  <div class="no-print flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Fee Collection Register</h1>
    <div class="flex gap-2">
      <a href="{{ route('dashboard') }}" class="btn-sm btn-secondary">← Dashboard</a>
      <button onclick="window.print()" class="btn-sm btn-primary flex items-center gap-1 cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Print Register
      </button>
    </div>
  </div>

  <form method="GET" class="no-print card flex flex-wrap gap-4 items-end">
    <div>
      <label class="label">From Date</label>
      <input type="date" name="from" value="{{ $from }}" class="input">
    </div>
    <div>
      <label class="label">To Date</label>
      <input type="date" name="to" value="{{ $to }}" class="input">
    </div>
    <button type="submit" class="btn-primary btn-sm">Filter</button>
    <a href="{{ route('reports.fee-collection-register') }}" class="btn-sm btn-secondary">Reset</a>
    <div class="ml-auto text-right">
      <p class="text-xs text-slate-400">Total Collection</p>
      <p class="text-xl font-bold text-emerald-600">₹{{ number_format($totalAmount, 2) }}</p>
    </div>
  </form>

  <div class="table-wrap">
    <table class="w-full text-sm border-collapse">
      <thead>
        <tr class="bg-slate-100 dark:bg-slate-800 text-left">
          <th class="th whitespace-nowrap">Receipt No.</th>
          <th class="th whitespace-nowrap">Date</th>
          <th class="th">Student</th>
          <th class="th whitespace-nowrap">Adm. No.</th>
          <th class="th">Class</th>
          <th class="th">Fee Head</th>
          <th class="th whitespace-nowrap">Mode</th>
          <th class="th text-right whitespace-nowrap">Amount</th>
        </tr>
      </thead>
      <tbody>
        @forelse($payments as $p)
        <tr class="tr hover:bg-slate-50">
          <td class="td font-mono font-semibold text-indigo-600 whitespace-nowrap">{{ $p->receipt_number }}</td>
          <td class="td whitespace-nowrap">{{ \Carbon\Carbon::parse($p->payment_date)->format('d/m/Y') }}</td>
          <td class="td font-medium text-slate-900">{{ $p->first_name }} {{ $p->last_name }}</td>
          <td class="td font-mono text-xs whitespace-nowrap">{{ $p->admission_no ?? $p->admission_number ?? '—' }}</td>
          <td class="td whitespace-nowrap">{{ $p->class_name ?? '—' }}</td>
          <td class="td text-xs">{{ $p->fee_head ?? '—' }}</td>
          <td class="td whitespace-nowrap">
            <span class="badge-slate text-xs capitalize whitespace-nowrap">{{ str_replace('_',' ',$p->payment_mode ?? '') }}</span>
          </td>
          <td class="td text-right font-semibold whitespace-nowrap">₹{{ number_format($p->amount, 2) }}</td>
        </tr>
        @empty
        <tr><td class="td text-slate-400 text-center" colspan="8">No payments found in this date range.</td></tr>
        @endforelse
      </tbody>
      @if($payments->isNotEmpty())
      <tfoot>
        <tr class="bg-slate-50 dark:bg-slate-800/50 font-bold border-t-2 border-slate-300">
          <td colspan="7" class="td text-right uppercase text-xs text-slate-600">Total Collection:</td>
          <td class="td text-right text-emerald-600 text-base whitespace-nowrap">₹{{ number_format($totalAmount, 2) }}</td>
        </tr>
      </tfoot>
      @endif
    </table>
  </div>

  {{-- Print Signatures & Footer --}}
  <div class="print-only mt-8 pt-4">
    <x-print.signatures mode="financial" />
    <x-print.footer />
  </div>

  <div class="no-print mt-4">{{ $payments->withQueryString()->links() }}</div>
</div>
@endsection
