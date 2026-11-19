<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Daily Account Day Book — {{ date('d-m-Y', strtotime($date)) }}</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <style>
    @page { size: A4 landscape; margin: 0 !important; }
    body {
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
      font-size: 10px;
      color: #1e293b;
      margin: 0;
      padding: 10mm 12mm 10mm 16mm !important;
      box-sizing: border-box !important;
      background: #fff;
    }
    .summary-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 10px;
      margin-bottom: 14px;
    }
    .summary-card {
      padding: 8px 10px;
      border: 1px solid #cbd5e1;
      border-radius: 6px;
      background: #f8fafc;
    }
    .card-label {
      font-size: 8.5px;
      font-weight: 800;
      text-transform: uppercase;
      color: #64748b;
    }
    .card-val {
      font-size: 13px;
      font-weight: 900;
      font-family: monospace;
      margin-top: 3px;
    }
    table.data-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 9px;
      margin-top: 8px;
    }
    table.data-table th {
      background: #1e293b;
      color: white;
      border: 1px solid #334155;
      padding: 5px 6px;
      text-align: left;
      font-weight: 700;
      font-size: 8.5px;
      text-transform: uppercase;
    }
    table.data-table td {
      border: 1px solid #cbd5e1;
      padding: 4px 6px;
    }
    table.data-table tr:nth-child(even) {
      background: #f8fafc;
    }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .font-bold { font-weight: bold; }
    .font-mono { font-family: monospace; }
    .badge {
      font-size: 8px;
      font-weight: 800;
      padding: 1px 5px;
      border-radius: 3px;
      display: inline-block;
      text-transform: uppercase;
    }
    .badge-inflow { background: #dcfce7; color: #166534; }
    .badge-outflow { background: #ffe4e6; color: #9f1239; }
    .badge-trf { background: #e0e7ff; color: #3730a3; }
    @media print {
      body { padding: 0 !important; background: #fff !important; }
      .no-print { display: none !important; }
      * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    }
  </style>
</head>
<body>

  {{-- Print Toolbar --}}
  <x-print.controls title="Daily Account Day Book" defaultSize="a4" defaultOrientation="landscape" :backUrl="route('accounts.index')" />

  {{-- Watermark & Centralized Header --}}
  <x-print.watermark />
  <x-print.header 
    title="DAILY CASH &amp; ACCOUNT DAY BOOK" 
    subtitle="Official Institutional Financial Inflow, Outflow &amp; Reconciliation Audit" 
    :academicYear="'Closing Date: ' . date('d M Y', strtotime($date))" 
    :date="now()->format('d M Y')"
  />

  {{-- Top KPIs --}}
  <div class="summary-grid">
    <div class="summary-card">
      <div class="card-label">Day Total Inflow</div>
      <div class="card-val" style="color: #15803d;">₹{{ number_format($totalInflow, 2) }}</div>
    </div>
    <div class="summary-card">
      <div class="card-label">Day Total Outflow</div>
      <div class="card-val" style="color: #be123c;">₹{{ number_format($totalOutflow, 2) }}</div>
    </div>
    <div class="summary-card">
      <div class="card-label">Net Day Surplus / Deficit</div>
      <div class="card-val" style="color: {{ $netAmount >= 0 ? '#15803d' : '#be123c' }};">
        {{ $netAmount >= 0 ? '+' : '' }}₹{{ number_format($netAmount, 2) }}
      </div>
    </div>
    <div class="summary-card">
      <div class="card-label">Total Transactions</div>
      <div class="card-val" style="color: #0f172a;">{{ $ledger->count() }} Entries</div>
    </div>
  </div>

  {{-- Account Breakdown --}}
  <div class="summary-grid" style="grid-template-columns: repeat(3, 1fr);">
    <div class="summary-card" style="border-left: 3px solid #9333ea;">
      <div class="card-label">UPI Account Day Movement</div>
      <div style="font-size: 10px; margin-top: 3px;">
        In: <strong style="color: #15803d;">₹{{ number_format($accountsMovement['UPI']['in'] ?? 0, 2) }}</strong> &bull;
        Out: <strong style="color: #be123c;">₹{{ number_format($accountsMovement['UPI']['out'] ?? 0, 2) }}</strong>
      </div>
    </div>
    <div class="summary-card" style="border-left: 3px solid #0284c7;">
      <div class="card-label">Bank Account Movement</div>
      <div style="font-size: 10px; margin-top: 3px;">
        In: <strong style="color: #15803d;">₹{{ number_format($accountsMovement['Bank']['in'] ?? 0, 2) }}</strong> &bull;
        Out: <strong style="color: #be123c;">₹{{ number_format($accountsMovement['Bank']['out'] ?? 0, 2) }}</strong>
      </div>
    </div>
    <div class="summary-card" style="border-left: 3px solid #16a34a;">
      <div class="card-label">Cash-in-Hand Movement</div>
      <div style="font-size: 10px; margin-top: 3px;">
        In: <strong style="color: #15803d;">₹{{ number_format($accountsMovement['Cash']['in'] ?? 0, 2) }}</strong> &bull;
        Out: <strong style="color: #be123c;">₹{{ number_format($accountsMovement['Cash']['out'] ?? 0, 2) }}</strong>
      </div>
    </div>
  </div>

  {{-- Ledger Entries Table --}}
  <table class="data-table">
    <thead>
      <tr>
        <th style="width: 35px;" class="text-center">#</th>
        <th style="width: 60px;">Time</th>
        <th style="width: 90px;">Voucher / Ref</th>
        <th style="width: 70px;">Type</th>
        <th>Particulars / Account Head</th>
        <th style="width: 80px;">Source / Mode</th>
        <th style="width: 130px;">Party / Payee</th>
        <th style="width: 85px;" class="text-right">Inflow (₹)</th>
        <th style="width: 85px;" class="text-right">Outflow (₹)</th>
        <th style="width: 85px;">Operator</th>
      </tr>
    </thead>
    <tbody>
      @forelse($ledger as $idx => $entry)
        <tr>
          <td class="text-center font-mono">{{ $idx + 1 }}</td>
          <td class="font-mono text-center">{{ date('H:i', strtotime($entry['time'])) }}</td>
          <td class="font-mono font-bold">{{ $entry['reference'] ?? '—' }}</td>
          <td class="text-center">
            @if($entry['type'] === 'inflow')
              <span class="badge badge-inflow">Inflow</span>
            @elseif($entry['type'] === 'outflow')
              <span class="badge badge-outflow">Outflow</span>
            @else
              <span class="badge badge-trf">Transfer</span>
            @endif
          </td>
          <td class="font-bold">{{ $entry['title'] }}</td>
          <td>{{ $entry['mode'] }}</td>
          <td>{{ $entry['party'] ?? '—' }}</td>
          <td class="text-right font-mono font-bold" style="color: {{ $entry['inflow'] > 0 ? '#15803d' : '#94a3b8' }};">
            {{ $entry['inflow'] > 0 ? '₹' . number_format($entry['inflow'], 2) : '—' }}
          </td>
          <td class="text-right font-mono font-bold" style="color: {{ $entry['outflow'] > 0 ? '#be123c' : '#94a3b8' }};">
            {{ $entry['outflow'] > 0 ? '₹' . number_format($entry['outflow'], 2) : '—' }}
          </td>
          <td>{{ $entry['operator'] ?? 'Admin' }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="10" class="text-center" style="padding: 20px; color: #64748b;">
            No transactions recorded on this date.
          </td>
        </tr>
      @endforelse
    </tbody>
    <tfoot>
      <tr style="background: #f1f5f9; font-weight: 800;">
        <td colspan="7" class="text-right" style="text-transform: uppercase;">Totals For The Day:</td>
        <td class="text-right font-mono" style="color: #15803d; font-size: 10.5px;">₹{{ number_format($totalInflow, 2) }}</td>
        <td class="text-right font-mono" style="color: #be123c; font-size: 10.5px;">₹{{ number_format($totalOutflow, 2) }}</td>
        <td></td>
      </tr>
    </tfoot>
  </table>

  {{-- Signatures & Footer --}}
  <div style="margin-top: 24px;">
    <x-print.signatures mode="financial" />
  </div>

  <x-print.footer />

</body>
</html>
