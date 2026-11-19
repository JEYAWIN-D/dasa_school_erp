<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Hostel Gate Pass Register</title>
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
    color: white;
    font-size: 8px;
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
  .badge-approved { color: #166534; font-weight: bold; }
  .badge-rejected { color: #991b1b; font-weight: bold; }
  .badge-pending { color: #b45309; font-weight: bold; }
</style>
</head>
<body>

@include('pdf.partials.watermark')

@include('pdf.partials.header', [
    'title' => 'HOSTEL GATE PASS & OUTPASS REGISTER',
    'subtitle' => 'Movement Log for ' . now()->format('F Y'),
    'date' => now()->format('d M Y')
])

<table class="data-table">
  <thead>
    <tr>
      <th style="width: 4%; text-align:center;">#</th>
      <th style="width: 18%;">Student Name</th>
      <th style="width: 9%; text-align:center;">Room #</th>
      <th style="width: 13%; text-align:center;">Departure Expected</th>
      <th style="width: 13%; text-align:center;">Return Expected</th>
      <th style="width: 15%;">Destination</th>
      <th style="width: 16%;">Purpose of Leave</th>
      <th style="width: 12%; text-align:center;">Approval Status</th>
    </tr>
  </thead>
  <tbody>
    @forelse($outpasses as $i => $op)
    <tr>
      <td style="text-align:center; color:#64748b;">{{ $i + 1 }}</td>
      <td style="font-weight:600;">{{ $op->student?->full_name ?? ($op->student?->first_name . ' ' . $op->student?->last_name) }}</td>
      <td style="text-align:center; font-weight:bold;">{{ $op->allotment?->room?->room_number ?? '—' }}</td>
      <td style="text-align:center;">{{ $op->from_datetime?->format('d M Y, h:i A') ?? '—' }}</td>
      <td style="text-align:center;">{{ $op->to_datetime?->format('d M Y, h:i A') ?? '—' }}</td>
      <td>{{ $op->destination ?? '—' }}</td>
      <td>{{ \Illuminate\Support\Str::limit($op->reason ?? '—', 45) }}</td>
      <td style="text-align:center;">
        @if(in_array($op->status, ['approved', 'returned']))
          <span class="badge-approved">{{ strtoupper($op->status) }}</span>
        @elseif($op->status === 'rejected')
          <span class="badge-rejected">REJECTED</span>
        @else
          <span class="badge-pending">{{ strtoupper($op->status ?? 'PENDING') }}</span>
        @endif
      </td>
    </tr>
    @empty
    <tr>
      <td colspan="8" style="text-align:center; padding:12px; color:#94a3b8;">No hostel gate passes registered for this period.</td>
    </tr>
    @endforelse
  </tbody>
</table>

@include('pdf.partials.signatures', ['mode' => 'standard'])

@include('pdf.partials.footer', ['computerGenerated' => true])

</body>
</html>
