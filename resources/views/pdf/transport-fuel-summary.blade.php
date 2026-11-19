<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Transport Fuel Expense Summary</title>
<style>
  @page {
    size: A4 portrait;
    margin: 12mm 15mm 16mm 15mm;
  }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, sans-serif; font-size: 9.5px; color: #1e293b; }
  .totals { display: table; width: 100%; margin: 10px 0 14px 0; }
  .total-box { display: table-cell; width: 50%; text-align: center; border: 1px solid #cbd5e1; background: #f8fafc; border-radius: 4px; padding: 8px; }
  .total-val { font-size: 16px; font-weight: bold; color: #0f172a; font-family: monospace; }
  .total-label { font-size: 8px; color: #64748b; margin-top: 2px; text-transform: uppercase; font-weight: bold; }
  table.fuel-table { width: 100%; border-collapse: collapse; margin-top: 8px; margin-bottom: 12px; }
  table.fuel-table th { background: #1e293b; color: white; font-size: 8.5px; padding: 5px 8px; text-align: left; text-transform: uppercase; border: 1px solid #334155; }
  table.fuel-table td { border: 1px solid #cbd5e1; padding: 5px 8px; font-size: 9px; }
  table.fuel-table tr:nth-child(even) td { background: #f8fafc; }
  .total-row td { font-weight: bold !important; background: #f1f5f9 !important; border-top: 1.5px solid #0f172a !important; color: #0f172a !important; }
</style>
</head>
<body>
@php
  [$yr, $mo] = explode('-', $month);
  $monthLabel = \Carbon\Carbon::createFromDate($yr, $mo, 1)->format('F Y');
@endphp

@include('pdf.partials.watermark')

@include('pdf.partials.header', [
  'title' => 'Fleet Fuel Consumption & Expense Summary',
  'subtitle' => 'Month: ' . $monthLabel . ' | Academic Session: ' . ($school?->academic_year ?? 'Current Session')
])

<div class="totals">
  <div class="total-box">
    <div class="total-val">{{ number_format($grandTotals['litres'], 1) }} Litres</div>
    <div class="total-label">Total Fuel Consumed</div>
  </div>
  <div class="total-box" style="border-left: none;">
    <div class="total-val">₹{{ number_format($grandTotals['amount'], 2) }}</div>
    <div class="total-label">Total Fuel Cost</div>
  </div>
</div>

<table class="fuel-table">
  <thead>
    <tr>
      <th>Vehicle Number</th>
      <th style="text-align: center; width: 65px;">Fill-Ups</th>
      <th style="text-align: right; width: 85px;">Litres</th>
      <th style="text-align: right; width: 95px;">Amount (₹)</th>
      <th style="text-align: right; width: 85px;">KM Covered</th>
      <th style="text-align: center; width: 80px;">Efficiency</th>
    </tr>
  </thead>
  <tbody>
    @foreach($fuelByVehicle->sortByDesc('total_amount') as $row)
    @php $efficiency = ($row->total_litres > 0 && $row->km_covered) ? round($row->km_covered/$row->total_litres, 2) : null; @endphp
    <tr>
      <td style="font-weight: 600;">{{ $row->vehicle?->vehicle_number ?? ('Vehicle #' . $row->vehicle_id) }}</td>
      <td style="text-align: center;">{{ $row->fill_count }}</td>
      <td style="text-align: right; font-family: monospace;">{{ number_format($row->total_litres, 1) }}</td>
      <td style="text-align: right; font-family: monospace;">{{ number_format($row->total_amount, 2) }}</td>
      <td style="text-align: right; font-family: monospace;">{{ $row->km_covered ? number_format($row->km_covered, 0) : '—' }}</td>
      <td style="text-align: center; font-weight: 600;">{{ $efficiency ? $efficiency . ' km/L' : '—' }}</td>
    </tr>
    @endforeach
    <tr class="total-row">
      <td>Consolidated Total</td>
      <td style="text-align: center;">{{ $fuelByVehicle->sum('fill_count') }}</td>
      <td style="text-align: right; font-family: monospace;">{{ number_format($grandTotals['litres'], 1) }}</td>
      <td style="text-align: right; font-family: monospace;">₹{{ number_format($grandTotals['amount'], 2) }}</td>
      <td>—</td>
      <td>—</td>
    </tr>
  </tbody>
</table>

@include('pdf.partials.signatures', ['type' => 'financial'])

@include('pdf.partials.footer')
</body>
</html>
