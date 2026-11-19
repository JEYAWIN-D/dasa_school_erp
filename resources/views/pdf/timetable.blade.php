<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Class Timetable — {{ $class->name }}</title>
<style>
  @page {
    size: A4 landscape;
    margin: 10mm 12mm 14mm 12mm;
  }
  * { box-sizing: border-box; }
  body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 9px;
    color: #1e293b;
    margin: 0;
    padding: 0;
    background: #fff;
  }
  table.timetable {
    width: 100%;
    border-collapse: collapse;
    margin-top: 8px;
    margin-bottom: 12px;
  }
  table.timetable th {
    background: #1e293b;
    color: #ffffff;
    padding: 6px 4px;
    text-align: center;
    font-size: 8.5px;
    text-transform: uppercase;
    font-weight: bold;
    border: 1px solid #475569;
  }
  table.timetable td {
    padding: 4px 5px;
    border: 1px solid #cbd5e1;
    font-size: 8.5px;
    vertical-align: top;
    height: 48px;
  }
  .day-cell {
    background: #f8fafc;
    font-weight: bold;
    text-align: center;
    color: #0f172a;
    vertical-align: middle !important;
    width: 75px;
  }
  .subject {
    font-weight: bold;
    color: #0f172a;
    font-size: 9px;
  }
  .teacher {
    font-size: 7.5px;
    color: #475569;
    font-weight: 600;
    margin-top: 1px;
  }
  .time-slot {
    font-size: 7px;
    color: #64748b;
    margin-top: 1px;
  }
</style>
</head>
<body>
@include('pdf.partials.watermark')

@include('pdf.partials.header', [
  'title' => 'Official Class Timetable — ' . ($class->name ?? 'Standard'),
  'subtitle' => 'Section: ' . ($class->section ?? 'All') . ' | Academic Session: ' . ($school?->academic_year ?? 'Current Session')
])

@php
$days = [1=>'Monday', 2=>'Tuesday', 3=>'Wednesday', 4=>'Thursday', 5=>'Friday', 6=>'Saturday'];
$maxPeriods = 0;
foreach($timetable as $entries) { $maxPeriods = max($maxPeriods, $entries->count()); }
$periodCount = max($maxPeriods, 8);
@endphp

<table class="timetable">
  <thead>
    <tr>
      <th style="width: 75px;">Day</th>
      @for($p = 1; $p <= $periodCount; $p++)
        <th>Period {{ $p }}</th>
      @endfor
    </tr>
  </thead>
  <tbody>
    @foreach($days as $dayNum => $dayName)
      @php $dayEntries = $timetable[$dayNum] ?? collect(); @endphp
      <tr>
        <td class="day-cell">{{ $dayName }}</td>
        @for($p = 1; $p <= $periodCount; $p++)
          @php $entry = $dayEntries->firstWhere('period_number', $p); @endphp
          <td>
            @if($entry)
              <div class="subject">{{ $entry->subject?->name ?? '—' }}</div>
              <div class="teacher">{{ $entry->teacher?->full_name ?: ($entry->teacher?->first_name ? $entry->teacher->first_name . ' ' . $entry->teacher->last_name : 'Faculty') }}</div>
              @if($entry->start_time)
                <div class="time-slot">{{ $entry->start_time }} – {{ $entry->end_time }}</div>
              @endif
            @else
              <div style="color: #cbd5e1; text-align: center; margin-top: 14px;">—</div>
            @endif
          </td>
        @endfor
      </tr>
    @endforeach
  </tbody>
</table>

@include('pdf.partials.signatures', ['type' => 'academic'])

@include('pdf.partials.footer')
</body>
</html>
