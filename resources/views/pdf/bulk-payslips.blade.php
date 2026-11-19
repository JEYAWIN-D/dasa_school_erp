<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Bulk Staff Salary Payslips</title>
<style>
  @page {
    size: A4 portrait;
    margin: 12mm 15mm 16mm 15mm;
  }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, sans-serif; font-size: 9.5px; color: #1e293b; }
  .slip-page {
    page-break-after: always;
    position: relative;
    padding-bottom: 20px;
  }
  .slip-page:last-child { page-break-after: auto; }
  table.info-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 12px;
  }
  table.info-table td {
    padding: 4.5px 8px;
    border: 1px solid #cbd5e1;
    font-size: 9px;
  }
  table.info-table td:nth-child(odd) {
    font-weight: bold;
    background: #f8fafc;
    width: 20%;
    color: #334155;
  }
  table.breakdown-table {
    width: 100%;
    border-collapse: collapse;
  }
  table.breakdown-table td {
    vertical-align: top;
    padding: 0;
    border: none;
  }
  table.earnings-table, table.deductions-table {
    width: 100%;
    border-collapse: collapse;
  }
  table.earnings-table th, table.deductions-table th {
    background: #1e293b;
    color: white;
    padding: 5px 8px;
    text-align: left;
    font-size: 8.5px;
    text-transform: uppercase;
    font-weight: bold;
  }
  table.earnings-table td, table.deductions-table td {
    padding: 4px 8px;
    border-bottom: 1px solid #e2e8f0;
    font-size: 9px;
  }
  .total-row td {
    font-weight: bold !important;
    background: #f8fafc;
    border-top: 1.5px solid #0f172a !important;
    color: #0f172a !important;
  }
  .net-box {
    background: #f0fdf4;
    border: 1.5px solid #86efac;
    border-radius: 4px;
    padding: 10px;
    text-align: center;
    margin-top: 14px;
  }
  .net-amount {
    font-size: 20px;
    font-weight: bold;
    color: #166534;
    font-family: monospace;
    margin: 2px 0;
  }
</style>
</head>
<body>
@foreach($records as $payroll)
@php
  $emp = $payroll->employee;
  $basic = $payroll->basic_salary ?? ($emp->basic_salary ?? 0);
  $hra   = $payroll->hra ?? round($basic * 0.4);
  $ta    = $payroll->transport_allowance ?? 1600;
  $other = $payroll->other_allowance ?? 0;
  $gross = $basic + $hra + $ta + $other;
  $pf    = $payroll->pf_employee ?? round($basic * 0.12);
  $esi   = $payroll->esi_employee ?? (($gross <= 21000) ? round($gross * 0.0075) : 0);
  $tds   = $payroll->tds ?? 0;
  $otherDeductions = $payroll->other_deductions ?? 0;
  $totalDeductions = $pf + $esi + $tds + $otherDeductions;
  $net   = $gross - $totalDeductions;
