<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Vehicle Maintenance Cost Report — {{ $year }}</title>
<style>
  @page {
    size: A4 portrait;
    margin: 12mm 15mm 16mm 15mm;
  }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, sans-serif; font-size: 9px; color: #1e293b; }
  .summary-row { display: table; width: 100%; margin-bottom: 12px; }
  .sum-cell { display: table-cell; text-align: center; border: 1px solid #cbd5e1; background: #f8fafc; padding: 6px; }
  .sum-val { font-size: 14px; font-weight: bold; color: #0f172a; font-family: monospace; }
  .sum-lbl { font-size: 7.5px; color: #64748b; text-transform: uppercase; margin-top: 1px; }
  .vehicle-header { background: #f1f5f9; padding: 4.5px 8px; margin: 10px 0 3px; font-weight: bold; font-size: 9px; border-left: 3px solid #0f172a; }
  table.maint-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
  table.maint-table th { background: #1e293b; color: white; font-size: 8px; padding: 4px 6px; text-align: left; text-transform: uppercase; border: 1px solid #334155; }
  table.maint-table td { border: 1px solid #cbd5e1; padding: 4px 6px; font-size: 8.5px; }
  table.maint-table tr:nth-child(even) td { background: #f8fafc; }
  .total-row td { font-weight: bold !important; background: #f1f5f9 !important; border-top: 1.5px solid #0f172a !important; color: #0f172a !important; }
  .grand-total td { font-weight: bold !important; background: #0f172a !important; color: white !important; font-size: 9.5px !important; }
  .badge-red { color: #dc2626; font-weight: bold; }
  .badge-green { color: #15803d; font-weight: bold; }
</style>
</head>
<body>
@include('pdf.partials.watermark')

@include('pdf.partials.header', [
  'title' => 'Fleet Maintenance & Service Cost Audit Report',
  'subtitle' => 'Period: Year ' . $year . ' | Academic Session: ' . ($school?->academic_year ?? 'Current Session')
])

<div class="summary-row">
  <div class="sum-cell"><div class="sum-val">₹{{ number_format($grandTotal, 2) }}</div><div class="sum-lbl">Total Fleet Maintenance</div></div>
  <div class="sum-cell"><div class="sum-val">{{ $perVehicle->count() }}</div><div class="sum-lbl">Total Vehicles</div></div>
  <div class="sum-cell"><div class="sum-val">{{ $perVehicle->sum(fn($v) => $v['logs']->count()) }}</div><div class="sum-lbl">Service Jobs Done</div></div>
  <div class="sum-cell"><div class="sum-val">₹{{ number_format($perVehicle->avg('total'), 2) }}</div><div class="sum-lbl">Average Cost / Vehicle</div></div>
</div>

@foreach($perVehicle->filter(fn($v) => $v['total'] > 0) as $row)
<div class="vehicle-header">
  Vehicle: {{ $row['vehicle']->vehicle_number }}
  @if($row['vehicle']->make ?? null) &bull; {{ $row['vehicle']->make }} @endif
  &nbsp;|&nbsp;
  Total Expense: ₹{{ number_format($row['total'], 2) }}
  &nbsp;|&nbsp; {{ $row['logs']->count() }} Job(s)
  &nbsp;|&nbsp; {{ $row['breakdown_count'] }} Breakdown(s)
</div>
<table class="maint-table">
  <thead>
    <tr>
      <th style="width: 75px;">Date</th>
      <th style="width: 70px;">Type</th>
      <th>Work Description</th>
      <th style="width: 100px;">Vendor / Workshop</th>
      <th style="width: 80px; text-align: right;">Cost (₹)</th>
      <th style="width: 80px;">Next Due</th>
    </tr>
  </thead>
  <tbody>
    @foreach($row['logs']->sortByDesc('maintenance_date') as $log)
    <tr>
      <td>{{ \Carbon\Carbon::parse($log->maintenance_date)->format('d M Y') }}</td>
      <td class="{{ $log->maintenance_type==='breakdown' ? 'badge-red' : 'badge-green' }}">{{ ucfirst($log->maintenance_type) }}</td>
      <td>{{ \Illuminate\Support\Str::limit($log->work_done, 45) }}</td>
      <td>{{ $log->vendor ?? '—' }}</td>
      <td style="text-align: right; font-family: monospace;">₹{{ number_format($log->cost, 2) }}</td>
      <td>{{ $log->next_service_date ? \Carbon\Carbon::parse($log->next_service_date)->format('d M Y') : '—' }}</td>
    </tr>
    @endforeach
    <tr class="total-row">
      <td colspan="4" style="text-align: right;">Subtotal ({{ $row['vehicle']->vehicle_number }}):</td>
      <td style="text-align: right; font-family: monospace;">₹{{ number_format($row['total'], 2) }}</td>
      <td></td>
    </tr>
  </tbody>
</table>
@endforeach

<table class="maint-table" style="margin-top: 10px;">
  <tbody>
    <tr class="grand-total">
      <td colspan="4" style="padding: 6px 8px;">CONSOLIDATED GRAND TOTAL — All Vehicles</td>
      <td style="text-align: right; font-family: monospace; padding: 6px 8px; width: 100px;">₹{{ number_format($grandTotal, 2) }}</td>
      <td style="width: 80px;"></td>
    </tr>
  </tbody>
</table>

@include('pdf.partials.signatures', ['type' => 'financial'])

@include('pdf.partials.footer')
</body>
</html>
