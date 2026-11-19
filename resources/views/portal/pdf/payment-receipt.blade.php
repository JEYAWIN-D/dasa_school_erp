<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Fee Payment Receipt - {{ $payment->receipt_number ?? $payment->id }}</title>
<style>
  @page {
    size: A4 portrait;
    margin: 12mm 15mm 16mm 15mm;
  }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 10px;
    color: #1e293b;
    line-height: 1.45;
  }
  .receipt-strip {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 12px;
  }
  .receipt-strip td {
    padding: 0;
    vertical-align: middle;
    border: none;
  }
  .receipt-tag {
    display: inline-block;
    border: 1.5px solid #166534;
    background: #f0fdf4;
    color: #166534;
    font-size: 11px;
    font-weight: bold;
    padding: 4px 12px;
    border-radius: 4px;
    letter-spacing: 0.5px;
  }
  table.meta-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 12px;
    border: 1px solid #cbd5e1;
    background: #f8fafc;
  }
  table.meta-table td {
    padding: 6px 10px;
    border: 1px solid #e2e8f0;
    font-size: 9.5px;
  }
  .meta-label {
    width: 18%;
    color: #64748b;
    font-size: 9px;
    text-transform: uppercase;
  }
  .meta-val {
    width: 32%;
    font-weight: bold;
    color: #0f172a;
  }
  table.item-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 14px;
  }
  table.item-table th {
    background: #f1f5f9;
    color: #334155;
    font-size: 9px;
    font-weight: bold;
    text-transform: uppercase;
    padding: 7px 10px;
    border: 1px solid #cbd5e1;
    text-align: left;
  }
  table.item-table td {
    padding: 8px 10px;
    border: 1px solid #e2e8f0;
    font-size: 10px;
  }
  .total-card {
    background: #0f172a;
    color: #ffffff;
    border-radius: 4px;
    padding: 10px 14px;
    margin-bottom: 14px;
  }
  .total-card table {
    width: 100%;
    border-collapse: collapse;
    color: #ffffff;
  }
  .total-card td {
    border: none;
    padding: 0;
  }
  .mode-pill {
    display: inline-block;
    background: #e0f2fe;
    color: #0369a1;
    font-size: 9px;
    font-weight: bold;
    padding: 2px 8px;
    border-radius: 3px;
    text-transform: uppercase;
  }
  .remarks-box {
    border: 1px dashed #cbd5e1;
    background: #f8fafc;
    border-radius: 4px;
    padding: 6px 10px;
    font-size: 9px;
    color: #475569;
    margin-bottom: 14px;
  }
</style>
</head>
<body>

@include('pdf.partials.watermark')

@include('pdf.partials.header', [
    'title' => 'FEE PAYMENT RECEIPT',
    'subtitle' => 'Official Acknowledgment of Payment',
    'date' => \Carbon\Carbon::parse($payment->payment_date)->format('d M Y')
])

<table class="receipt-strip">
  <tr>
    <td style="text-align: left;">
      <span class="receipt-tag">RECEIPT #: {{ $payment->receipt_number ?? $payment->id }}</span>
    </td>
    <td style="text-align: right; font-size: 9px; color: #64748b;">
      Payment Date: <strong style="color:#0f172a;">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</strong>
    </td>
  </tr>
</table>

<table class="meta-table">
  <tr>
    <td class="meta-label">Student Name:</td>
    <td class="meta-val">{{ $student->first_name ?? '' }} {{ $student->last_name ?? '' }}</td>
    <td class="meta-label">Admission No:</td>
    <td class="meta-val">{{ $student->admission_no }}</td>
  </tr>
  <tr>
    <td class="meta-label">Class & Section:</td>
    <td class="meta-val">{{ $enrollment?->class_name ?? '—' }} {{ $enrollment?->section_name ?? '' }}</td>
    <td class="meta-label">Payment Mode:</td>
    <td class="meta-val">
      <span class="mode-pill">{{ strtoupper(str_replace('_', ' ', $payment->payment_mode ?? 'cash')) }}</span>
    </td>
  </tr>
  @if($payment->transaction_id)
  <tr>
    <td class="meta-label">Transaction Ref:</td>
    <td class="meta-val" colspan="3" style="font-family: monospace;">{{ $payment->transaction_id }}</td>
  </tr>
  @endif
</table>

<table class="item-table">
  <thead>
    <tr>
      <th style="width: 10%; text-align:center;">#</th>
      <th>Particulars / Fee Head</th>
      <th style="width: 25%; text-align:right;">Amount (₹)</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td style="text-align:center; color:#64748b;">1</td>
      <td style="font-weight:600;">{{ $payment->fee_head_name ?? 'School Term Fee' }}</td>
      <td style="text-align:right; font-weight:bold;">{{ number_format($payment->amount, 2) }}</td>
    </tr>
    @if($payment->discount > 0)
    <tr>
      <td style="text-align:center; color:#166534;">—</td>
      <td style="color:#166534;">Concession / Fee Discount</td>
      <td style="text-align:right; color:#166534; font-weight:bold;">- {{ number_format($payment->discount, 2) }}</td>
    </tr>
    @endif
    @if($payment->late_fee > 0)
    <tr>
      <td style="text-align:center; color:#b91c1c;">—</td>
      <td style="color:#b91c1c;">Late Payment Fee / Fine</td>
      <td style="text-align:right; color:#b91c1c; font-weight:bold;">+ {{ number_format($payment->late_fee, 2) }}</td>
    </tr>
    @endif
  </tbody>
</table>

<div class="total-card">
  <table>
    <tr>
      <td style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Net Amount Received</td>
      <td style="text-align: right; font-size: 16px; font-weight: bold;">₹{{ number_format($payment->total_paid, 2) }}</td>
    </tr>
  </table>
</div>

@if($payment->remarks)
<div class="remarks-box">
  <strong>Remarks / Note:</strong> {{ $payment->remarks }}
</div>
@endif

@include('pdf.partials.signatures', ['mode' => 'financial'])

@include('pdf.partials.footer', ['computerGenerated' => true])

</body>
</html>
