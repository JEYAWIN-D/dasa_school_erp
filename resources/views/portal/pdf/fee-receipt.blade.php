<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Fee Statement - {{ $student->admission_no }}</title>
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
  table.meta-grid {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 12px;
  }
  table.meta-grid td {
    padding: 0;
    vertical-align: top;
    border: none;
  }
  .info-box {
    border: 1px solid #cbd5e1;
    background: #f8fafc;
    border-radius: 4px;
    padding: 8px 10px;
  }
  .info-title {
    font-size: 9px;
    font-weight: bold;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 4px;
    margin-bottom: 6px;
  }
  table.data-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 12px;
  }
  table.data-table th {
    background: #f1f5f9;
    color: #334155;
    font-weight: bold;
    font-size: 9px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    padding: 6px 8px;
    border: 1px solid #cbd5e1;
  }
  table.data-table td {
    padding: 6px 8px;
    border: 1px solid #e2e8f0;
    font-size: 9.5px;
  }
  .section-title {
    font-size: 10px;
    font-weight: bold;
    color: #0f172a;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin: 10px 0 5px 0;
    padding-bottom: 3px;
    border-bottom: 1.5px solid #cbd5e1;
  }
  .badge-paid {
    display: inline-block;
    background: #dcfce7;
    color: #166534;
    font-size: 8.5px;
    font-weight: bold;
    padding: 2px 6px;
    border-radius: 3px;
  }
  .badge-unpaid {
    display: inline-block;
    background: #fee2e2;
    color: #991b1b;
    font-size: 8.5px;
    font-weight: bold;
    padding: 2px 6px;
    border-radius: 3px;
  }
  .balance-card {
    padding: 8px 12px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: bold;
    margin-bottom: 14px;
    text-align: center;
  }
</style>
</head>
<body>

@include('pdf.partials.watermark')

@include('pdf.partials.header', [
    'title' => 'STUDENT FEE STATEMENT',
    'subtitle' => 'Official Pupil Account Summary',
    'academicYear' => now()->year . '-' . (now()->year + 1),
    'date' => now()->format('d M Y')
])

<table class="meta-grid">
  <tr>
    <td style="width: 49%;">
      <div class="info-box">
        <div class="info-title">Student Information</div>
        <table style="width:100%; border-collapse:collapse; font-size:9.5px;">
          <tr>
            <td style="width:40%; padding:2px 0; color:#64748b;">Full Name:</td>
            <td style="font-weight:bold; color:#0f172a;">{{ $student->first_name }} {{ $student->last_name }}</td>
          </tr>
          <tr>
            <td style="padding:2px 0; color:#64748b;">Admission No:</td>
            <td style="font-weight:bold; color:#0f172a;">{{ $student->admission_no }}</td>
          </tr>
          @if($enrollment)
          <tr>
            <td style="padding:2px 0; color:#64748b;">Class & Section:</td>
            <td style="font-weight:bold; color:#0f172a;">{{ $enrollment->class_name }} {{ $enrollment->section_name }}</td>
          </tr>
          @endif
        </table>
      </div>
    </td>
    <td style="width: 2%;"></td>
    <td style="width: 49%;">
      <div class="info-box">
        <div class="info-title">Account Summary</div>
        <table style="width:100%; border-collapse:collapse; font-size:9.5px;">
          <tr>
            <td style="width:50%; padding:2px 0; color:#64748b;">Total Billed:</td>
            <td style="font-weight:bold; text-align:right; color:#0f172a;">₹{{ number_format($totalDue, 2) }}</td>
          </tr>
          <tr>
            <td style="padding:2px 0; color:#64748b;">Total Paid:</td>
            <td style="font-weight:bold; text-align:right; color:#166534;">₹{{ number_format($totalPaid, 2) }}</td>
          </tr>
          <tr>
            <td style="padding:2px 0; color:#64748b;">Outstanding Balance:</td>
            <td style="font-weight:bold; text-align:right; color:{{ $balance > 0 ? '#b91c1c' : '#166534' }};">
              ₹{{ number_format($balance, 2) }}
            </td>
          </tr>
        </table>
      </div>
    </td>
  </tr>
</table>

@if($invoices->count())
<div class="section-title">Fee Invoices & Charges</div>
<table class="data-table">
  <thead>
    <tr>
      <th style="width: 8%; text-align:center;">#</th>
      <th>Fee Head Description</th>
      <th style="width: 20%; text-align:center;">Due Date</th>
      <th style="width: 15%; text-align:center;">Status</th>
      <th style="width: 22%; text-align:right;">Amount (₹)</th>
    </tr>
  </thead>
  <tbody>
    @foreach($invoices as $idx => $inv)
    <tr>
      <td style="text-align:center; color:#64748b;">{{ $idx + 1 }}</td>
      <td style="font-weight:600;">{{ $inv->fee_head_name ?? 'School Fee Charge' }}</td>
      <td style="text-align:center;">{{ $inv->due_date ? \Carbon\Carbon::parse($inv->due_date)->format('d M Y') : '—' }}</td>
      <td style="text-align:center;">
        <span class="{{ ($inv->status ?? '') === 'paid' ? 'badge-paid' : 'badge-unpaid' }}">
          {{ strtoupper($inv->status ?? 'UNPAID') }}
        </span>
      </td>
      <td style="text-align:right; font-weight:bold;">{{ number_format($inv->amount, 2) }}</td>
    </tr>
    @endforeach
    <tr style="background:#f8fafc; font-weight:bold;">
      <td colspan="4" style="text-align:right; text-transform:uppercase; font-size:9px; color:#475569;">Total Invoiced:</td>
      <td style="text-align:right;">₹{{ number_format($totalDue, 2) }}</td>
    </tr>
  </tbody>
</table>
@endif

@if($payments->count())
<div class="section-title">Receipts & Payment History</div>
<table class="data-table">
  <thead>
    <tr>
      <th style="width: 20%;">Receipt #</th>
      <th style="width: 22%; text-align:center;">Payment Date</th>
      <th style="width: 25%;">Payment Mode</th>
      <th style="text-align:right;">Amount Paid (₹)</th>
    </tr>
  </thead>
  <tbody>
    @foreach($payments as $pay)
    <tr>
      <td style="font-weight:bold; color:#0f172a;">#{{ $pay->receipt_number ?? $pay->id }}</td>
      <td style="text-align:center;">{{ \Carbon\Carbon::parse($pay->payment_date)->format('d M Y') }}</td>
      <td>{{ ucfirst(str_replace('_', ' ', $pay->payment_mode ?? 'Cash')) }}</td>
      <td style="text-align:right; font-weight:bold; color:#166534;">{{ number_format($pay->total_paid ?? 0, 2) }}</td>
    </tr>
    @endforeach
    <tr style="background:#f8fafc; font-weight:bold;">
      <td colspan="3" style="text-align:right; text-transform:uppercase; font-size:9px; color:#475569;">Total Paid to Date:</td>
      <td style="text-align:right; color:#166534;">₹{{ number_format($totalPaid, 2) }}</td>
    </tr>
  </tbody>
</table>
@endif

@if($balance > 0)
<div class="balance-card" style="background:#fef2f2; color:#991b1b; border:1px solid #fecaca;">
  Outstanding Balance Payable: ₹{{ number_format($balance, 2) }}
</div>
@else
<div class="balance-card" style="background:#f0fdf4; color:#166534; border:1px solid #bbf7d0;">
  Account Fully Cleared — Zero Outstanding Dues
</div>
@endif

@include('pdf.partials.signatures', ['mode' => 'financial'])

@include('pdf.partials.footer', ['computerGenerated' => true])

</body>
</html>
