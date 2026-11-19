<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Progress Report Card - {{ $student->admission_no }}</title>
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
  .badge-pass {
    display: inline-block;
    background: #dcfce7;
    color: #166534;
    font-weight: bold;
    font-size: 8.5px;
    padding: 1px 6px;
    border-radius: 3px;
  }
  .badge-fail {
    display: inline-block;
    background: #fee2e2;
    color: #991b1b;
    font-weight: bold;
    font-size: 8.5px;
    padding: 1px 6px;
    border-radius: 3px;
  }
  .badge-absent {
    display: inline-block;
    background: #f1f5f9;
    color: #64748b;
    font-weight: bold;
    font-size: 8.5px;
    padding: 1px 6px;
    border-radius: 3px;
  }
  .grade-tag {
    font-weight: bold;
    color: #0f172a;
  }
</style>
</head>
<body>

@include('pdf.partials.watermark')

@include('pdf.partials.header', [
    'title' => 'STUDENT PROGRESS REPORT CARD',
    'subtitle' => 'Official Academic Performance Evaluation',
    'academicYear' => now()->year . '-' . (now()->year + 1),
    'date' => now()->format('d M Y')
])

<table class="meta-box">
  <tr>
    <td class="meta-lbl">Student Name:</td>
    <td class="meta-val">{{ $student->first_name }} {{ $student->last_name }}</td>
    <td class="meta-lbl">Admission No:</td>
    <td class="meta-val">{{ $student->admission_no }}</td>
  </tr>
  <tr>
    <td class="meta-lbl">Class:</td>
    <td class="meta-val">{{ $enrollment?->class_name ?? '—' }}</td>
    <td class="meta-lbl">Section:</td>
    <td class="meta-val">{{ $enrollment?->section_name ?? '—' }}</td>
  </tr>
  @if($student->dob ?? null)
  <tr>
    <td class="meta-lbl">Date of Birth:</td>
    <td class="meta-val" colspan="3">{{ \Carbon\Carbon::parse($student->dob)->format('d M Y') }}</td>
  </tr>
  @endif
</table>

@if($results->isEmpty())
  <div style="text-align:center; padding:30px; color:#64748b; border:1px dashed #cbd5e1; border-radius:4px; margin:20px 0;">
    No evaluated examination records found for this academic term.
  </div>
@else
  @foreach($results as $examName => $marks)
  @php
    $totalObtained = $marks->sum('marks_obtained');
    $totalMax = $marks->sum('max_marks');
    $totalPct = $totalMax > 0 ? round($totalObtained / $totalMax * 100, 1) : 0;
  @endphp
  
  <div class="exam-banner">
    <table style="width:100%; border-collapse:collapse; color:#fff;">
      <tr>
        <td style="border:none; padding:0; text-align:left; font-weight:bold;">{{ strtoupper($examName) }}</td>
        @if($marks->first()->exam_date ?? null)
        <td style="border:none; padding:0; text-align:right; font-size:9px; font-weight:normal;">
          Date: {{ \Carbon\Carbon::parse($marks->first()->exam_date)->format('d M Y') }}
        </td>
        @endif
      </tr>
    </table>
  </div>

  <table class="exam-table">
    <thead>
      <tr>
        <th style="width: 8%; text-align:center;">#</th>
        <th>Subject Name</th>
        <th style="width: 18%; text-align:center;">Max Marks</th>
        <th style="width: 18%; text-align:center;">Marks Obtained</th>
        <th style="width: 14%; text-align:center;">Grade</th>
        <th style="width: 16%; text-align:center;">Result</th>
      </tr>
    </thead>
    <tbody>
      @foreach($marks as $idx => $mark)
      <tr>
        <td style="text-align:center; color:#64748b;">{{ $idx + 1 }}</td>
        <td style="font-weight:600;">{{ $mark->subject_name }}</td>
        <td style="text-align:center;">{{ $mark->max_marks }}</td>
        <td style="text-align:center; font-weight:bold;">{{ $mark->marks_obtained }}</td>
        <td style="text-align:center;" class="grade-tag">{{ $mark->grade ?? '—' }}</td>
        <td style="text-align:center;">
          @if($mark->pass_status === 'Pass')
            <span class="badge-pass">PASS</span>
          @elseif($mark->pass_status === 'Absent')
            <span class="badge-absent">ABSENT</span>
          @else
            <span class="badge-fail">FAIL</span>
          @endif
        </td>
      </tr>
      @endforeach
      <tr style="background:#f8fafc; font-weight:bold;">
        <td colspan="2" style="text-align:right; text-transform:uppercase; font-size:9px; color:#475569;">Summary Total:</td>
        <td style="text-align:center;">{{ $totalMax }}</td>
        <td style="text-align:center; color:#0f172a;">{{ $totalObtained }}</td>
        <td colspan="2" style="text-align:center; color:#166534;">
          {{ $totalPct }}%
        </td>
      </tr>
    </tbody>
  </table>
  @endforeach
@endif

@include('pdf.partials.signatures', ['mode' => 'academic'])

@include('pdf.partials.footer', ['computerGenerated' => true])

</body>
</html>
