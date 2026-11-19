<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Letter of Appointment — {{ $employee->full_name }}</title>
<style>
  @page {
    size: A4 portrait;
    margin: 15mm 20mm 18mm 20mm;
  }
  * { box-sizing: border-box; }
  body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 10.5px;
    color: #1e293b;
    line-height: 1.7;
    background: #fff;
  }
  .meta-bar {
    width: 100%;
    margin-bottom: 16px;
    font-size: 9.5px;
    color: #475569;
    border-collapse: collapse;
  }
  .meta-bar td { padding: 0; border: none; }
  .recipient-box {
    margin-bottom: 16px;
    background: #f8fafc;
    padding: 10px 14px;
    border-left: 3.5px solid #1e293b;
    border-radius: 4px;
    font-size: 10px;
  }
  .recipient-name {
    font-size: 12px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 2px;
  }
  .body-text {
    margin-bottom: 12px;
    text-align: justify;
    line-height: 1.8;
  }
  .terms-table {
    width: 100%;
    border-collapse: collapse;
    margin: 14px 0;
    border: 1px solid #cbd5e1;
  }
  .terms-table th, .terms-table td {
    border: 1px solid #e2e8f0;
    padding: 6px 10px;
    font-size: 10px;
  }
  .terms-table th {
    background: #f8fafc;
    color: #334155;
    font-weight: 700;
    text-align: left;
    width: 35%;
  }
  .terms-table td {
    color: #0f172a;
    font-weight: 600;
  }
</style>
</head>
<body>
@include('pdf.partials.watermark')

@include('pdf.partials.header', [
  'title' => 'Official Letter of Employment Appointment'
])

<table class="meta-bar">
  <tr>
    <td style="text-align: left;"><strong>Ref No:</strong> {{ $refNumber ?? ('APT-' . date('Y') . '-' . str_pad($employee->id, 4, '0', STR_PAD_LEFT)) }}</td>
    <td style="text-align: right;"><strong>Date:</strong> {{ now()->format('F d, Y') }}</td>
  </tr>
</table>

<div class="recipient-box">
  <div class="recipient-name">To, {{ $employee->full_name }}</div>
  <div>Employee Code: <strong>{{ $employee->employee_code ?? $employee->employee_number ?? $employee->id }}</strong></div>
  @if($employee->residential_address ?? $employee->address)
    <div>{{ $employee->residential_address ?? $employee->address }}</div>
  @endif
  <div>
    @if($employee->mobile) Contact: +91 {{ $employee->mobile }} @endif
    @if($employee->official_email ?? $employee->email) &bull; Email: {{ $employee->official_email ?? $employee->email }} @endif
  </div>
</div>

<div class="body-text">
  Dear <strong>{{ $employee->full_name }}</strong>,
</div>

<div class="body-text">
  With reference to your application and the subsequent selection evaluation, the Management of <strong>{{ $school->school_name ?? config('app.name', 'Erode Public School') }}</strong> is pleased to appoint you to the faculty/administrative staff as <strong>{{ is_object($employee->designation) ? $employee->designation->name : ($employee->designation ?? 'Staff Member') }}</strong> in the Department of <strong>{{ is_object($employee->department) ? $employee->department->name : ($employee->department ?? 'General') }}</strong> under the following terms:
</div>

<table class="terms-table">
  <tr>
    <th>Candidate Full Name</th>
    <td>{{ $employee->full_name }}</td>
  </tr>
  <tr>
    <th>Assigned Employee Code</th>
    <td style="font-family: monospace;">{{ $employee->employee_code ?? $employee->employee_number ?? $employee->id }}</td>
  </tr>
  <tr>
    <th>Designation &amp; Role</th>
    <td>{{ is_object($employee->designation) ? $employee->designation->name : ($employee->designation ?? 'Staff Member') }}</td>
  </tr>
  <tr>
    <th>Staff Classification</th>
    <td>{{ $employee->category_label ?? 'Teaching Faculty' }}</td>
  </tr>
  <tr>
    <th>Effective Date of Joining</th>
    <td>{{ $employee->joining_date ? \Carbon\Carbon::parse($employee->joining_date)->format('F d, Y') : ($employee->date_of_joining ? \Carbon\Carbon::parse($employee->date_of_joining)->format('F d, Y') : now()->format('F d, Y')) }}</td>
  </tr>
  <tr>
    <th>Approved Basic Salary</th>
    <td>₹{{ number_format($employee->basic_salary > 0 ? $employee->basic_salary : 25000, 2) }} / month</td>
  </tr>
</table>

<div class="body-text">
  You will be governed by the standard institutional code of conduct, statutory regulations, and service norms of the institution as updated from time to time. Please sign and return the duplicate copy of this appointment order to the administrative office as formal acceptance of this offer.
</div>

<div class="body-text">
  We warmly welcome you to <strong>{{ $school->school_name ?? config('app.name', 'the Institution') }}</strong> and look forward to your valuable contribution toward our educational mission.
</div>

<table style="width: 100%; border-collapse: collapse; border: none; margin-top: 40px; page-break-inside: avoid;">
  <tr>
    <td style="width: 50%; text-align: left; vertical-align: bottom; border: none;">
      <div style="border-top: 1.5px solid #475569; width: 170px; margin-bottom: 4px;"></div>
      <div style="font-size: 9.5px; font-weight: bold; color: #0f172a;">Employee Acceptance</div>
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
