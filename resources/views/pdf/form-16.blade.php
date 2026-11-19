<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Form 16 — {{ $employee->first_name }} {{ $employee->last_name }}</title>
<style>
  @page {
    size: A4 portrait;
    margin: 12mm 15mm 16mm 15mm;
  }
  body {
    font-family: Arial, sans-serif;
    font-size: 9.5px;
    color: #1e293b;
    margin: 0;
    padding: 0;
  }
  .section-title {
    background: #f1f5f9;
    padding: 4px 8px;
    font-weight: bold;
    font-size: 9px;
    margin: 8px 0 4px;
    border-left: 3px solid #0f172a;
    text-transform: uppercase;
    color: #0f172a;
  }
  table.data-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 8px;
  }
  table.data-table td, table.data-table th {
    border: 1px solid #cbd5e1;
    padding: 4px 6px;
    font-size: 8.5px;
  }
  table.data-table th {
    background: #f8fafc;
    font-weight: bold;
    color: #334155;
  }
  .label-col {
    width: 55%;
    background: #f8fafc;
    font-weight: 600;
  }
  .amount-col {
    text-align: right;
    width: 45%;
    font-family: monospace;
  }
  .total-row td {
    background: #f1f5f9;
    font-weight: bold;
  }
  .verification-box {
    background: #fffbeb;
    border: 1px solid #fde68a;
    padding: 8px 10px;
    font-size: 8.5px;
    margin: 8px 0;
    border-radius: 3px;
    color: #92400e;
  }
</style>
</head>
<body>
@include('pdf.partials.watermark')

@include('pdf.partials.header', [
  'title' => 'Form 16 — Certificate of Tax Deducted at Source',
  'subtitle' => 'Financial Year: ' . $financialYear . ' | Issued under Section 203 of the Income Tax Act, 1961'
])

@if(!empty($school?->pan) || !empty($school?->gstin))
  <div style="background: #f8fafc; border: 1px solid #cbd5e1; padding: 4px 8px; font-size: 8.5px; margin-bottom: 8px; text-align: center;">
    @if(!empty($school?->pan)) <strong>PAN of Deductor:</strong> {{ $school->pan }} &nbsp;|&nbsp; @endif
    @if(!empty($school?->gstin)) <strong>GSTIN:</strong> {{ $school->gstin }} &nbsp;|&nbsp; @endif
    <strong>Deductor Category:</strong> Educational Institution
  </div>
@endif

<div class="section-title">Part A — Deductee / Employee Details</div>
<table class="data-table">
  <tr>
    <td class="label-col">Name of Employee</td>
    <td><strong>{{ $employee->full_name ?: ($employee->first_name . ' ' . $employee->last_name) }}</strong></td>
  </tr>
  <tr>
    <td class="label-col">PAN of Employee</td>
    <td style="font-family: monospace;">{{ $employee->pan_number ?? $employee->pan_no ?? '—' }}</td>
  </tr>
  <tr>
    <td class="label-col">Employee Code / Roll</td>
    <td style="font-family: monospace;">{{ $employee->employee_code ?? $employee->employee_number ?? $employee->id }}</td>
  </tr>
  <tr>
    <td class="label-col">Period of Assessment</td>
    <td>01 April {{ substr($financialYear, 0, 4) }} to 31 March {{ substr($financialYear, -4) }}</td>
  </tr>
  <tr>
    <td class="label-col">Applicable Tax Regime</td>
    <td><strong>{{ strtoupper($employee->tax_regime ?? 'new') }} REGIME</strong></td>
  </tr>
</table>

<div class="section-title">Part B — Summary of Salary Paid and Tax Deducted</div>
<table class="data-table">
  <tr>
    <td class="label-col">1. Gross Salary Paid / Credited</td>
    <td class="amount-col">₹ {{ number_format($annualGross, 2) }}</td>
  </tr>
  <tr>
    <td class="label-col">2. Standard Deduction (Sec 16(ia))</td>
    <td class="amount-col">₹ {{ number_format($stdDeduction ?? 50000, 2) }}</td>
  </tr>
  <tr>
    <td class="label-col">3. Professional Tax Deductions (Sec 16(iii))</td>
    <td class="amount-col">₹ {{ number_format($totalPt ?? 0, 2) }}</td>
  </tr>
  <tr class="total-row">
    <td>4. Net Taxable Income from Salaries</td>
    <td class="amount-col" style="font-weight: bold;">₹ {{ number_format($taxableIncome, 2) }}</td>
  </tr>
  <tr>
    <td class="label-col">5. Total Tax Deducted at Source (TDS)</td>
    <td class="amount-col">₹ {{ number_format($totalTds, 2) }}</td>
  </tr>
  <tr class="total-row">
    <td>6. Net Tax Payable / Balance</td>
    <td class="amount-col" style="color: #166534; font-weight: bold;">₹ 0.00</td>
  </tr>
