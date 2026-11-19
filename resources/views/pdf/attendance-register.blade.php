<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Monthly Attendance Register</title>
  <style>
    @page {
      size: A4 landscape;
      margin: 8mm 10mm 12mm 10mm;
    }
    body {
      font-family: Arial, Helvetica, sans-serif;
      font-size: 8.5px;
      margin: 0;
      color: #1e293b;
    }
    table.att-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 8px;
      margin-bottom: 12px;
    }
    table.att-table th, table.att-table td {
      border: 1px solid #cbd5e1;
      padding: 3px 2px;
      text-align: center;
      font-size: 8px;
    }
    table.att-table th {
      background: #f1f5f9;
      font-weight: bold;
      color: #334155;
      text-transform: uppercase;
    }
    .name-col {
      text-align: left !important;
      white-space: nowrap;
      min-width: 120px;
      padding-left: 6px !important;
      font-weight: 600;
    }
    .present { color: #16a34a; font-weight: bold; }
    .absent { color: #dc2626; font-weight: bold; }
    .late { color: #d97706; font-weight: bold; }
  </style>
</head>
<body>
  @include('pdf.partials.watermark')

  @include('pdf.partials.header', [
    'title' => 'Monthly Student Attendance Register',
    'subtitle' => 'Month: ' . (request('month') ?? now()->format('F Y'))
  ])

  <table class="att-table">
    <thead>
      <tr>
        <th class="name-col">Student Name</th>
        @foreach($dates as $d)
          <th style="width: 18px;">{{ $d->format('d') }}</th>
        @endforeach
        <th style="width: 24px; background: #dcfce7; color: #166534;">P</th>
        <th style="width: 24px; background: #fee2e2; color: #991b1b;">A</th>
        <th style="width: 30px; background: #e0e7ff; color: #3730a3;">%</th>
      </tr>
    </thead>
    <tbody>
      @forelse($students as $s)
        @php $rec = $attendanceMap[$s->student_id] ?? []; @endphp
        <tr>
          <td class="name-col">{{ $s->student?->full_name ?? '—' }}</td>
          @foreach($dates as $d)
            @php $status = $rec[$d->toDateString()] ?? null; @endphp
            <td>
              @if($status==='present')<span class="present">P</span>
              @elseif($status==='absent')<span class="absent">A</span>
              @elseif($status==='late')<span class="late">L</span>
              @else<span style="color:#cbd5e1;">-</span>@endif
            </td>
          @endforeach
          @php
            $p = collect($rec)->filter(fn($v)=>in_array($v,['present','late']))->count();
            $a = collect($rec)->filter(fn($v)=>$v==='absent')->count();
            $t = $p + $a;
            $pct = $t > 0 ? round(($p / $t) * 100) : 0;
          @endphp
          <td class="present">{{ $p }}</td>
          <td class="absent">{{ $a }}</td>
          <td style="{{ $pct < 75 ? 'color:#dc2626;font-weight:bold;' : 'font-weight:bold;' }}">{{ $pct }}%</td>
        </tr>
      @empty
        <tr>
          <td colspan="{{ count($dates) + 4 }}" style="padding: 15px; color: #94a3b8;">No student enrollment records found for this period.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  @include('pdf.partials.signatures', ['type' => 'academic'])

  @include('pdf.partials.footer')
</body>
</html>
