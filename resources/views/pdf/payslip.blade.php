<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Salary Payslip — {{ $employee->full_name }}</title>
<style>
  @page {
    size: A4 portrait;
    margin: 12mm 15mm 16mm 15mm;
  }
  * { box-sizing: border-box; }
  body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 9.5px;
    color: #1e293b;
    margin: 0;
    padding: 0;
  }
  .info-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 12px;
  }
  .info-table td {
    padding: 4.5px 8px;
    border: 1px solid #cbd5e1;
    font-size: 9px;
  }
  .info-table td:nth-child(odd) {
    font-weight: bold;
    background: #f8fafc;
    width: 20%;
    color: #334155;
  }
  .breakdown-table {
    width: 100%;
    border-collapse: collapse;
  }
  .breakdown-table td {
    vertical-align: top;
    padding: 0;
    border: none;
  }
  .earnings-table, .deductions-table {
    width: 100%;
    border-collapse: collapse;
  }
  .earnings-table th, .deductions-table th {
    background: #1e293b;
    color: white;
    padding: 5px 8px;
    text-align: left;
    font-size: 8.5px;
    text-transform: uppercase;
    font-weight: bold;
  }
  .earnings-table td, .deductions-table td {
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
  .net-pay-box {
    background: #f0fdf4;
    border: 1.5px solid #86efac;
    border-radius: 6px;
    padding: 10px;
    text-align: center;
    margin-top: 14px;
  }
  .net-amount {
    font-size: 20px;
    font-weight: bold;
    color: #166534;
    font-family: 'Courier New', Courier, monospace;
    margin: 2px 0;
  }
</style>
</head>
<body>
@include('pdf.partials.watermark')

@include('pdf.partials.header', [
  'title' => 'Monthly Staff Salary Slip',
  'subtitle' => 'Pay Period: ' . \Carbon\Carbon::create($year, $month)->format('F Y')
])

<table class="info-table">
  <tr>
    <td>Employee Name</td><td><strong>{{ $employee->full_name }}</strong></td>
    <td>Employee Code / ID</td><td><strong>{{ $employee->employee_code ?? $employee->employee_number ?? $employee->id }}</strong></td>
  </tr>
  <tr>
    <td>Designation</td><td>{{ $employee->designation?->name ?? 'Faculty / Staff' }}</td>
    <td>Department</td><td>{{ $employee->department?->name ?? 'Academics' }}</td>
  </tr>
  <tr>
    <td>Date of Joining</td><td>{{ $employee->date_of_joining ? \Carbon\Carbon::parse($employee->date_of_joining)->format('d/m/Y') : '—' }}</td>
    <td>Bank Account No.</td><td style="font-family: monospace;">{{ $employee->bank_account_number ?? '—' }}</td>
  </tr>
  <tr>
    <td>PAN Number</td><td style="font-family: monospace;">{{ $employee->pan_number ?? $employee->pan ?? '—' }}</td>
    <td>PF Account No.</td><td style="font-family: monospace;">{{ $employee->pf_account_number ?? '—' }}</td>
  </tr>
</table>

<table class="breakdown-table">
  <tr>
    {{-- Left: Earnings --}}
    <td style="width: 49%; padding-right: 1%;">
      <table class="earnings-table">
        <thead>
          <tr><th colspan="2">Earnings Breakdown</th></tr>
        </thead>
        <tbody>
          <tr><td>Basic Salary</td><td style="text-align:right; font-family: monospace;">₹{{ number_format($payroll->basic_salary, 2) }}</td></tr>
          <tr><td>House Rent Allowance (HRA)</td><td style="text-align:right; font-family: monospace;">₹{{ number_format($payroll->hra ?? 0, 2) }}</td></tr>
          <tr><td>Transport Allowance</td><td style="text-align:right; font-family: monospace;">₹{{ number_format($payroll->transport_allowance ?? 0, 2) }}</td></tr>
          <tr><td>Medical Allowance</td><td style="text-align:right; font-family: monospace;">₹{{ number_format($payroll->medical_allowance ?? 0, 2) }}</td></tr>
          <tr><td>Other Allowances</td><td style="text-align:right; font-family: monospace;">₹{{ number_format($payroll->other_allowances ?? 0, 2) }}</td></tr>
          @if(($payroll->bonus ?? 0) > 0)
            <tr><td>Bonus / Incentive</td><td style="text-align:right; font-family: monospace;">₹{{ number_format($payroll->bonus, 2) }}</td></tr>
          @endif
          <tr class="total-row">
            <td>Gross Earnings</td>
            <td style="text-align:right; font-family: monospace;">₹{{ number_format($payroll->gross_salary, 2) }}</td>
          </tr>
        </tbody>
      </table>
    </td>

    {{-- Right: Deductions --}}
    <td style="width: 49%; padding-left: 1%;">
      <table class="deductions-table">
        <thead>
          <tr><th colspan="2">Deductions Breakdown</th></tr>
        </thead>
        <tbody>
          <tr><td>Provident Fund (PF)</td><td style="text-align:right; font-family: monospace;">₹{{ number_format($payroll->pf_employee ?? 0, 2) }}</td></tr>
          <tr><td>Professional Tax</td><td style="text-align:right; font-family: monospace;">₹{{ number_format($payroll->professional_tax ?? 0, 2) }}</td></tr>
          <tr><td>Tax Deducted at Source (TDS)</td><td style="text-align:right; font-family: monospace;">₹{{ number_format($payroll->tds ?? 0, 2) }}</td></tr>
          @if(($payroll->loan_deduction ?? 0) > 0)
            <tr><td>Loan EMI Recovery</td><td style="text-align:right; font-family: monospace;">₹{{ number_format($payroll->loan_deduction, 2) }}</td></tr>
          @endif
          @if(($payroll->advance_deduction ?? 0) > 0)
            <tr><td>Salary Advance Recovery</td><td style="text-align:right; font-family: monospace;">₹{{ number_format($payroll->advance_deduction, 2) }}</td></tr>
          @endif
          <tr class="total-row">
            <td>Total Deductions</td>
            <td style="text-align:right; font-family: monospace;">₹{{ number_format($payroll->total_deductions ?? 0, 2) }}</td>
          </tr>
        </tbody>
      </table>
    </td>
  </tr>
</table>

<div class="net-pay-box">
  <div style="font-size: 10px; font-weight: bold; color: #374151; text-transform: uppercase; letter-spacing: 0.5px;">Net Salary Disbursed</div>
  <div class="net-amount">₹{{ number_format($payroll->net_salary, 2) }}</div>
  <div style="font-size: 9px; color: #4b5563;">
    Disbursement Status: <strong style="color: {{ $payroll->payment_status === 'paid' ? '#16a34a' : '#d97706' }};">{{ strtoupper($payroll->payment_status ?? 'PAID') }}</strong>
  </div>
</div>

@include('pdf.partials.signatures', ['type' => 'financial'])

@include('pdf.partials.footer', [
  'footerNote' => 'Confidential salary disbursement statement issued by ' . ($school?->school_name ?? 'the Institution') . '.'
])
</body>
</html>
