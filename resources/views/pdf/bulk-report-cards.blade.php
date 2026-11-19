<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Bulk Student Report Cards</title>
<style>
  @page {
    size: A4 portrait;
    margin: 10mm 14mm 14mm 14mm;
  }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, sans-serif; font-size: 9.5px; color: #1e293b; }
  .card-page {
    page-break-after: always;
    position: relative;
    padding-bottom: 20px;
  }
  .card-page:last-child { page-break-after: auto; }
  table.info-strip {
    width: 100%;
    border-collapse: collapse;
    margin: 8px 0 12px 0;
    border: 1px solid #cbd5e1;
    background: #f8fafc;
  }
  table.info-strip td {
    padding: 5px 8px;
    font-size: 9.5px;
    border-right: 1px solid #cbd5e1;
  }
  table.info-strip td:last-child { border-right: none; }
  table.marks-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 12px;
  }
  table.marks-table th {
    background: #1e293b;
    color: white;
    padding: 5px 8px;
    font-size: 8.5px;
    text-transform: uppercase;
    font-weight: bold;
    border: 1px solid #334155;
    text-align: center;
  }
  table.marks-table th.left { text-align: left; }
  table.marks-table td {
    padding: 4.5px 8px;
    border: 1px solid #cbd5e1;
    font-size: 9px;
    text-align: center;
  }
  table.marks-table td.left { text-align: left; }
  table.marks-table tr:nth-child(even) td { background: #f8fafc; }
  .fail { color: #dc2626; font-weight: bold; }
  table.summary-bar {
    width: 100%;
    border-collapse: collapse;
    background: #0f172a;
    color: white;
    border-radius: 4px;
    margin-bottom: 15px;
  }
  table.summary-bar td {
    padding: 8px 10px;
    text-align: center;
    border-right: 1px solid #334155;
  }
  table.summary-bar td:last-child { border-right: none; }
  .summary-num { font-size: 16px; font-weight: bold; font-family: monospace; }
  .summary-lbl { font-size: 7.5px; opacity: 0.85; text-transform: uppercase; margin-top: 2px; }
</style>
</head>
<body>
@foreach($reportData as $row)
<div class="card-page">
  @include('pdf.partials.watermark')

  @include('pdf.partials.header', [
    'title' => 'Student Academic Progress Report Card',
    'subtitle' => ($exam->name ?? 'Standard Assessment') . ' | Academic Session: ' . ($school?->academic_year ?? 'Current Session')
  ])

  <table class="info-strip">
    <tr>
      <td style="width: 35%;">Candidate Name: <strong>{{ $row['enrollment']->student->full_name ?? ($row['enrollment']->student->first_name . ' ' . $row['enrollment']->student->last_name) }}</strong></td>
      <td style="width: 25%;">Adm No: <strong style="font-family: monospace;">{{ $row['enrollment']->student->admission_no ?? $row['enrollment']->student->admission_number ?? '—' }}</strong></td>
      <td style="width: 20%;">Class: <strong>{{ $row['enrollment']->class->name ?? '' }}</strong></td>
      <td style="width: 20%;">Roll No: <strong>{{ $row['enrollment']->roll_number ?? '—' }}</strong></td>
    </tr>
  </table>

  <table class="marks-table">
    <thead>
      <tr>
        <th class="left">Subject</th>
        <th style="width: 75px;">Max Marks</th>
        <th style="width: 85px;">Marks Obtained</th>
        <th style="width: 75px;">Pass Marks</th>
        <th style="width: 65px;">Status</th>
        <th style="width: 65px;">Grade</th>
      </tr>
    </thead>
    <tbody>
      @foreach($row['marks'] as $mark)
      @php
        $passMarks  = $mark->examSchedule?->pass_marks ?? 35;
        $maxMarks   = $mark->examSchedule?->max_marks ?? 100;
        $isPassed   = $mark->is_absent ? false : $mark->marks_obtained >= $passMarks;
        $pct        = $maxMarks > 0 ? ($mark->marks_obtained / $maxMarks * 100) : 0;
        $grade = '—';
        if ($gradingScheme && !$mark->is_absent) {
            foreach ($gradingScheme->ranges->sortByDesc('min_percentage') as $range) {
                if ($pct >= $range->min_percentage) { $grade = $range->grade; break; }
            }
        }
      @endphp
      <tr>
        <td class="left"><strong>{{ $mark->examSchedule?->subject?->name ?? '—' }}</strong></td>
        <td>{{ $maxMarks }}</td>
        <td class="{{ !$isPassed && !$mark->is_absent ? 'fail' : '' }}">
          <strong>{{ $mark->is_absent ? 'Absent' : $mark->marks_obtained }}</strong>
        </td>
        <td>{{ $passMarks }}</td>
        <td class="{{ $isPassed ? '' : 'fail' }}">{{ $mark->is_absent ? 'AB' : ($isPassed ? 'Pass' : 'Fail') }}</td>
        <td><strong>{{ $grade }}</strong></td>
      </tr>
      @endforeach
    </tbody>
  </table>

  @php
    $percentage = $row['percentage'];
    $overallGrade = '—';
    if ($gradingScheme) {
        foreach ($gradingScheme->ranges->sortByDesc('min_percentage') as $range) {
            if ($percentage >= $range->min_percentage) { $overallGrade = $range->grade; break; }
        }
    }
  @endphp

  <table class="summary-bar">
    <tr>
      <td>
        <div class="summary-num">{{ $row['obtained'] }}</div>
        <div class="summary-lbl">Marks Obtained</div>
      </td>
      <td>
        <div class="summary-num">{{ $row['totalMarks'] }}</div>
        <div class="summary-lbl">Total Max Marks</div>
      </td>
      <td>
        <div class="summary-num">{{ $percentage }}%</div>
        <div class="summary-lbl">Percentage</div>
      </td>
      <td>
        <div class="summary-num">{{ $overallGrade }}</div>
        <div class="summary-lbl">Overall Grade</div>
      </td>
      <td>
        <div class="summary-num" style="color: {{ $row['hasFail'] ? '#fca5a5' : '#86efac' }};">
          {{ $row['hasFail'] ? 'FAIL' : 'PASS' }}
        </div>
        <div class="summary-lbl">Result Outcome</div>
      </td>
    </tr>
  </table>

  <table style="width: 100%; border-collapse: collapse; border: none; margin-top: 30px; page-break-inside: avoid;">
    <tr>
      <td style="width: 33%; text-align: left; vertical-align: bottom; border: none;">
        <div style="border-top: 1px dashed #64748b; width: 120px; margin-bottom: 3px;"></div>
        <span style="font-size: 8px; font-weight: bold; color: #334155;">Class Teacher</span>
      </td>
      <td style="width: 34%; text-align: center; vertical-align: bottom; border: none;">
        <div style="border-top: 1px dashed #64748b; width: 120px; margin: 0 auto 3px auto;"></div>
        <span style="font-size: 8px; font-weight: bold; color: #334155;">Parent / Guardian</span>
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
        <span style="font-size: 8px; font-weight: bold; color: #334155;">Principal</span>
      </td>
    </tr>
  </table>

  @include('pdf.partials.footer')
</div>
@endforeach
</body>
</html>
