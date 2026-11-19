<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Leave Encashment Report</title>
<style>
  @page {
    size: A4 portrait;
    margin: 12mm 15mm 16mm 15mm;
  }
  body { font-family: Arial, sans-serif; font-size: 9.5px; color: #1e293b; margin: 0; padding: 0; }
  table.data-table { width: 100%; border-collapse: collapse; margin-top: 8px; margin-bottom: 12px; }
  table.data-table th { background: #1e293b; color: #fff; padding: 5px 7px; text-align: left; font-size: 8.5px; text-transform: uppercase; font-weight: bold; border: 1px solid #334155; }
  table.data-table td { padding: 4.5px 7px; border: 1px solid #cbd5e1; font-size: 9px; }
  table.data-table tr:nth-child(even) td { background: #f8fafc; }
  .total-row td { font-weight: bold !important; background: #f1f5f9 !important; border-top: 1.5px solid #0f172a !important; color: #0f172a !important; }
</style>
</head>
<body>
@include('pdf.partials.watermark')

@include('pdf.partials.header', [
  'title' => 'Staff Leave Encashment & Settlement Audit Report',
  'subtitle' => 'Period: ' . $fyStart->format('d M Y') . ' to ' . $fyEnd->format('d M Y') . ' | Academic Session: ' . ($school?->academic_year ?? 'Current Session')
])

<table class="data-table">
  <thead>
    <tr>
      <th style="width: 25px;">#</th>
      <th style="width: 80px;">Emp Code</th>
      <th>Employee Full Name</th>
      <th style="width: 100px;">Department</th>
      <th style="width: 75px;">Date</th>
      <th style="text-align: center; width: 50px;">Days</th>
      <th style="text-align: right; width: 90px;">Amount (₹)</th>
      <th>Remarks</th>
    </tr>
  </thead>
  <tbody>
    @forelse($encashments as $enc)
    <tr>
      <td>{{ $loop->iteration }}</td>
      <td style="font-family: monospace;">{{ $enc->employee?->employee_code ?? $enc->employee?->employee_number ?? $enc->employee_id }}</td>
      <td><strong>{{ $enc->employee?->full_name ?: ($enc->employee?->first_name . ' ' . $enc->employee?->last_name) ?: ($enc->employee?->name ?? '—') }}</strong></td>
      <td>{{ is_object($enc->employee?->department) ? $enc->employee->department->name : ($enc->employee?->department?->name ?? '—') }}</td>
      <td>{{ $enc->encashment_date ? $enc->encashment_date->format('d M Y') : '—' }}</td>
      <td style="text-align: center; font-weight: bold;">{{ $enc->days ?? '—' }}</td>
      <td style="text-align: right; font-weight: bold; font-family: monospace; color: #16a34a;">₹{{ number_format($enc->amount, 2) }}</td>
      <td>{{ $enc->remarks ?? '—' }}</td>
    </tr>
    @empty
    <tr>
      <td colspan="8" style="text-align: center; padding: 18px; color: #94a3b8;">No leave encashment transactions recorded in this fiscal period.</td>
    </tr>
    @endforelse
  </tbody>
  @if($encashments->isNotEmpty())
  <tfoot>
    <tr class="total-row">
      <td colspan="6" style="text-align: right;">CONSOLIDATED TOTAL ENCASHMENT DISBURSED:</td>
      <td style="text-align: right; font-family: monospace;">₹{{ number_format($totalPaid, 2) }}</td>
      <td></td>
    </tr>
  </tfoot>
  @endif
</table>

@include('pdf.partials.signatures', ['type' => 'financial'])

@include('pdf.partials.footer')
</body>
</html>
