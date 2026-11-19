<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $title ?? 'Student Transport Passenger Manifest' }}</title>
<style>
  @page {
    size: A4 landscape;
    margin: 10mm 12mm 14mm 12mm;
  }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 8.5px;
    color: #1e293b;
    line-height: 1.4;
  }
  table.data-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 12px;
  }
  table.data-table th {
    background: #1e293b;
    color: #ffffff;
    font-size: 8px;
    font-weight: bold;
    text-transform: uppercase;
    padding: 5px 6px;
    border: 1px solid #334155;
    text-align: left;
  }
  table.data-table td {
    padding: 4px 6px;
    border: 1px solid #cbd5e1;
    font-size: 8px;
  }
  table.data-table tr:nth-child(even) td {
    background: #f8fafc;
  }
  .badge-active {
    color: #166534;
    font-weight: bold;
  }
  .badge-disc {
    color: #991b1b;
    font-weight: bold;
  }
</style>
</head>
<body>

@include('pdf.partials.watermark')

@include('pdf.partials.header', [
    'title' => 'STUDENT TRANSPORT PASSENGER MANIFEST',
    'subtitle' => $title ?? 'Route & Vehicle Allocation Roster',
    'date' => now()->format('d M Y')
])

<table class="data-table">
  <thead>
    <tr>
      <th style="width: 3%; text-align:center;">#</th>
      <th style="width: 10%; text-align:center;">Admission #</th>
      <th style="width: 15%;">Student Name</th>
      <th style="width: 8%; text-align:center;">Class &amp; Sec</th>
      <th style="width: 10%;">Vehicle #</th>
      <th style="width: 14%;">Route Corridor</th>
      <th style="width: 12%;">Pickup Stop</th>
      <th style="width: 7%; text-align:center;">Pickup</th>
      <th style="width: 12%;">Drop Stop</th>
      <th style="width: 7%; text-align:center;">Drop</th>
      <th style="width: 8%; text-align:right;">Fee (₹)</th>
      <th style="width: 6%; text-align:center;">Status</th>
    </tr>
  </thead>
  <tbody>
    @forelse($rows as $idx => $r)
      <tr>
        <td style="text-align:center; color:#64748b;">{{ $idx + 1 }}</td>
        <td style="text-align:center; font-weight:bold; color:#0f172a;">{{ $r['Admission No'] ?? '—' }}</td>
        <td style="font-weight:600;">{{ $r['Student Name'] ?? '—' }}</td>
        <td style="text-align:center;">{{ $r['Class & Section'] ?? '—' }}</td>
        <td style="font-weight:600;">{{ $r['Vehicle'] ?? '—' }}</td>
        <td>{{ $r['Route'] ?? '—' }}</td>
        <td>{{ $r['Pickup Stop'] ?? '—' }}</td>
        <td style="text-align:center;">{{ $r['Pickup Time'] ?? '—' }}</td>
        <td>{{ $r['Drop Stop'] ?? '—' }}</td>
        <td style="text-align:center;">{{ $r['Drop Time'] ?? '—' }}</td>
        <td style="text-align:right; font-weight:bold;">{{ number_format($r['Transport Fee'] ?? 0, 2) }}</td>
        <td style="text-align:center;">
          <span class="{{ ($r['Status'] ?? '') === 'Active' ? 'badge-active' : 'badge-disc' }}">
            {{ strtoupper($r['Status'] ?? 'ACTIVE') }}
          </span>
        </td>
      </tr>
    @empty
      <tr>
        <td colspan="12" style="text-align:center; padding:16px; color:#94a3b8;">No student transport allocations found for this filter criteria.</td>
      </tr>
    @endforelse
  </tbody>
</table>

@include('pdf.partials.signatures', ['mode' => 'standard'])

@include('pdf.partials.footer', ['computerGenerated' => true])

</body>
</html>
