<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Official Academic Calendar</title>
<style>
  @page {
    size: A4 portrait;
    margin: 12mm 15mm 16mm 15mm;
  }
  * { box-sizing: border-box; }
  body {
    font-family: Arial, sans-serif;
    font-size: 9.5px;
    color: #1e293b;
    margin: 0;
    padding: 0;
    background: #fff;
  }
  .month-title {
    background: #1e293b;
    color: white;
    font-size: 10px;
    font-weight: bold;
    padding: 5px 10px;
    margin-top: 14px;
    border-radius: 3px 3px 0 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  table.cal-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 8px;
    border: 1px solid #cbd5e1;
    border-top: none;
  }
  table.cal-table th {
    background: #f1f5f9;
    padding: 4.5px 8px;
    text-align: left;
    font-size: 8.5px;
    color: #334155;
    border: 1px solid #cbd5e1;
    text-transform: uppercase;
    font-weight: bold;
  }
  table.cal-table td {
    padding: 4.5px 8px;
    border: 1px solid #e2e8f0;
    font-size: 9px;
    vertical-align: middle;
  }
  table.cal-table tr:nth-child(even) td { background: #f8fafc; }
  .holiday-type {
    display: inline-block;
    padding: 2px 7px;
    border-radius: 999px;
    font-size: 7.5px;
    font-weight: bold;
    text-transform: uppercase;
  }
  .type-national  { background: #dbeafe; color: #1e40af; }
  .type-religious { background: #fef3c7; color: #92400e; }
  .type-school    { background: #d1fae5; color: #065f46; }
  .type-other     { background: #f1f5f9; color: #475569; }
</style>
</head>
<body>
@include('pdf.partials.watermark')

@include('pdf.partials.header', [
  'title' => 'Official Institutional Academic Calendar & Holiday Schedule',
  'subtitle' => 'Academic Session: ' . ($currentYear?->name ?? (date('Y') . '-' . (date('Y') + 1)))
])

@php
$byMonth = $holidays->groupBy(fn($h) => $h->date->format('Y-m'));
$months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
@endphp

@foreach($byMonth as $monthKey => $monthHolidays)
  @php [$y, $m] = explode('-', $monthKey); @endphp
  <div class="month-title">{{ $months[(int)$m - 1] }} {{ $y }}</div>
  <table class="cal-table">
    <thead>
      <tr>
        <th style="width: 100px;">Date</th>
        <th style="width: 80px;">Day</th>
        <th>Event / Observance / Holiday</th>
        <th style="width: 110px; text-align: center;">Classification</th>
      </tr>
    </thead>
    <tbody>
      @foreach($monthHolidays->sortBy('holiday_date') as $h)
        <tr>
          <td style="font-weight: 600;">{{ $h->date->format('d M Y') }}</td>
          <td>{{ $h->date->format('l') }}</td>
          <td><strong>{{ $h->name }}</strong></td>
          <td style="text-align: center;">
            <span class="holiday-type type-{{ $h->type ?? 'other' }}">
              {{ ucfirst($h->type ?? 'holiday') }}
            </span>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
@endforeach

@if($holidays->isEmpty())
  <div style="text-align: center; color: #94a3b8; padding: 30px; font-style: italic;">
    No public holidays or institutional events registered for this academic session.
  </div>
@endif

@include('pdf.partials.signatures', ['type' => 'academic'])

@include('pdf.partials.footer', [
  'footerNote' => 'Official Academic Schedule approved by ' . ($school?->school_name ?? 'the Academic Council') . '. Total holidays: ' . $holidays->count() . '.'
])
</body>
</html>
