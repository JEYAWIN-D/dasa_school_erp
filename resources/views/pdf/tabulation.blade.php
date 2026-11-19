<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Examination Tabulation Sheet — {{ $class->name }}</title>
<style>
  @page {
    size: A4 landscape;
    margin: 8mm 10mm 12mm 10mm;
  }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 8px;
    color: #1e293b;
  }
  table.tab-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 6px;
    margin-bottom: 10px;
  }
  table.tab-table th {
    background: #1e293b;
    color: white;
    padding: 4px 5px;
    font-size: 7.5px;
    text-align: center;
    border: 1px solid #475569;
    text-transform: uppercase;
  }
  table.tab-table td {
    padding: 3.5px 5px;
    border: 1px solid #cbd5e1;
    font-size: 8px;
    text-align: center;
  }
  .name-col { text-align: left !important; font-weight: 600; white-space: nowrap; }
  tr:nth-child(even) td { background: #f8fafc; }
  .fail { color: #dc2626; font-weight: bold; }
  .top3 { background: #fef9c3 !important; }
  .summary-strip {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 8px;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
  }
  .summary-strip td {
    padding: 4px 8px;
    font-size: 8px;
    font-weight: 600;
    color: #334155;
    border: none;
  }
</style>
</head>
<body>
@include('pdf.partials.watermark')

@include('pdf.partials.header', [
  'title' => 'Official Examination Tabulation Register & Result Master',
  'subtitle' => 'Exam: ' . ($exam->name ?? 'Standard') . ' | Class: ' . ($class->name ?? '—') . ' | Academic Session: ' . ($school?->academic_year ?? 'Current Session')
])

@php
  $totalCount = $enrollments->count();
  $failedCount = collect($enrollments)->filter(fn($e) => collect($marksMap[$e->student_id] ?? [])->contains(fn($m) => !$m->is_absent && $m->marks_obtained < ($m->examSchedule?->pass_marks ?? 35)))->count();
  $passedCount = $totalCount - $failedCount;
  $classAvg = $totalCount > 0 ? round(collect($totals)->avg(), 1) : 0;
@endphp

<table class="summary-strip">
  <tr>
    <td>Total Candidates: <strong>{{ $totalCount }}</strong></td>
    <td style="color: #166534;">Passed: <strong>{{ $passedCount }}</strong></td>
    <td style="color: #dc2626;">Failed / Compartment: <strong>{{ $failedCount }}</strong></td>
    <td>Pass Rate: <strong>{{ $totalCount > 0 ? round(($passedCount / $totalCount) * 100, 1) : 0 }}%</strong></td>
    <td>Class Average Score: <strong>{{ $classAvg }}</strong></td>
  </tr>
</table>

<table class="tab-table">
  <thead>
    <tr>
      <th style="width: 20px;">#</th>
      <th style="width: 75px;">Adm No</th>
      <th class="name-col" style="min-width: 110px;">Candidate Name</th>
      @foreach($subjects as $subject)
        <th>{{ Str::limit($subject->name, 10) }}<br><span style="font-size: 6.5px; opacity: 0.85;">(Max: {{ $subject->pivot->max_marks ?? 100 }})</span></th>
      @endforeach
      <th style="width: 45px;">Total</th>
      <th style="width: 38px;">%</th>
      <th style="width: 35px;">Grade</th>
      <th style="width: 32px;">Rank</th>
      <th style="width: 35px;">Result</th>
    </tr>
  </thead>
  <tbody>
    @foreach($enrollments as $enrollment)
    @php
      $rowMarks   = $marksMap[$enrollment->student_id] ?? [];
      $total      = $totals[$enrollment->id] ?? 0;
      $maxTotal   = $subjects->sum(fn($s) => $s->pivot->max_marks ?? 100);
      $percentage = $maxTotal > 0 ? round($total / $maxTotal * 100, 1) : 0;
      $hasFail    = false;
      foreach ($rowMarks as $m) {
          if (!$m->is_absent && $m->marks_obtained < ($m->examSchedule?->pass_marks ?? 35)) { $hasFail = true; break; }
      }
      $grade = '—';
      if ($gradingScheme) {
          foreach ($gradingScheme->ranges->sortByDesc('min_percentage') as $range) {
              if ($percentage >= $range->min_percentage) { $grade = $range->grade; break; }
          }
      }
      $rank = $ranks[$enrollment->id] ?? '—';
    @endphp
    <tr class="{{ is_numeric($rank) && $rank <= 3 ? 'top3' : '' }}">
      <td>{{ $loop->iteration }}</td>
      <td style="font-family: monospace;">{{ $enrollment->student?->admission_no ?? $enrollment->student?->admission_number ?? '—' }}</td>
      <td class="name-col">{{ $enrollment->student?->full_name ?: ($enrollment->student?->first_name . ' ' . $enrollment->student?->last_name) }}</td>
      @foreach($subjects as $subject)
        @php $m = $rowMarks[$subject->id] ?? null; @endphp
        <td class="{{ $m && !$m->is_absent && $m->marks_obtained < ($m->examSchedule?->pass_marks ?? 35) ? 'fail' : '' }}">
          {{ $m ? ($m->is_absent ? 'AB' : $m->marks_obtained) : '—' }}
        </td>
      @endforeach
      <td><strong>{{ $total }}</strong></td>
      <td>{{ $percentage }}%</td>
      <td><strong>{{ $grade }}</strong></td>
      <td><strong>{{ $rank }}</strong></td>
      <td class="{{ $hasFail ? 'fail' : '' }}">
        <strong style="color: {{ $hasFail ? '#dc2626' : '#16a34a' }};">{{ $hasFail ? 'FAIL' : 'PASS' }}</strong>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>

@include('pdf.partials.signatures', ['type' => 'academic'])

@include('pdf.partials.footer')
</body>
</html>
