<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Class Result Summary — {{ $class->name }}</title>
<style>
  @page {
    size: A4 portrait;
    margin: 12mm 15mm 16mm 15mm;
  }
  body {
    font-family: Arial, sans-serif;
    font-size: 9.5px;
    color: #1e293b;
    margin: 0;
    padding: 0;
  }
  table.summary-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 8px;
    margin-bottom: 12px;
  }
  table.summary-table th {
    background: #1e293b;
    color: #fff;
    padding: 5px 7px;
    text-align: left;
    font-size: 8.5px;
    text-transform: uppercase;
    font-weight: bold;
    border: 1px solid #334155;
  }
  table.summary-table td {
    padding: 4.5px 7px;
    border: 1px solid #cbd5e1;
    font-size: 9px;
  }
  table.summary-table tr:nth-child(even) td {
    background: #f8fafc;
  }
  .pass { color: #16a34a; font-weight: bold; }
  .fail { color: #dc2626; font-weight: bold; }
  .rank { color: #4338ca; font-weight: bold; text-align: center; }
  .total-row td {
    font-weight: bold !important;
    background: #f1f5f9 !important;
    border-top: 1.5px solid #0f172a !important;
    color: #0f172a !important;
  }
</style>
</head>
<body>
@include('pdf.partials.watermark')

@include('pdf.partials.header', [
  'title' => 'Class Examination Performance & Result Summary',
  'subtitle' => 'Exam: ' . ($exam->name ?? 'Standard') . ' | Class: ' . ($class->name ?? '—') . ' | Academic Session: ' . ($school?->academic_year ?? 'Current Session')
])

<table class="summary-table">
  <thead>
    <tr>
      <th style="width: 35px; text-align: center;">Rank</th>
      <th style="width: 85px;">Adm No</th>
      <th>Candidate Full Name</th>
      <th style="text-align: right; width: 85px;">Marks Obtained</th>
      <th style="text-align: right; width: 75px;">Max Marks</th>
      <th style="text-align: right; width: 75px;">Percentage</th>
      <th style="text-align: center; width: 65px;">Status</th>
    </tr>
  </thead>
  <tbody>
    @php $rank = 1; @endphp
    @forelse($summary as $row)
    <tr>
      <td class="rank">{{ $rank++ }}</td>
      <td style="font-family: monospace;">{{ $row['student']?->admission_no ?? $row['student']?->admission_number ?? '—' }}</td>
      <td><strong>{{ $row['student']?->full_name }}</strong></td>
      <td style="text-align: right; font-family: monospace;">{{ $row['obtained'] }}</td>
      <td style="text-align: right; font-family: monospace;">{{ $row['totalMarks'] }}</td>
      <td style="text-align: right; font-family: monospace;"><strong>{{ $row['percentage'] }}%</strong></td>
      <td style="text-align: center;" class="{{ $row['hasFail'] ? 'fail' : 'pass' }}">
        {{ $row['hasFail'] ? 'FAIL' : 'PASS' }}
      </td>
    </tr>
    @empty
    <tr>
      <td colspan="7" style="text-align: center; padding: 15px; color: #94a3b8;">No assessment records recorded for this examination.</td>
    </tr>
    @endforelse
  </tbody>
  @if($summary->isNotEmpty())
  <tfoot>
    <tr class="total-row">
      <td colspan="3">CONSOLIDATED BATCH METRICS</td>
      <td style="text-align: right; font-family: monospace;">{{ $summary->sum('obtained') }}</td>
      <td></td>
      <td style="text-align: right; font-family: monospace;">
        {{ $summary->count() > 0 ? round($summary->avg('percentage'), 1) : 0 }}% avg
      </td>
      <td style="text-align: center; font-size: 8px;">
        P: {{ $summary->where('hasFail', false)->count() }} | F: {{ $summary->where('hasFail', true)->count() }}
      </td>
    </tr>
  </tfoot>
  @endif
</table>

@include('pdf.partials.signatures', ['type' => 'academic'])

@include('pdf.partials.footer')
</body>
</html>
