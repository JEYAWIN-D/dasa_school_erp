<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Staff Increment History Report</title>
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
  .total-row td {
    font-weight: bold;
    background: #f1f5f9;
  }
</style>
</head>
<body>

@include('pdf.partials.watermark')

@include('pdf.partials.header', [
    'title' => 'STAFF SALARY INCREMENT AUDIT REPORT',
    'subtitle' => 'Period: ' . $fyStart->format('d M Y') . ' to ' . $fyEnd->format('d M Y'),
    'date' => now()->format('d M Y')
])

<table class="data-table">
  <thead>
    <tr>
      <th style="width: 4%; text-align:center;">#</th>
      <th>Staff Name</th>
      <th style="width: 15%;">Department</th>
      <th style="width: 12%; text-align:center;">Appraisal Cycle</th>
      <th style="width: 14%; text-align:right;">Increment (₹)</th>
      <th style="width: 10%; text-align:center;">Inc %</th>
      <th style="width: 13%; text-align:center;">Effective Date</th>
      <th style="width: 12%; text-align:center;">Appraisal Rating</th>
    </tr>
  </thead>
  <tbody>
    @forelse($increments as $inc)
    <tr>
      <td style="text-align:center; color:#64748b;">{{ $loop->iteration }}</td>
      <td style="font-weight:600;">{{ $inc->employee?->name ?? '—' }}</td>
      <td>{{ $inc->employee?->department?->name ?? '—' }}</td>
      <td style="text-align:center;">{{ $inc->appraisal_year }}</td>
      <td style="text-align:right; color:#166534; font-weight:bold;">₹{{ number_format($inc->increment_amount, 2) }}</td>
      <td style="text-align:center;">{{ $inc->increment_percent ? number_format($inc->increment_percent, 1).'%' : '—' }}</td>
      <td style="text-align:center;">{{ $inc->increment_effective_date?->format('d M Y') ?? '—' }}</td>
      <td style="text-align:center;">{{ $inc->rating_label ?? '—' }}</td>
    </tr>
    @empty
    <tr>
      <td colspan="8" style="text-align:center; padding:14px; color:#94a3b8;">No increment records found for the selected period.</td>
    </tr>
    @endforelse
  </tbody>
  @if($increments->isNotEmpty())
  <tfoot>
    <tr class="total-row">
      <td colspan="4" style="text-align:right; text-transform:uppercase; font-size:8px; color:#475569;">Cumulative Increment Outlay:</td>
      <td style="text-align:right; color:#166534;">₹{{ number_format($totalIncrement, 2) }}</td>
      <td colspan="3"></td>
    </tr>
  </tfoot>
  @endif
</table>

@include('pdf.partials.signatures', ['mode' => 'financial'])

@include('pdf.partials.footer', ['computerGenerated' => true])

</body>
</html>
