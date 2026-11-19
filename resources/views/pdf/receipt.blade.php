<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Fee Receipt / Tax Invoice</title>
<style>
  @page {
    size: A4 portrait;
    margin: 12mm 15mm 16mm 15mm;
  }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 10px;
    color: #1a1a1a;
    padding: 0;
  }
  .duplicate-badge {
    text-align: center;
    color: #dc2626;
    font-weight: bold;
    font-size: 10px;
    letter-spacing: 2px;
    margin-bottom: 8px;
  }
  .meta-box {
    width: 100%;
    border: 1px solid #cbd5e1;
    background-color: #f8fafc;
    border-radius: 4px;
    margin-bottom: 12px;
  }
  .meta-box td {
    padding: 5px 10px;
    font-size: 9.5px;
    border: none;
  }
  table.details-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 8px;
    margin-bottom: 15px;
  }
  table.details-table tr {
    border-bottom: 1px solid #e2e8f0;
  }
  table.details-table td {
    padding: 6px 8px;
    font-size: 10px;
  }
  table.details-table td:first-child {
    color: #475569;
    width: 40%;
    font-weight: 500;
  }
  table.details-table td:last-child {
    font-weight: 600;
    color: #0f172a;
  }
  .total-row td {
    background: #f1f5f9;
    font-weight: bold !important;
    font-size: 11.5px !important;
    border-top: 2px solid #0f172a !important;
    color: #0f172a !important;
  }
</style>
</head>
<body>
@include('pdf.partials.watermark')

@include('pdf.partials.header', [
  'title' => 'Fee Receipt / Tax Invoice',
  'subtitle' => 'Receipt No: ' . $payment->receipt_number . ' | Date: ' . \Carbon\Carbon::parse($payment->payment_date)->format('d M Y')
])

@if(isset($isDuplicate) && $isDuplicate)
  <div class="duplicate-badge">*** DUPLICATE RECORD COPY ***</div>
@endif

<table class="meta-box">
  <tr>
    <td style="width: 50%;"><strong>Receipt No:</strong> <span style="font-family: monospace; font-size: 10.5px;">{{ $payment->receipt_number }}</span></td>
    <td style="width: 50%; text-align: right;"><strong>Payment Date:</strong> {{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</td>
  </tr>
  <tr>
    <td><strong>Academic Year:</strong> {{ $payment->academic_year ?? ($school?->academic_year ?? 'Current Session') }}</td>
    <td style="text-align: right;"><strong>Collected By:</strong> {{ $payment->collectedBy?->name ?? 'Front Office / Accounts' }}</td>
  </tr>
</table>

<table class="details-table">
  <tr><td>Student Name</td><td>{{ $payment->student?->first_name }} {{ $payment->student?->last_name }}</td></tr>
  <tr><td>Admission No</td><td>{{ $payment->student?->admission_no ?? $payment->student?->admission_number ?? '—' }}</td></tr>
  <tr><td>Class &amp; Section</td><td>{{ $payment->student?->currentEnrollment?->class?->name ?? '—' }}</td></tr>
  <tr><td>Fee Head / Term</td><td>{{ $payment->feeHead?->name ?? $payment->term_name ?? ($payment->term_number ? 'Term ' . $payment->term_number : 'Fee Payment') }}</td></tr>
  @if($payment->feeHead?->hsn_code)
  <tr><td>SAC / HSN Code</td><td>{{ $payment->feeHead->hsn_code }}</td></tr>
  @endif
  <tr><td>Payment Mode</td><td>{{ $payment->payment_mode === 'split' ? 'Split Payment' : ucfirst($payment->payment_mode) }}</td></tr>
  @if($payment->payment_mode === 'split' && $payment->splits && $payment->splits->count() > 0)
    @foreach($payment->splits as $sp)
      <tr>
        <td style="padding-left: 20px; font-size: 9.5px; color: #64748b;">&bull; {{ ucfirst(str_replace('_', ' ', $sp->payment_mode)) }} ({{ $sp->transaction_id ?? $sp->cheque_number ?? 'Direct' }})</td>
        <td style="font-size: 9.5px; font-weight: 600;">₹{{ number_format($sp->amount, 2) }}</td>
      </tr>
    @endforeach
  @endif
  @if($payment->transaction_id)
  <tr><td>Transaction ID / Reference</td><td>{{ $payment->transaction_id }}</td></tr>
  @endif
  @php
    $feeHead = $payment->feeHead;
    $baseAmount = $payment->amount ?? 0;
    $gstAmt = 0;
    if ($feeHead?->gst_applicable && $feeHead->gst_percent > 0) {
      if ($feeHead->gst_type === 'inclusive') {
        $gstAmt = round($baseAmount - ($baseAmount * 100 / (100 + $feeHead->gst_percent)), 2);
        $baseAmount = $baseAmount - $gstAmt;
      } else {
        $gstAmt = round($baseAmount * $feeHead->gst_percent / 100, 2);
      }
    }
  @endphp
  @if($feeHead?->gst_applicable && $feeHead->gst_percent > 0)
  <tr><td>Taxable Amount</td><td>₹{{ number_format($baseAmount, 2) }}</td></tr>
  <tr><td>GST ({{ $feeHead->gst_percent }}% — {{ strtoupper($feeHead->gst_type ?? 'exclusive') }})</td><td>₹{{ number_format($gstAmt, 2) }}</td></tr>
  @else
  <tr><td>Fee Amount</td><td>₹{{ number_format($payment->amount, 2) }}</td></tr>
  @endif
  @if($payment->late_fee > 0)
  <tr><td>Late Fee</td><td>₹{{ number_format($payment->late_fee, 2) }}</td></tr>
  @endif
  @if($payment->discount > 0)
  <tr><td>Concession / Discount</td><td>- ₹{{ number_format($payment->discount, 2) }}</td></tr>
  @endif
  <tr class="total-row"><td>Total Amount Paid</td><td>₹{{ number_format($payment->total_paid, 2) }}</td></tr>
</table>

@include('pdf.partials.signatures', ['type' => 'financial'])

@include('pdf.partials.footer', [
  'footerNote' => 'This is an official computer-generated receipt issued by ' . ($school?->school_name ?? 'the School') . '. Valid without physical seal unless required.'
])
</body>
</html>
