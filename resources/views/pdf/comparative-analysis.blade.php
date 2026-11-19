<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Comparative Academic Analysis — {{ $exam->name }}</title>
<style>
  @page {
    size: A4 landscape;
    margin: 10mm 12mm 14mm 12mm;
  }
  body { font-family: Arial, sans-serif; font-size: 8.5px; color: #1e293b; margin: 0; padding: 0; }
  table.comp-table { width: 100%; border-collapse: collapse; margin-top: 8px; margin-bottom: 12px; }
  table.comp-table th { background: #1e293b; color: #fff; padding: 5px 6px; text-align: center; font-size: 8px; text-transform: uppercase; border: 1px solid #475569; }
  table.comp-table th.left { text-align: left; }
  table.comp-table td { padding: 4px 6px; border: 1px solid #cbd5e1; font-size: 8.5px; text-align: center; }
  table.comp-table td.left { text-align: left; }
  table.comp-table tr:nth-child(even) td { background: #f8fafc; }
  .above { color: #16a34a; font-weight: bold; }
  .below { color: #dc2626; font-weight: bold; }
  .school-row td { background: #f1f5f9 !important; font-weight: bold !important; color: #0f172a !important; border-top: 2px solid #0f172a !important; }
</style>
</head>
<body>
@include('pdf.partials.watermark')

@include('pdf.partials.header', [
  'title' => 'Cross-Grade Comparative Academic Analysis & Benchmark Matrix',
  'subtitle' => 'Exam: ' . ($exam->name ?? 'Assessment') . ' | Institutional Mean: ' . ($schoolAvg ?? '—') . '% | Academic Session: ' . ($school?->academic_year ?? 'Current Session')
])

<table class="comp-table">
  <thead>
    <tr>
      <th class="left" style="width: 120px;">Grade / Class</th>
      <th style="width: 85px;">Overall Avg</th>
      <th style="width: 90px;">vs School Mean</th>
      @foreach(array_keys($subjectSchoolAvg) as $subj)
        <th>{{ $subj }}</th>
      @endforeach
    </tr>
  </thead>
  <tbody>
    @foreach($classData as $row)
    @php $diff = round($row['overall_avg'] - ($schoolAvg ?? 0), 1); @endphp
    <tr>
      <td class="left"><strong>{{ $row['class']->name }}</strong></td>
      <td><strong>{{ $row['overall_avg'] }}%</strong></td>
      <td class="{{ $diff >= 0 ? 'above' : 'below' }}">{{ $diff >= 0 ? '+' : '' }}{{ $diff }}%</td>
      @foreach(array_keys($subjectSchoolAvg) as $subj)
      @php
        $cAvg = $row['subject_avgs'][$subj] ?? null;
        $sAvg = $subjectSchoolAvg[$subj];
      @endphp
      <td class="{{ $cAvg !== null ? ($cAvg >= $sAvg ? 'above' : 'below') : '' }}">
        {{ $cAvg ?? '—' }}
      </td>
      @endforeach
    </tr>
    @endforeach
    @if(!empty($subjectSchoolAvg))
    <tr class="school-row">
      <td class="left">INSTITUTIONAL BENCHMARK (MEAN)</td>
      <td>{{ $schoolAvg ?? '—' }}%</td>
      <td>—</td>
      @foreach($subjectSchoolAvg as $avg)
        <td>{{ $avg }}</td>
      @endforeach
    </tr>
    @endif
  </tbody>
</table>

@include('pdf.partials.signatures', ['type' => 'academic'])

@include('pdf.partials.footer', [
  'footerNote' => 'Comparative diagnostic evaluation. Green indicates performance at or above school mean; Red indicates performance below school mean.'
])
</body>
</html>
