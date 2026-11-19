<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Salary Increment Order — {{ $employee->full_name ?? ($employee->first_name . ' ' . $employee->last_name) }}</title>
<style>
  @page {
    size: A4 portrait;
    margin: 15mm 20mm 18mm 20mm;
  }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 10.5px;
    color: #1e293b;
    line-height: 1.8;
  }
  .ref-table {
    width: 100%;
    border-collapse: collapse;
    margin: 10px 0 16px 0;
    font-size: 9.5px;
    color: #475569;
  }
  .ref-table td { padding: 0; border: none; }
  p { margin-bottom: 12px; text-align: justify; line-height: 1.8; }
  table.info-table {
    width: 100%;
    border-collapse: collapse;
    margin: 14px 0;
    border: 1px solid #cbd5e1;
  }
  table.info-table td {
    padding: 6px 10px;
    border: 1px solid #e2e8f0;
    font-size: 10px;
  }
  table.info-table td:first-child {
    background: #f8fafc;
    font-weight: 600;
    width: 40%;
    color: #334155;
  }
  .highlight-box {
    background: #f0fdf4;
    border: 1.5px solid #86efac;
    border-radius: 4px;
    padding: 8px 14px;
    margin: 14px 0;
    font-weight: bold;
    font-size: 11px;
    color: #166534;
  }
</style>
</head>
<body>
@include('pdf.partials.watermark')

@include('pdf.partials.header', [
  'title' => 'Annual Compensation Revision Order'
])

<table class="ref-table">
  <tr>
    <td style="text-align: left;"><strong>Ref No:</strong> {{ $refNumber ?? ('INC-' . date('Y') . '-' . str_pad($employee->id, 4, '0', STR_PAD_LEFT)) }}</td>
    <td style="text-align: right;"><strong>Date of Issue:</strong> {{ now()->format('d F Y') }}</td>
  </tr>
</table>

<div style="margin-bottom: 14px; line-height: 1.5;">
  To,<br>
  <strong>{{ $employee->full_name ?? ($employee->first_name . ' ' . $employee->last_name) }}</strong><br>
  {{ is_object($employee->designation) ? $employee->designation->name : ($employee->designation ?? 'Faculty') }},
  {{ is_object($employee->department) ? $employee->department->name : ($employee->department ?? 'General') }}
</div>

<p>Dear {{ $employee->first_name ?? $employee->full_name }},</p>

<p>
  The Management and Governing Body of <strong>{{ $school->school_name ?? config('app.name', 'Erode Public School') }}</strong> take pleasure in placing on record our appreciation of your dedication and performance. In accordance with the annual appraisal cycle, we are pleased to revise your monthly compensation as outlined below:
</p>

<table class="info-table">
  <tr><td>Employee Full Name</td><td><strong>{{ $employee->full_name ?? ($employee->first_name . ' ' . $employee->last_name) }}</strong></td></tr>
  <tr><td>Employee Code</td><td style="font-family: monospace;"><strong>{{ $employee->employee_code ?? $employee->employee_number ?? $employee->id }}</strong></td></tr>
  <tr><td>Designation</td><td>{{ is_object($employee->designation) ? $employee->designation->name : ($employee->designation ?? '—') }}</td></tr>
  <tr><td>Department</td><td>{{ is_object($employee->department) ? $employee->department->name : ($employee->department ?? '—') }}</td></tr>
  <tr><td>Joining Date</td><td>{{ $employee->joining_date ? \Carbon\Carbon::parse($employee->joining_date)->format('d M Y') : ($employee->date_of_joining ? \Carbon\Carbon::parse($employee->date_of_joining)->format('d M Y') : '—') }}</td></tr>
  <tr><td>Existing Basic Salary</td><td>₹{{ number_format($employee->basic_salary ?? 0, 2) }} / month</td></tr>
  <tr><td>Revised Basic Salary</td><td style="color: #166534; font-weight: bold;">₹{{ number_format($newSalary, 2) }} / month</td></tr>
  <tr><td>Effective Revision Date</td><td><strong>{{ now()->addMonth()->startOfMonth()->format('d M Y') }}</strong></td></tr>
</table>

<div class="highlight-box">
  Net Monthly Increment: ₹{{ number_format($newSalary - ($employee->basic_salary ?? 0), 2) }} per month
</div>

<p>
  All other service terms, conditions of appointment, and institutional policies shall continue unchanged. Please sign the acknowledgement duplicate below and submit it to the administrative office for payroll records.
</p>

<table style="width: 100%; border-collapse: collapse; border: none; margin-top: 40px; page-break-inside: avoid;">
  <tr>
    <td style="width: 50%; text-align: left; vertical-align: bottom; border: none;">
      <div style="border-top: 1.5px solid #475569; width: 170px; margin-bottom: 4px;"></div>
      <div style="font-size: 9.5px; font-weight: bold; color: #0f172a;">Employee Acknowledgement</div>
      <div style="font-size: 8px; color: #64748b;">Signature &amp; Date</div>
    </td>
    <td style="width: 50%; text-align: right; vertical-align: bottom; border: none;">
      @php
        $sigBase64 = null;
        if (!empty($school?->principal_signature) && \Illuminate\Support\Facades\Storage::disk('public')->exists($school->principal_signature)) {
            $sigBase64 = 'data:image/png;base64,' . base64_encode(\Illuminate\Support\Facades\Storage::disk('public')->get($school->principal_signature));
        }
      @endphp
      @if($sigBase64)
        <img src="{{ $sigBase64 }}" alt="Signature" style="height: 30px; display: inline-block; margin-bottom: 2px;">
      @endif
      <div style="border-top: 1.5px solid #475569; width: 170px; margin-left: auto; margin-bottom: 4px;"></div>
      <div style="font-size: 9.5px; font-weight: bold; color: #0f172a;">Principal / Director</div>
      <div style="font-size: 8px; color: #64748b;">{{ $school->school_name ?? 'Management' }}</div>
    </td>
  </tr>
</table>

@include('pdf.partials.footer')
</body>
</html>
