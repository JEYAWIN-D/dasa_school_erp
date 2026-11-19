<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Attendance Shortage Notice — {{ $student->full_name }}</title>
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
    line-height: 1.6;
    margin: 0;
    padding: 0;
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
    width: 40%;
    color: #334155;
  }
  .warning-box {
    background: #fffbeb;
    border: 1px solid #fde68a;
    padding: 10px 12px;
    margin: 12px 0;
    border-radius: 4px;
    font-size: 9.5px;
    color: #92400e;
    line-height: 1.5;
  }
</style>
</head>
<body>
@include('pdf.partials.watermark')

@include('pdf.partials.header', [
  'title' => 'Official Attendance Shortage Advisory Notice',
  'subtitle' => 'Academic Session: ' . ($school?->academic_year ?? 'Current Session') . ' | Date: ' . now()->format('d/m/Y')
])

<div style="margin: 10px 0 8px 0; font-size: 10px; line-height: 1.4;">
  To,<br>
  <strong>{{ $student->father_name ?? $student->guardian_name ?? 'Parent / Guardian' }}</strong><br>
  Parent / Guardian of <strong>{{ $student->full_name }}</strong><br>
  @if($student->address) {{ $student->address }}<br> @endif
</div>

<p>Dear Parent / Guardian,</p>
<p>
  This official communication is to notify you that your ward <strong>{{ $student->full_name }}</strong>
  (Admission No: <strong>{{ $student->admission_no ?? $student->admission_number ?? '—' }}</strong>)
  has accumulated an attendance percentage below the mandatory institutional benchmark as detailed below:
</p>

<table class="details-table">
  <tr><td>Candidate Name</td><td><strong>{{ $student->full_name }}</strong></td></tr>
  <tr><td>Admission No.</td><td style="font-family: monospace;"><strong>{{ $student->admission_no ?? $student->admission_number ?? '—' }}</strong></td></tr>
  <tr><td>Class &amp; Section</td><td>{{ $enrollment?->class?->name ?? '—' }} {{ $enrollment?->section?->name ?? '' }}</td></tr>
  <tr><td>Total Prescribed Working Days</td><td>{{ $stats['total_days'] ?? 0 }} days</td></tr>
  <tr><td>Total Days Attended</td><td>{{ $stats['present'] ?? 0 }} days</td></tr>
  <tr><td>Total Days Absent</td><td>{{ $stats['absent'] ?? 0 }} days</td></tr>
  <tr><td>Current Attendance Percentage</td><td style="color: #dc2626; font-size: 11px;"><strong>{{ $stats['percentage'] ?? 0 }}%</strong></td></tr>
  <tr><td>Statutory Minimum Required</td><td><strong>75%</strong></td></tr>
</table>

<div class="warning-box">
  <strong>Regulatory Advisory:</strong> As per Board affiliation and Institutional Academic Regulations, a minimum of 75% attendance is mandatory to maintain eligibility for semester/annual examinations. Continued unexcused absence puts your ward at risk of debarment.
</div>

<p>
  You are requested to attend a parent-teacher consultation with the Class Teacher or Principal at the school administrative office at the earliest convenience to formalize attendance regularisation.
</p>

@include('pdf.partials.signatures', ['type' => 'academic'])

@include('pdf.partials.footer')
</body>
</html>
