<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Fee Demand Notice — {{ $student->first_name }} {{ $student->last_name }}</title>
<style>
  @page {
    size: A4 portrait;
    margin: 12mm 15mm 16mm 15mm;
  }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body {
    font-family: Arial, sans-serif;
    font-size: 10px;
    color: #1e293b;
    line-height: 1.6;
    margin: 0;
    padding: 0;
  }
  .notice-meta {
    width: 100%;
    margin-bottom: 12px;
    font-size: 9.5px;
    color: #475569;
    border-collapse: collapse;
  }
  .notice-meta td { padding: 0; border: none; }
  .to-block {
    margin-bottom: 12px;
    font-size: 10px;
    line-height: 1.4;
  }
  p { margin-bottom: 8px; line-height: 1.6; }
  table.dues-table {
    width: 100%;
    border-collapse: collapse;
    margin: 10px 0 14px 0;
  }
  table.dues-table th {
    background: #1e293b;
    color: white;
    padding: 5px 8px;
    font-size: 9px;
    text-align: left;
    border: 1px solid #334155;
    text-transform: uppercase;
  }
  table.dues-table td {
    padding: 5px 8px;
    border: 1px solid #cbd5e1;
    font-size: 9.5px;
  }
  table.dues-table tr:nth-child(even) td { background: #f8fafc; }
  .total-row td {
    font-weight: bold !important;
    background: #fef2f2 !important;
    color: #991b1b !important;
    border-top: 1.5px solid #ef4444 !important;
  }
  .warning-box {
    background: #fffbeb;
    border: 1px solid #fde68a;
    border-radius: 4px;
    padding: 10px 14px;
    margin: 12px 0;
    font-size: 9.5px;
    color: #92400e;
    line-height: 1.5;
  }
</style>
</head>
<body>
@include('pdf.partials.watermark')

@include('pdf.partials.header', [
  'title' => 'Official Fee Demand Notice & Dues Advisory',
  'subtitle' => 'Academic Session: ' . ($currentYear?->name ?? now()->year) . ' | Date: ' . now()->format('d F Y')
])

<table class="notice-meta">
  <tr>
    <td style="text-align: left;"><strong>Notice Ref:</strong> FDN/{{ $student->admission_no ?? $student->admission_number ?? $student->id }}/{{ now()->format('Y') }}</td>
    <td style="text-align: right;"><strong>Date:</strong> {{ now()->format('d F Y') }}</td>
  </tr>
</table>

<div class="to-block">
  To,<br>
  <strong>Parent / Guardian of {{ $student->first_name }} {{ $student->last_name }}</strong><br>
  Class &amp; Section: <strong>{{ $student->currentEnrollment?->class?->name ?? '—' }}</strong><br>
  Admission No: <strong style="font-family: monospace;">{{ $student->admission_no ?? $student->admission_number ?? '—' }}</strong>
</div>

<p>Dear Parent / Guardian,</p>
<p>
  This official communication is to notify you that according to the accounts department records of
  <strong>{{ $school?->school_name ?? config('app.name', 'the School') }}</strong>, the following statutory and tuition fee installments remain pending for the current academic session:
</p>

<table class="dues-table">
  <thead>
    <tr>
      <th>Fee Category / Head</th>
      <th style="width: 90px; text-align: right;">Total Prescribed</th>
      <th style="width: 90px; text-align: right;">Amount Paid</th>
      <th style="width: 95px; text-align: right;">Outstanding Balance</th>
    </tr>
  </thead>
  <tbody>
    @foreach($dues as $due)
    <tr>
      <td><strong>{{ $due['feeHead']?->name ?? 'Tuition Fee' }}</strong></td>
      <td style="text-align: right; font-family: monospace;">₹{{ number_format($due['amount'], 2) }}</td>
      <td style="text-align: right; font-family: monospace;">₹{{ number_format($due['paid'], 2) }}</td>
      <td style="text-align: right; font-family: monospace; font-weight: bold; color: #dc2626;">₹{{ number_format($due['balance'], 2) }}</td>
    </tr>
    @endforeach
    <tr class="total-row">
      <td style="text-transform: uppercase;">Total Outstanding Dues</td>
      <td style="text-align: right; font-family: monospace;">₹{{ number_format($dues->sum('amount'), 2) }}</td>
      <td style="text-align: right; font-family: monospace;">₹{{ number_format($dues->sum('paid'), 2) }}</td>
      <td style="text-align: right; font-family: monospace; font-size: 11px;">₹{{ number_format($dues->sum('balance'), 2) }}</td>
    </tr>
  </tbody>
</table>

<div class="warning-box">
  <strong>Payment Reminder:</strong> Kindly remit the outstanding balance of <strong>₹{{ number_format($dues->sum('balance'), 2) }}</strong> within <strong>7 working days</strong> from the receipt of this notice to ensure uninterrupted academic continuity, issuance of examination hall tickets, and progress report releases.
</div>

<p>You may settle fees via online portal or at the accounts cash counter during standard office hours (9:00 AM – 2:00 PM).</p>

<table style="width: 100%; border-collapse: collapse; border: none; margin-top: 30px; page-break-inside: avoid;">
  <tr>
    <td style="width: 50%; text-align: left; vertical-align: bottom; border: none;">
      <div style="border-top: 1px dashed #64748b; width: 140px; margin-bottom: 3px;"></div>
      <span style="font-size: 8.5px; font-weight: bold; color: #334155;">Accounts Officer / Bursar</span>
    </td>
    <td style="width: 50%; text-align: right; vertical-align: bottom; border: none;">
      @php
        $sigBase64 = null;
        if (!empty($school?->principal_signature) && \Illuminate\Support\Facades\Storage::disk('public')->exists($school->principal_signature)) {
            $sigBase64 = 'data:image/png;base64,' . base64_encode(\Illuminate\Support\Facades\Storage::disk('public')->get($school->principal_signature));
        }
      @endphp
      @if($sigBase64)
        <img src="{{ $sigBase64 }}" alt="Signature" style="height: 28px; display: inline-block; margin-bottom: 2px;">
      @endif
      <div style="border-top: 1px dashed #64748b; width: 140px; margin-left: auto; margin-bottom: 3px;"></div>
      <span style="font-size: 8.5px; font-weight: bold; color: #334155;">Principal</span>
    </td>
  </tr>
</table>

@include('pdf.partials.footer')
</body>
</html>
