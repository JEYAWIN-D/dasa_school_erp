<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Daily Fee Collection Day Book</title>
  <style>
    @page {
      size: A4 portrait;
      margin: 12mm 14mm 16mm 14mm;
    }
    body {
      font-family: Arial, Helvetica, sans-serif;
      font-size: 9.5px;
      margin: 0;
      padding: 0;
      color: #1e293b;
    }
    table.data-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
      margin-bottom: 12px;
    }
    table.data-table th, table.data-table td {
      border: 1px solid #cbd5e1;
      padding: 4.5px 6px;
      text-align: left;
    }
    table.data-table th {
      background: #f1f5f9;
      font-size: 8.5px;
      text-transform: uppercase;
      font-weight: bold;
      color: #334155;
    }
    .amount {
      text-align: right;
      font-family: 'Courier New', Courier, monospace;
    }
    tfoot td {
      font-weight: bold;
      background: #f8fafc;
    }
    .summary-table {
      border-collapse: collapse;
      margin-bottom: 10px;
    }
    .summary-table th, .summary-table td {
      border: 1px solid #cbd5e1;
      padding: 4px 8px;
    }
    .summary-table th {
      background: #f8fafc;
      font-size: 8.5px;
      font-weight: bold;
      color: #475569;
    }
  </style>
</head>
<body>
  @include('pdf.partials.watermark')

  @include('pdf.partials.header', [
    'title' => 'Daily Fee Collection Day Book',
    'subtitle' => 'Collection Date: ' . (request('date') ?? \Carbon\Carbon::today()->toFormattedDateString())
  ])

  @php
    $cash   = $payments->where('payment_mode','cash')->sum('amount_paid');
    $online = $payments->whereIn('payment_mode',['online','upi','neft','rtgs'])->sum('amount_paid');
    $cheque = $payments->where('payment_mode','cheque')->sum('amount_paid');
    $total  = $payments->sum('amount_paid');
  @endphp

  <table class="summary-table" style="width: auto; min-width: 320px;">
    <tr>
      <th>Cash</th>
      <th>Online / UPI</th>
      <th>Cheque / DD</th>
      <th style="background:#f1f5f9; color:#0f172a;">Grand Total</th>
    </tr>
    <tr>
      <td class="amount">₹{{ number_format($cash, 2) }}</td>
      <td class="amount">₹{{ number_format($online, 2) }}</td>
      <td class="amount">₹{{ number_format($cheque, 2) }}</td>
      <td class="amount" style="font-weight:bold; color:#0f172a;">₹{{ number_format($total, 2) }}</td>
    </tr>
  </table>

  <table class="data-table">
    <thead>
      <tr>
        <th style="width: 25px;">#</th>
        <th style="width: 85px;">Receipt No</th>
        <th>Student Name</th>
        <th style="width: 70px;">Class</th>
        <th>Fee Head</th>
        <th style="width: 65px;">Mode</th>
        <th style="width: 80px;">Ref No</th>
        <th class="amount" style="width: 80px;">Amount (₹)</th>
      </tr>
    </thead>
    <tbody>
      @forelse($payments as $i => $p)
      <tr>
        <td>{{ $i + 1 }}</td>
        <td style="font-family:monospace; font-weight: 600;">{{ $p->receipt_number }}</td>
        <td>{{ $p->enrollment?->student?->full_name ?? '—' }}</td>
        <td>{{ $p->enrollment?->class?->name ?? '—' }}</td>
        <td>{{ $p->feeHead?->name ?? 'General Fee' }}</td>
        <td style="text-transform:capitalize;">{{ str_replace('_',' ',$p->payment_mode) }}</td>
        <td>{{ $p->transaction_reference ?? '—' }}</td>
        <td class="amount">{{ number_format($p->amount_paid, 2) }}</td>
      </tr>
      @empty
      <tr>
        <td colspan="8" style="text-align:center; color:#94a3b8; padding:15px;">No transactions recorded for this date.</td>
      </tr>
      @endforelse
    </tbody>
    @if($payments->count())
    <tfoot>
      <tr>
        <td colspan="7" style="text-align:right; font-weight: bold;">Grand Total:</td>
        <td class="amount" style="font-weight:bold;">₹{{ number_format($total, 2) }}</td>
      </tr>
    </tfoot>
    @endif
  </table>

  @include('pdf.partials.signatures', ['type' => 'financial'])

  @include('pdf.partials.footer')
</body>
</html>
