<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Hall Ticket — {{ $student->full_name }}</title>
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
  }
  .info-table {
    width: 100%;
    border-collapse: collapse;
    margin: 8px 0 12px 0;
  }
  .info-table td {
    padding: 5px 8px;
    border: 1px solid #cbd5e1;
    font-size: 9.5px;
  }
  .info-table td:first-child {
    font-weight: bold;
    background: #f8fafc;
    width: 25%;
    color: #334155;
  }
  .schedule-table {
    width: 100%;
    border-collapse: collapse;
    margin: 8px 0 12px 0;
  }
  .schedule-table th {
    background: #1e293b;
    color: white;
    padding: 6px 8px;
    text-align: left;
    font-size: 9px;
    text-transform: uppercase;
    font-weight: bold;
    border: 1px solid #334155;
  }
  .schedule-table td {
    padding: 5px 7px;
    border: 1px solid #cbd5e1;
    font-size: 9px;
  }
  .schedule-table tr:nth-child(even) td {
    background: #f8fafc;
  }
  .instructions-box {
    background: #fffbeb;
    border: 1px solid #fde68a;
    padding: 8px 12px;
    font-size: 9px;
    color: #92400e;
    border-radius: 4px;
    margin-top: 10px;
    line-height: 1.4;
  }
</style>
</head>
<body>
@include('pdf.partials.watermark')

@include('pdf.partials.header', [
  'title' => 'Official Examination Hall Ticket',
  'subtitle' => ($exam->name ?? 'Examination') . ' | Academic Year: ' . ($academicYear->name ?? 'Current Session')
])

<table style="width: 100%; border-collapse: collapse; border: none; margin-bottom: 8px;">
  <tr>
    <td style="vertical-align: top; border: none; width: 75%; padding: 0;">
      <table class="info-table" style="margin: 0;">
        <tr>
          <td>Candidate Name</td>
          <td style="font-weight: bold; font-size: 10.5px;">{{ $student->full_name }}</td>
        </tr>
        <tr>
          <td>Admission No</td>
          <td style="font-weight: bold; font-family: monospace;">{{ $student->admission_no ?? $student->admission_number ?? '—' }}</td>
        </tr>
        <tr>
          <td>Class / Section</td>
          <td>{{ $enrollment?->class?->name ?? '—' }} &bull; Section {{ $enrollment?->section?->name ?? 'A' }}</td>
        </tr>
        <tr>
          <td>Roll Number</td>
          <td><strong>{{ $enrollment?->roll_number ?? '—' }}</strong></td>
        </tr>
        <tr>
          <td>Date of Birth</td>
          <td>{{ $student->dob ? \Carbon\Carbon::parse($student->dob)->format('d/m/Y') : '—' }}</td>
        </tr>
      </table>
    </td>
    <td style="vertical-align: top; text-align: center; border: none; width: 25%; padding-left: 14px;">
      @if(isset($qrCode))
        <img src="data:image/png;base64,{{ $qrCode }}" alt="QR Code" width="85" height="85" style="border: 1px solid #cbd5e1; padding: 3px; display: inline-block;">
        <p style="font-size: 7.5px; color: #64748b; margin-top: 3px;">Scan to Verify</p>
      @elseif(!empty($student->photo))
        <img src="{{ public_path('storage/' . $student->photo) }}" alt="Photo" style="width: 75px; height: 90px; object-fit: cover; border: 1px solid #cbd5e1; display: inline-block;">
      @else
        <div style="width: 75px; height: 90px; border: 1px dashed #cbd5e1; margin: 0 auto; display: flex; align-items: center; justify-content: center; font-size: 8px; color: #94a3b8; text-align: center; padding-top: 30px;">
          Candidate Photo
        </div>
      @endif
    </td>
  </tr>
</table>

<h4 style="margin: 10px 0 4px 0; font-size: 10.5px; text-transform: uppercase; letter-spacing: 0.5px; color: #0f172a;">Examination Timetable &amp; Schedule</h4>

<table class="schedule-table">
  <thead>
    <tr>
      <th style="width: 80px;">Date</th>
      <th>Subject</th>
      <th style="width: 105px;">Time</th>
      <th style="width: 70px;">Duration</th>
      <th style="width: 65px;">Max Marks</th>
      <th style="width: 85px;">Venue</th>
    </tr>
  </thead>
  <tbody>
    @foreach($schedules as $s)
    <tr>
      <td style="font-weight: 600;">{{ \Carbon\Carbon::parse($s->exam_date)->format('d M Y') }}</td>
      <td style="font-weight: bold; color: #0f172a;">{{ $s->subject?->name ?? '—' }}</td>
      <td>{{ $s->start_time }} – {{ $s->end_time }}</td>
      <td>{{ $s->duration_minutes ?? 180 }} mins</td>
      <td>{{ $s->max_marks }}</td>
      <td>{{ $s->venue ?? 'Examination Hall' }}</td>
    </tr>
    @endforeach
  </tbody>
</table>

<div class="instructions-box">
  <strong>Important Examination Guidelines:</strong>
  <ul style="margin: 4px 0 0 16px; padding: 0;">
    <li>Candidate must bring this original Hall Ticket and School ID card to each examination session.</li>
    <li>Report to the assigned examination hall at least 20 minutes prior to commencement.</li>
    <li>Mobile phones, smartwatches, calculators, and unauthorized paper materials are strictly prohibited inside the hall.</li>
  </ul>
</div>

<table style="width: 100%; border-collapse: collapse; border: none; margin-top: 35px;">
  <tr>
    <td style="width: 33%; text-align: left; vertical-align: bottom; border: none;">
      <div style="border-top: 1px dashed #64748b; width: 120px; margin-bottom: 3px;"></div>
      <span style="font-size: 8.5px; font-weight: bold; color: #334155;">Candidate Signature</span>
    </td>
    <td style="width: 34%; text-align: center; vertical-align: bottom; border: none;">
      <div style="border-top: 1px dashed #64748b; width: 120px; margin: 0 auto 3px auto;"></div>
      <span style="font-size: 8.5px; font-weight: bold; color: #334155;">Exam Coordinator</span>
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
      <span style="font-size: 8.5px; font-weight: bold; color: #334155;">Principal / Headmaster</span>
    </td>
  </tr>
</table>

@include('pdf.partials.footer')
</body>
</html>