@endphp
<div class="slip-page">
  @include('pdf.partials.watermark')

  @include('pdf.partials.header', [
    'title' => 'Monthly Staff Salary Slip',
    'subtitle' => 'Pay Period: ' . \Carbon\Carbon::createFromFormat('Y-m', $year . '-' . $month)->format('F Y')
  ])

  <table class="info-table">
    <tr>
      <td>Employee Full Name</td><td><strong>{{ $emp->full_name ?? ($emp->first_name . ' ' . $emp->last_name) }}</strong></td>
      <td>Employee Code</td><td><strong style="font-family: monospace;">{{ $emp->employee_code ?? $emp->employee_number ?? $emp->id }}</strong></td>
    </tr>
    <tr>
      <td>Designation</td><td>{{ is_object($emp->designation) ? $emp->designation->name : ($emp->designation ?? '—') }}</td>
      <td>Department</td><td>{{ is_object($emp->department) ? $emp->department->name : ($emp->department ?? '—') }}</td>
    </tr>
    <tr>
      <td>Working / Present Days</td><td>{{ $payroll->working_days ?? 26 }} / {{ $payroll->present_days ?? 26 }} Days</td>
      <td>Bank Account</td><td style="font-family: monospace;">{{ $emp->bank_account_number ?? '—' }}</td>
    </tr>
  </table>

  <table class="breakdown-table">
    <tr>
      <td style="width: 49%; padding-right: 1%;">
        <table class="earnings-table">
          <thead>
            <tr><th colspan="2">Earnings Breakdown</th></tr>
          </thead>
          <tbody>
            <tr><td>Basic Salary</td><td style="text-align: right; font-family: monospace;">₹{{ number_format($basic, 2) }}</td></tr>
            <tr><td>House Rent Allowance (HRA)</td><td style="text-align: right; font-family: monospace;">₹{{ number_format($hra, 2) }}</td></tr>
            <tr><td>Transport Allowance</td><td style="text-align: right; font-family: monospace;">₹{{ number_format($ta, 2) }}</td></tr>
            @if($other > 0)
              <tr><td>Other Allowances</td><td style="text-align: right; font-family: monospace;">₹{{ number_format($other, 2) }}</td></tr>
            @endif
            <tr class="total-row">
              <td>Gross Salary</td>
              <td style="text-align: right; font-family: monospace;">₹{{ number_format($gross, 2) }}</td>
            </tr>
          </tbody>
        </table>
      </td>
      <td style="width: 49%; padding-left: 1%;">
        <table class="deductions-table">
          <thead>
            <tr><th colspan="2">Deductions Breakdown</th></tr>
          </thead>
          <tbody>
            <tr><td>Provident Fund (PF)</td><td style="text-align: right; font-family: monospace;">₹{{ number_format($pf, 2) }}</td></tr>
            @if($esi > 0)
              <tr><td>ESI Contribution</td><td style="text-align: right; font-family: monospace;">₹{{ number_format($esi, 2) }}</td></tr>
            @endif
            @if($tds > 0)
              <tr><td>Tax Deducted (TDS)</td><td style="text-align: right; font-family: monospace;">₹{{ number_format($tds, 2) }}</td></tr>
            @endif
            @if($otherDeductions > 0)
              <tr><td>Other Deductions</td><td style="text-align: right; font-family: monospace;">₹{{ number_format($otherDeductions, 2) }}</td></tr>
            @endif
            <tr class="total-row">
              <td>Total Deductions</td>
              <td style="text-align: right; font-family: monospace;">₹{{ number_format($totalDeductions, 2) }}</td>
            </tr>
          </tbody>
        </table>
      </td>
    </tr>
  </table>

  <div class="net-box">
    <div style="font-size: 9.5px; font-weight: bold; color: #374151; text-transform: uppercase;">Net Disbursed Compensation</div>
    <div class="net-amount">₹{{ number_format($net, 2) }}</div>
  </div>

  <table style="width: 100%; border-collapse: collapse; border: none; margin-top: 30px; page-break-inside: avoid;">
    <tr>
      <td style="width: 50%; text-align: left; vertical-align: bottom; border: none;">
        <div style="border-top: 1px dashed #64748b; width: 140px; margin-bottom: 3px;"></div>
        <span style="font-size: 8px; font-weight: bold; color: #334155;">Employee Signature</span>
      </td>
      <td style="width: 50%; text-align: right; vertical-align: bottom; border: none;">
        <div style="border-top: 1px dashed #64748b; width: 140px; margin-left: auto; margin-bottom: 3px;"></div>
        <span style="font-size: 8px; font-weight: bold; color: #334155;">Accounts &amp; HR Officer</span>
      </td>
    </tr>
  </table>

  @include('pdf.partials.footer', [
    'footerNote' => 'Official computer-generated salary slip issued by ' . ($school?->school_name ?? 'the Institution') . '.'
  ])
</div>
@endforeach
</body>
</html>
