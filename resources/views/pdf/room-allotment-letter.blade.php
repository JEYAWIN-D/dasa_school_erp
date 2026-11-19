<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Hostel Room Allotment Letter — {{ $student->full_name }}</title>
<style>
  @page {
    size: A4 portrait;
    margin: 12mm 15mm 16mm 15mm;
  }
  * { box-sizing: border-box; }
  body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 10px;
    color: #1e293b;
    margin: 0;
    padding: 0;
    line-height: 1.6;
  }
  p { line-height: 1.7; margin: 6px 0; }
  table.details-table {
    width: 100%;
    border-collapse: collapse;
    margin: 12px 0;
  }
  table.details-table td {
    padding: 5px 8px;
    border: 1px solid #cbd5e1;
    font-size: 9.5px;
  }
  table.details-table td:first-child {
    font-weight: bold;
    background: #f8fafc;
    width: 35%;
    color: #334155;
  }
  .guideline-box {
    background: #f0fdf4;
    border: 1px solid #86efac;
    padding: 8px 12px;
    border-radius: 4px;
    margin: 12px 0;
    font-size: 9px;
    color: #166534;
  }
</style>
</head>
<body>
@include('pdf.partials.watermark')

@include('pdf.partials.header', [
  'title' => 'Hostel Accommodation Allotment Order',
  'subtitle' => 'Academic Session: ' . ($school?->academic_year ?? 'Current Session') . ' | Date: ' . now()->format('d/m/Y')
])

<div style="margin: 8px 0; font-size: 10px; line-height: 1.4;">
  To,<br>
  <strong>{{ $student->father_name ?? $student->guardian_name ?? 'Parent / Guardian' }}</strong><br>
  Parent / Guardian of <strong>{{ $student->full_name }}</strong><br>
  @if($student->address) {{ $student->address }}<br> @endif
</div>

<p>Dear Parent / Guardian,</p>
<p>
  We are pleased to inform you that institutional hostel accommodation has been allotted to your ward
  <strong>{{ $student->full_name }}</strong> (Admission No: <strong>{{ $student->admission_no ?? $student->admission_number ?? '—' }}</strong>)
  under the following verified details:
</p>

<table class="details-table">
  <tr><td>Student Full Name</td><td><strong>{{ $student->full_name }}</strong></td></tr>
  <tr><td>Admission Number</td><td style="font-family: monospace;"><strong>{{ $student->admission_no ?? $student->admission_number ?? '—' }}</strong></td></tr>
  <tr><td>Class &amp; Section</td><td>{{ $enrollment?->class?->name ?? '—' }} {{ $enrollment?->section?->name ?? '' }}</td></tr>
  <tr><td>Assigned Hostel / Block</td><td><strong>{{ $allotment->room?->hostel?->name ?? 'Hostel Block' }}</strong></td></tr>
  <tr><td>Room Number</td><td style="font-weight: bold; font-size: 11px;">{{ $allotment->room?->room_number ?? '—' }}</td></tr>
  <tr><td>Effective Allotment Date</td><td>{{ $allotment->allotment_date ? \Carbon\Carbon::parse($allotment->allotment_date)->format('d/m/Y') : now()->format('d/m/Y') }}</td></tr>
  <tr><td>Approved Monthly Hostel Fee</td><td style="font-weight: bold;">₹{{ number_format($allotment->monthly_fee, 2) }}</td></tr>
</table>

<div class="guideline-box">
  <strong>Hostel Check-in Instructions:</strong> Please report to the Chief Warden on or before the allotment date with medical fitness certificate, 2 guardian passport photographs, and personal boarding articles. Contact: <strong>{{ $allotment->room?->hostel?->warden_mobile ?? 'Warden Office' }}</strong>.
</div>

<table style="width: 100%; border-collapse: collapse; border: none; margin-top: 35px; page-break-inside: avoid;">
  <tr>
    <td style="width: 50%; text-align: left; vertical-align: bottom; border: none;">
      <div style="border-top: 1px dashed #64748b; width: 140px; margin-bottom: 3px;"></div>
      <span style="font-size: 8.5px; font-weight: bold; color: #334155;">Hostel Warden / In-Charge</span>
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
