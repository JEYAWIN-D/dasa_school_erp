<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Hostel Occupancy Report</title>
<style>
  @page {
    size: A4 landscape;
    margin: 10mm 12mm 14mm 12mm;
  }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 9px;
    color: #1e293b;
    line-height: 1.4;
  }
  table.summary-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 12px;
  }
  table.summary-table td {
    border: 1px solid #cbd5e1;
    background: #f8fafc;
    padding: 6px 10px;
    text-align: center;
  }
  .sum-val { font-size: 14px; font-weight: bold; color: #0f172a; }
  .sum-lbl { font-size: 8px; color: #64748b; text-transform: uppercase; margin-top: 2px; }
  table.data-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 12px;
  }
  table.data-table th {
    background: #1e293b;
    color: white;
    font-size: 8.5px;
    padding: 5px 6px;
    text-align: left;
    text-transform: uppercase;
  }
  table.data-table td {
    border: 1px solid #e2e8f0;
    padding: 4px 6px;
    font-size: 8.5px;
  }
  table.data-table tr:nth-child(even) td { background: #f8fafc; }
</style>
</head>
<body>

@include('pdf.partials.watermark')

@include('pdf.partials.header', [
    'title' => 'HOSTEL CURRENT OCCUPANCY REGISTER',
    'subtitle' => 'Resident Allotment and Capacity Audit',
    'date' => now()->format('d M Y')
])

<table class="summary-table">
  <tr>
    <td style="width: 25%;">
      <div class="sum-val">{{ $allotments->count() }}</div>
      <div class="sum-lbl">Total Resident Students</div>
    </td>
    @foreach($hostels as $h)
    <td>
      <div class="sum-val">{{ $allotments->filter(fn($a) => $a->room?->hostel_id == $h->id)->count() }}</div>
      <div class="sum-lbl">{{ $h->name }}</div>
    </td>
    @endforeach
  </tr>
</table>

<table class="data-table">
  <thead>
    <tr>
      <th style="width: 5%; text-align:center;">#</th>
      <th>Student Full Name</th>
      <th style="width: 12%; text-align:center;">Admission No</th>
      <th style="width: 16%;">Hostel Block</th>
      <th style="width: 10%; text-align:center;">Room #</th>
      <th style="width: 12%;">Room Type</th>
      <th style="width: 12%; text-align:center;">Allotted Since</th>
      <th style="width: 12%; text-align:right;">Monthly Fee (₹)</th>
    </tr>
  </thead>
  <tbody>
    @forelse($allotments->sortBy(fn($a) => $a->room?->room_number) as $i => $a)
    <tr>
      <td style="text-align:center; color:#64748b;">{{ $i + 1 }}</td>
      <td style="font-weight:600;">{{ $a->student?->full_name ?? ($a->student?->first_name . ' ' . $a->student?->last_name) }}</td>
      <td style="text-align:center; font-weight:bold;">{{ $a->student?->admission_no ?? $a->student?->admission_number ?? '—' }}</td>
      <td>{{ $a->room?->hostel?->name ?? '—' }}</td>
      <td style="text-align:center; font-weight:bold;">{{ $a->room?->room_number ?? '—' }}</td>
      <td>{{ ucfirst($a->room?->room_type ?? '—') }}</td>
      <td style="text-align:center;">{{ $a->allotment_date?->format('d M Y') ?? '—' }}</td>
      <td style="text-align:right; font-weight:bold;">{{ number_format($a->monthly_fee ?? 0, 2) }}</td>
    </tr>
    @empty
    <tr>
      <td colspan="8" style="text-align:center; color:#94a3b8; padding:12px;">No active room allotments recorded.</td>
    </tr>
    @endforelse
  </tbody>
</table>

@include('pdf.partials.signatures', ['mode' => 'standard'])

@include('pdf.partials.footer', ['computerGenerated' => true])

</body>
</html>