</table>

<div class="section-title">Monthly Salary &amp; TDS Disbursement Schedule</div>
<table class="data-table">
  <thead>
    <tr>
      <th>Month</th>
      <th style="text-align: right;">Gross Pay</th>
      <th style="text-align: right;">Deductions</th>
      <th style="text-align: right;">Net Disbursed</th>
      <th style="text-align: right;">TDS Deducted</th>
    </tr>
  </thead>
  <tbody>
    @foreach($payrolls as $p)
    <tr>
      <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $p->month)->format('M Y') }}</td>
      <td style="text-align: right; font-family: monospace;">₹ {{ number_format($p->gross_salary, 2) }}</td>
      <td style="text-align: right; font-family: monospace;">₹ {{ number_format($p->deductions, 2) }}</td>
      <td style="text-align: right; font-family: monospace;">₹ {{ number_format($p->net_salary, 2) }}</td>
      <td style="text-align: right; font-family: monospace; font-weight: bold;">₹ {{ number_format($p->tds_amount ?? ($p->tds ?? 0), 2) }}</td>
    </tr>
    @endforeach
    <tr class="total-row">
      <td>Annual Total</td>
      <td style="text-align: right; font-family: monospace;">₹ {{ number_format($annualGross, 2) }}</td>
      <td style="text-align: right; font-family: monospace;">₹ {{ number_format($totalDeductions, 2) }}</td>
      <td style="text-align: right; font-family: monospace;">₹ {{ number_format($netPaid, 2) }}</td>
      <td style="text-align: right; font-family: monospace;">₹ {{ number_format($totalTds, 2) }}</td>
    </tr>
  </tbody>
</table>

<div class="verification-box">
  <strong>Statutory Declaration:</strong> I, the Drawing and Disbursing Officer / Principal of {{ $school?->school_name ?? 'the Institution' }}, certify that the tax deducted at source has been deposited into the Central Government treasury account and all information reported herein is verified against official records.
</div>

<table style="width: 100%; border-collapse: collapse; border: none; margin-top: 30px; page-break-inside: avoid;">
  <tr>
    <td style="width: 33%; text-align: left; vertical-align: bottom; border: none;">
      <div style="border-top: 1px dashed #64748b; width: 120px; margin-bottom: 3px;"></div>
      <span style="font-size: 8px; font-weight: bold; color: #334155;">Date: {{ now()->format('d M Y') }}</span>
    </td>
    <td style="width: 34%; text-align: center; vertical-align: bottom; border: none;">
      @php
        $stampImg = $school ? $school->getSealBase64(1) : null;
      @endphp
      @if($stampImg)
        <img src="{{ $stampImg }}" alt="Seal" style="max-height: 40px; margin-bottom: 2px; opacity: 0.85;">
      @else
        <div style="height: 35px; border: 1px dashed #cbd5e1; width: 90px; margin: 0 auto 2px auto; line-height: 35px; font-size: 7px; color: #94a3b8;">Office Seal</div>
      @endif
      <div style="border-top: 1px dashed #64748b; width: 120px; margin: 0 auto 3px auto;"></div>
      <span style="font-size: 8px; font-weight: bold; color: #334155;">Institutional Seal</span>
    </td>
    <td style="width: 33%; text-align: right; vertical-align: bottom; border: none;">
      @php
        $sigBase64 = null;
        if (!empty($school?->principal_signature) && \Illuminate\Support\Facades\Storage::disk('public')->exists($school->principal_signature)) {
            $sigBase64 = 'data:image/png;base64,' . base64_encode(\Illuminate\Support\Facades\Storage::disk('public')->get($school->principal_signature));
        }
      @endphp
      @if($sigBase64)
        <img src="{{ $sigBase64 }}" alt="Signature" style="height: 28px; display: inline-block; margin-bottom: 2px;">
      @endif
      <div style="border-top: 1px dashed #64748b; width: 120px; margin-left: auto; margin-bottom: 3px;"></div>
      <span style="font-size: 8px; font-weight: bold; color: #334155;">Principal / DDO</span>
    </td>
  </tr>
</table>

@include('pdf.partials.footer', [
  'footerNote' => 'Statutory Form 16 certificate generated by ' . ($school?->school_name ?? 'the Employer') . '.'
])
</body>
</html>
