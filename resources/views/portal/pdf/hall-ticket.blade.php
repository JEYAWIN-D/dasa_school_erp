<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Examination Hall Ticket - {{ $student->admission_no }}</title>
<style>
  @page {
    size: A4 portrait;
    margin: 12mm 15mm 16mm 15mm;
  }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 10px;
    color: #1e293b;
    line-height: 1.45;
  }
  table.meta-box {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 12px;
    border: 1px solid #cbd5e1;
    background: #f8fafc;
  }
  table.meta-box td {
    padding: 6px 10px;
    border: 1px solid #e2e8f0;
    font-size: 9.5px;
  }
  .meta-lbl {
    color: #64748b;
    font-size: 9px;
    text-transform: uppercase;
    width: 18%;
  }
  .meta-val {
    color: #0f172a;
    font-weight: bold;
    width: 32%;
  }
  .exam-banner {
    background: #1e293b;
    color: #ffffff;
    padding: 5px 8px;
    font-size: 10px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin: 12px 0 6px 0;
    border-radius: 2px;
  }
  table.exam-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 10px;
  }
  table.exam-table th {
    background: #f1f5f9;
    color: #334155;
    font-size: 9px;
    font-weight: bold;
    text-transform: uppercase;
    padding: 6px 8px;
    border: 1px solid #cbd5e1;
  }
  table.exam-table td {
    padding: 6px 8px;
    border: 1px solid #e2e8f0;
    font-size: 9.5px;
  }
  .instructions {
    margin-top: 14px;
    padding: 8px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    background: #fffbeb;
    font-size: 8.5px;
    color: #92400e;
  }
  .instructions ol {
    margin: 4px 0 0 16px;
    padding: 0;
  }
  .instructions li {
    margin-bottom: 3px;
  }
</style>
</head>
<body>

@include('pdf.partials.watermark')

@include('pdf.partials.header', [
    'title' => 'EXAMINATION HALL TICKET / ADMIT CARD',
    'subtitle' => 'Official Exam Entry Clearance',
    'date' => now()->format('d M Y')
])

<table class="meta-box">
  <tr>
    <td class="meta-lbl">Candidate Name:</td>
    <td class="meta-val">{{ $student->first_name ?? '' }} {{ $student->last_name ?? '' }}</td>
    <td class="meta-lbl">Admission No:</td>
    <td class="meta-val">{{ $student->admission_no }}</td>
  </tr>
  <tr>
    <td class="meta-lbl">Class & Section:</td>
    <td class="meta-val">{{ $enrollment?->class_name ?? '—' }} {{ $enrollment?->section_name ?? '' }}</td>
    <td class="meta-lbl">Roll Number:</td>
    <td class="meta-val">{{ $student->roll_number ?? '—' }}</td>
  </tr>
</table>

@forelse($upcoming as $examName => $schedules)
  <div class="exam-banner">{{ strtoupper($examName) }}</div>
  <table class="exam-table">
    <thead>
      <tr>
        <th style="width: 8%; text-align:center;">#</th>
        <th>Subject Name</th>
        <th style="width: 20%; text-align:center;">Date</th>
        <th style="width: 22%; text-align:center;">Timing</th>
        <th style="width: 15%; text-align:center;">Max Marks</th>
        <th style="width: 15%; text-align:center;">Invigilator Sign</th>
      </tr>
    </thead>
    <tbody>
      @foreach($schedules as $idx => $s)
      <tr>
        <td style="text-align:center; color:#64748b;">{{ $idx + 1 }}</td>
        <td style="font-weight:600;">{{ $s->subject_name }}</td>
        <td style="text-align:center;">{{ \Carbon\Carbon::parse($s->exam_date)->format('d M Y') }}</td>
        <td style="text-align:center;">{{ $s->start_time }} – {{ $s->end_time }}</td>
        <td style="text-align:center; font-weight:bold;">{{ $s->total_marks }}</td>
        <td style="text-align:center; border-bottom:1px solid #cbd5e1;">&nbsp;</td>
      </tr>
      @endforeach
    </tbody>
  </table>
@empty
  <div style="text-align:center; padding:30px; color:#64748b; border:1px dashed #cbd5e1; border-radius:4px; margin:20px 0;">
    No scheduled examinations found for the active term.
  </div>
@endforelse

<div class="instructions">
  <strong>IMPORTANT EXAMINATION GUIDELINES:</strong>
  <ol>
    <li>Candidates must carry this Hall Ticket and their official School ID card to each examination session.</li>
    <li>Entry to the examination hall closes 15 minutes after the scheduled start time.</li>
    <li>Electronic devices, smartwatches, study materials, and loose papers are strictly prohibited in the exam hall.</li>
    <li>Candidates must occupy only the seat allotted against their roll number.</li>
  </ol>
</div>

@include('pdf.partials.signatures', ['mode' => 'academic'])

@include('pdf.partials.footer', ['computerGenerated' => true])

</body>
</html>
