@extends('layouts.app')
@section('title','Receipt — ' . $payment->receipt_number)

@push('styles')
<style>
  @media print {
    .max-w-xl {
      max-width: 100% !important;
      width: 100% !important;
      margin: 0 !important;
      padding: 0 !important;
    }
    #receipt-print {
      padding: 0 !important;
      border: none !important;
      box-shadow: none !important;
      border-radius: 0 !important;
    }
  }
</style>
@endpush
@section('content')
<div class="max-w-xl mx-auto space-y-6">

  {{-- Print Toolbar --}}
  <x-print.controls title="Fee Payment Receipt" defaultSize="a4" defaultOrientation="portrait" :backUrl="$payment->student ? route('students.show', $payment->student->id) : route('fees.index')" />

  <div class="no-print flex items-center justify-between">
    <div class="flex items-center gap-3">
      <a href="{{ $payment->student ? route('students.show', $payment->student->id) : route('fees.index') }}" 
         class="btn-icon" 
         title="Back to Student Profile">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      </a>
      <h1 class="page-title">Fee Receipt</h1>
    </div>
    <div class="flex gap-2">
      <a href="{{ route('fees.student-statement', $payment->student_id) }}" class="btn btn-secondary btn-sm flex items-center gap-1.5">
        <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <span>Fee Statement</span>
      </a>
      <a href="{{ route('fees.receipt.duplicate', $payment->id) }}" class="btn btn-secondary btn-sm" target="_blank">Duplicate PDF</a>
      <button onclick="window.print()" class="btn btn-primary btn-sm cursor-pointer">Print</button>
    </div>
  </div>

  @if(session('success'))
    <div class="no-print p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3 shadow-xs">
      <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      <span class="font-semibold">{{ session('success') }}</span>
    </div>
  @endif

  <div class="card p-8 relative overflow-hidden" id="receipt-print">
    {{-- Watermark --}}
    <x-print.watermark />

    {{-- Centralized School Letterhead --}}
    <x-print.header 
      title="FEE PAYMENT RECEIPT" 
      subtitle="Official Student Fee Acknowledgment Receipt" 
      :academicYear="now()->year . '-' . (now()->year + 1)" 
    />

    <div class="flex justify-between items-center text-sm mb-4 border-b border-slate-200 pb-3">
      <span class="text-xs uppercase font-bold text-slate-500">Receipt No: <strong class="font-mono text-indigo-600 text-sm">{{ $payment->receipt_number }}</strong></span>
      <span class="text-xs text-slate-500">Date: <strong class="text-slate-800">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</strong></span>
    </div>

    <table class="w-full text-sm border-collapse">
      @php
        $feeHeadTitle = $payment->feeHead?->name ?? $payment->term_name ?? ($payment->term_number ? 'Term ' . $payment->term_number : 'Fee Payment');
        $isSplit = $payment->payment_mode === 'split' || ($payment->splits && $payment->splits->count() > 0);
        $rows = [
          ['Student Name', $payment->student?->full_name],
          ['Admission No.', $payment->student?->admission_no ?? $payment->student?->admission_number],
          ['Class & Section', $payment->student?->currentEnrollment?->class?->name ?? ($payment->student?->currentEnrollment?->schoolClass?->name ?? '—')],
          ['Fee Particulars', $feeHeadTitle],
          ['Payment Date', \Carbon\Carbon::parse($payment->payment_date)->format('d M Y')],
          ['Payment Mode', $isSplit ? 'Split Payment' : ucfirst($payment->payment_mode)],
        ];
        if (!$isSplit && $payment->transaction_id) {
          $rows[] = ['Transaction ID / UTR', $payment->transaction_id];
        }
        if (!$isSplit && $payment->cheque_number) {
          $rows[] = ['Cheque No.', $payment->cheque_number . ($payment->cheque_bank ? ' (' . $payment->cheque_bank . ')' : '')];
        }
        $rows[] = ['Gross Amount', '₹' . number_format($payment->amount, 2)];
        if ($payment->discount > 0) {
          $rows[] = ['Discount', '- ₹' . number_format($payment->discount, 2)];
        }
        $rows[] = ['Total Paid', '₹' . number_format($payment->total_paid, 2)];
        if ((float)($payment->amount_adjusted ?? 0) > 0) {
          $rows[] = ['Amount Adjusted', '₹' . number_format($payment->amount_adjusted, 2)];
        }
        if ((float)($payment->excess_amount ?? 0) > 0) {
          $rows[] = ['Excess Amount Received', '₹' . number_format($payment->excess_amount, 2) . ' (Credited to Advance Wallet)'];
        }
        if ($payment->remarks) {
          $rows[] = ['Remarks / Note', $payment->remarks];
        }
        $rows[] = ['Received By', $payment->collectedBy?->name ?? 'Accounts Office'];
      @endphp
      @foreach($rows as [$l, $v])
        <tr class="border-b border-slate-100">
          <td class="py-2.5 text-slate-500 pr-4 w-2/5 font-medium text-xs uppercase">{{ $l }}</td>
          <td class="py-2.5 font-bold text-slate-800 {{ $l === 'Total Paid' ? 'text-emerald-700 font-black font-mono text-base' : '' }}">{{ $v ?? '—' }}</td>
        </tr>
      @endforeach
    </table>

    {{-- Split Payment Breakdown --}}
    @if($isSplit && $payment->splits && $payment->splits->count() > 0)
      <div class="mt-5 p-4 rounded-xl bg-slate-50 border border-slate-200">
        <p class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-2.5">Split Payment Breakdown</p>
        <table class="w-full text-xs">
          <thead>
            <tr class="border-b border-slate-200 text-slate-500 font-bold uppercase text-[10px]">
              <th class="py-1.5 text-left">Method</th>
              <th class="py-1.5 text-left">Reference / Notes</th>
              <th class="py-1.5 text-right">Amount</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-medium">
            @foreach($payment->splits as $sp)
              <tr>
                <td class="py-2 font-bold text-slate-800">{{ ucfirst(str_replace('_', ' ', $sp->payment_mode)) }}</td>
                <td class="py-2 text-slate-500 font-mono">{{ $sp->transaction_id ?? $sp->cheque_number ?? '—' }}</td>
                <td class="py-2 text-right font-bold font-mono text-slate-900">₹{{ number_format($sp->amount, 2) }}</td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr class="border-t border-slate-200 font-bold text-slate-900">
              <td class="pt-2" colspan="2">Total Split Amount</td>
              <td class="pt-2 text-right font-mono font-black text-emerald-700">₹{{ number_format($payment->total_paid, 2) }}</td>
            </tr>
          </tfoot>
        </table>
      </div>
    @endif

    {{-- Signatures Section --}}
    <div class="mt-8">
      <x-print.signatures mode="financial" />
    </div>

    {{-- Footer --}}
    <x-print.footer />
  </div>
</div>

@if($payment->student_id)
<script>
  // Signal fee update to Student Profile
  try {
    localStorage.setItem('student_fee_updated_{{ $payment->student_id }}', Date.now().toString());
  } catch(e) {}

  // If user clicks browser back button from this receipt, return directly to Student Profile
  if (window.history && window.history.pushState) {
    window.history.pushState(null, '', window.location.href);
    window.addEventListener('popstate', function() {
      window.location.href = "{{ route('students.show', $payment->student_id) }}";
    });
  }
</script>
@endif
@endsection
