<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Statutory Challan — {{ strtoupper($type) }}</title>
<style>
  @page {
    size: A4 portrait;
    margin: 12mm 15mm 16mm 15mm;
  }
  body {
    font-family: Arial, sans-serif;
    font-size: 9px;
    color: #1e293b;
    margin: 0;
    padding: 0;
  }
  table.summary-strip {
    width: 100%;
    border-collapse: collapse;
    margin: 8px 0 12px 0;
  }
  table.summary-strip td {
    padding: 6px 10px;
    border: 1px solid #cbd5e1;
    background: #f8fafc;
    text-align: center;
  }
  .sum-val { font-size: 14px; font-weight: bold; color: #0f172a; font-family: monospace; }
  .sum-lbl { font-size: 7.5px; color: #64748b; text-transform: uppercase; font-weight: bold; margin-top: 1px; }
  table.data-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 12px;
  }
  table.data-table th {
    background: #1e293b;
    color: white;
    padding: 4.5px 6px;
    text-align: left;
    font-size: 8px;
    text-transform: uppercase;
    font-weight: bold;
    border: 1px solid #334155;
  }
  table.data-table td {
    padding: 4px 6px;
    border: 1px solid #cbd5e1;
    font-size: 8.5px;
  }
  table.data-table tr:nth-child(even) td { background: #f8fafc; }
  .total-row td {
    font-weight: bold !important;
    background: #f1f5f9 !important;
    border-top: 1.5px solid #0f172a !important;
    color: #0f172a !important;
  }
</style>
</head>
<body>
@php
  $challanTitle = match($type) {
    'pf' => 'Employee Provident Fund (EPF) Statutory Remittance Challan',
    'esi' => 'Employees State Insurance (ESI) Contribution Challan',
    default => 'Professional Tax (PT) Statutory Deduction Statement'
  };
@endphp

@include('pdf.partials.watermark')

@include('pdf.partials.header', [
  'title' => $challanTitle,
  'subtitle' => 'Month: ' . \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y')
])

<table class="summary-strip">
  <tr>
    @if($type === 'pf')
      <td>
        <div class="sum-val">₹{{ number_format($totals['pf_employee'], 2) }}</div>
        <div class="sum-lbl">Employee PF (12%)</div>
      </td>
      <td>
        <div class="sum-val">₹{{ number_format($totals['pf_employer'], 2) }}</div>
        <div class="sum-lbl">Employer PF (12%)</div>
      </td>
      <td style="background: #eff6ff;">
        <div class="sum-val" style="color: #1e40af;">₹{{ number_format($totals['pf_total'], 2) }}</div>
        <div class="sum-lbl" style="color: #1e40af;">Total EPF Remittance</div>
      </td>
    @elseif($type === 'esi')
      <td>
        <div class="sum-val">₹{{ number_format($totals['esi_employee'], 2) }}</div>
        <div class="sum-lbl">Employee ESI (0.75%)</div>
      </td>
      <td>
        <div class="sum-val">₹{{ number_format($totals['esi_employer'], 2) }}</div>
        <div class="sum-lbl">Employer ESI (3.25%)</div>
      </td>
      <td style="background: #eff6ff;">
        <div class="sum-val" style="color: #1e40af;">₹{{ number_format($totals['esi_total'], 2) }}</div>
        <div class="sum-lbl" style="color: #1e40af;">Total ESI Remittance</div>
      </td>
    @else
      <td style="background: #eff6ff;">
        <div class="sum-val" style="color: #1e40af;">₹{{ number_format($totals['pt_total'], 2) }}</div>
        <div class="sum-lbl" style="color: #1e40af;">Total Professional Tax Deductions</div>
      </td>
    @endif
  </tr>
</table>

<table class="data-table">
  <thead>
    <tr>
      <th style="width: 25px;">#</th>
      <th style="width: 75px;">Emp Code</th>
      <th>Employee Name</th>
      @if($type === 'pf')
        <th style="width: 85px;">UAN</th>
        <th style="width: 80px; text-align: right;">PF Wages (₹)</th>
        <th style="width: 75px; text-align: right;">EE Share</th>
        <th style="width: 75px; text-align: right;">ER Share</th>
        <th style="width: 80px; text-align: right;">Total PF</th>
      @elseif($type === 'esi')
        <th style="width: 85px;">ESI IP No.</th>
        <th style="width: 80px; text-align: right;">ESI Wages</th>
        <th style="width: 75px; text-align: right;">EE (0.75%)</th>
        <th style="width: 75px; text-align: right;">ER (3.25%)</th>
        <th style="width: 80px; text-align: right;">Total ESI</th>
      @else
        <th>Designation</th>
        <th style="width: 90px; text-align: right;">Gross Pay</th>
        <th style="width: 90px; text-align: right;">PT Amount</th>
      @endif
    </tr>
  </thead>
  <tbody>
    @foreach($rows as $i => $row)
    <tr>
      <td>{{ $i + 1 }}</td>
      <td style="font-family: monospace;">{{ $row['employee']?->employee_code ?? $row['employee']?->employee_number ?? $row['employee']?->id }}</td>
      <td><strong>{{ $row['employee']?->full_name ?: ($row['employee']?->first_name . ' ' . $row['employee']?->last_name) }}</strong></td>
      @if($type === 'pf')
        <td style="font-family: monospace;">{{ $row['employee']?->pf_number ?? '—' }}</td>
        <td style="text-align: right; font-family: monospace;">{{ number_format($row['pf_wages'], 2) }}</td>
        <td style="text-align: right; font-family: monospace;">{{ number_format($row['pf_employee'], 2) }}</td>
        <td style="text-align: right; font-family: monospace;">{{ number_format($row['pf_employer'], 2) }}</td>
        <td style="text-align: right; font-family: monospace; font-weight: bold;">{{ number_format($row['pf_total'], 2) }}</td>
      @elseif($type === 'esi')
        <td style="font-family: monospace;">{{ $row['employee']?->esi_number ?? '—' }}</td>
        <td style="text-align: right; font-family: monospace;">{{ number_format($row['esi_wages'], 2) }}</td>
        <td style="text-align: right; font-family: monospace;">{{ number_format($row['esi_employee'], 2) }}</td>
        <td style="text-align: right; font-family: monospace;">{{ number_format($row['esi_employer'], 2) }}</td>
        <td style="text-align: right; font-family: monospace; font-weight: bold;">{{ number_format($row['esi_total'], 2) }}</td>
      @else
        <td>{{ is_object($row['employee']?->designation) ? $row['employee']->designation->name : ($row['employee']?->designation ?? 'Staff') }}</td>
        <td style="text-align: right; font-family: monospace;">{{ number_format($row['record']->gross_salary ?? 0, 2) }}</td>
        <td style="text-align: right; font-family: monospace; font-weight: bold;">{{ number_format($row['pt'], 2) }}</td>
      @endif
    </tr>
    @endforeach
    <tr class="total-row">
      <td colspan="{{ $type === 'pf' ? 5 : ($type === 'esi' ? 4 : 3) }}" style="text-align: right;">TOTAL STATUTORY LIABILITY:</td>
      @if($type === 'pf')
        <td style="text-align: right; font-family: monospace;">{{ number_format($totals['pf_employee'], 2) }}</td>
        <td style="text-align: right; font-family: monospace;">{{ number_format($totals['pf_employer'], 2) }}</td>
        <td style="text-align: right; font-family: monospace;">{{ number_format($totals['pf_total'], 2) }}</td>
      @elseif($type === 'esi')
        <td style="text-align: right; font-family: monospace;">{{ number_format($totals['esi_employee'], 2) }}</td>
        <td style="text-align: right; font-family: monospace;">{{ number_format($totals['esi_employer'], 2) }}</td>
        <td style="text-align: right; font-family: monospace;">{{ number_format($totals['esi_total'], 2) }}</td>
      @else
        <td style="text-align: right; font-family: monospace;">{{ number_format($totals['pt_total'], 2) }}</td>
      @endif
    </tr>
  </tbody>
</table>

@include('pdf.partials.signatures', ['type' => 'financial'])

@include('pdf.partials.footer', [
  'footerNote' => 'Statutory audit schedule prepared for remittance verification under institutional rules.'
])
</body>
</html>
