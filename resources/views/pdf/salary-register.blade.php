<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Salary Register — {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}</title>
<style>
  @page {
    size: A4 landscape;
    margin: 10mm 12mm 14mm 12mm;
  }
  body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 8.5px;
    color: #1e293b;
    margin: 0;
    padding: 0;
  }
  table.salary-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 8px;
    margin-bottom: 12px;
  }
  table.salary-table th {
    background: #1e293b;
    color: #fff;
    padding: 5px 6px;
    text-align: right;
    font-size: 8px;
    text-transform: uppercase;
    font-weight: bold;
    border: 1px solid #475569;
  }
  table.salary-table th.left { text-align: left; }
  table.salary-table td {
    padding: 4px 6px;
    border: 1px solid #cbd5e1;
    font-size: 8.5px;
    text-align: right;
  }
  table.salary-table td.left { text-align: left; }
  table.salary-table tr:nth-child(even) td { background: #f8fafc; }
  .total-row td {
    font-weight: bold !important;
    background: #f1f5f9 !important;
    border-top: 1.5px solid #0f172a !important;
    color: #0f172a !important;
  }
</style>
</head>
<body>
@include('pdf.partials.watermark')

@include('pdf.partials.header', [
  'title' => 'Monthly Staff Salary Register',
  'subtitle' => 'Pay Period: ' . \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y')
])

<table class="salary-table">
  <thead>
    <tr>
      <th class="left" style="width: 25px;">#</th>
      <th class="left" style="width: 80px;">Emp Code</th>
      <th class="left">Employee Name &amp; Designation</th>
      <th class="left" style="width: 100px;">Department</th>
      <th style="width: 90px;">Gross Salary (₹)</th>
      <th style="width: 85px;">Deductions (₹)</th>
      <th style="width: 90px;">Net Payable (₹)</th>
      <th style="width: 70px; text-align: center;">Status</th>
    </tr>
  </thead>
  <tbody>
    @forelse($records as $rec)
    <tr>
      <td class="left">{{ $loop->iteration }}</td>
      <td class="left" style="font-family: monospace; font-weight: 600;">{{ $rec->employee?->employee_code ?? $rec->employee?->employee_number ?? $rec->employee_id }}</td>
      <td class="left">
        <strong>{{ $rec->employee?->full_name ?: ($rec->employee?->first_name ? $rec->employee->first_name . ' ' . $rec->employee->last_name : ($rec->employee?->name ?? '—')) }}</strong>
        <div style="color: #64748b; font-size: 7.5px;">{{ is_object($rec->employee?->designation) ? $rec->employee->designation->name : ($rec->employee?->designation ?? 'Faculty') }}</div>
      </td>
      <td class="left">{{ is_object($rec->employee?->department) ? $rec->employee->department->name : ($rec->employee?->department?->name ?? '—') }}</td>
      <td style="font-family: monospace;">{{ number_format($rec->gross_salary, 2) }}</td>
      <td style="font-family: monospace; color: #dc2626;">{{ number_format($rec->deductions, 2) }}</td>
      <td style="font-family: monospace; font-weight: bold; color: #0f172a;">{{ number_format($rec->net_salary, 2) }}</td>
      <td style="text-align: center; font-weight: bold; color: {{ $rec->status === 'paid' ? '#16a34a' : '#d97706' }};">
        {{ strtoupper($rec->status ?? 'PAID') }}
      </td>
    </tr>
    @empty
    <tr>
      <td colspan="8" style="text-align: center; padding: 18px; color: #94a3b8;">No monthly payroll records found.</td>
    </tr>
    @endforelse
  </tbody>
  @if($records->isNotEmpty())
  <tfoot>
    <tr class="total-row">
      <td class="left" colspan="4">GRAND TOTAL:</td>
      <td style="font-family: monospace;">₹{{ number_format($records->sum('gross_salary'), 2) }}</td>
      <td style="font-family: monospace; color: #dc2626;">₹{{ number_format($records->sum('deductions'), 2) }}</td>
      <td style="font-family: monospace;">₹{{ number_format($records->sum('net_salary'), 2) }}</td>
      <td></td>
    </tr>
  </tfoot>
  @endif
</table>

@include('pdf.partials.signatures', ['type' => 'financial'])

@include('pdf.partials.footer')
</body>
</html>
