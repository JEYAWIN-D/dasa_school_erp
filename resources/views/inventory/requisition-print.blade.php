<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Purchase Requisition — {{ $pr->pr_number }}</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
<style>
  @page { size: A4 portrait; margin: 0 !important; }
  body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; font-size: 10px; color: #1e293b; margin: 0; padding: 10mm 12mm 10mm 18mm !important; box-sizing: border-box !important; background: #fff; }
  table.meta-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
  table.meta-table td { vertical-align: top; border: none; padding: 0; }
  .box { background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px 12px; }
  .box-title { font-size: 9px; font-weight: bold; color: #475569; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; padding-bottom: 3px; margin-bottom: 6px; }
  table.data-table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 9.5px; }
  table.data-table th { background: #1e293b; color: white; border: 1px solid #334155; padding: 6px 8px; text-align: left; font-size: 9px; text-transform: uppercase; }
  table.data-table td { border: 1px solid #cbd5e1; padding: 5px 8px; }
  table.data-table tr:nth-child(even) { background: #f8fafc; }
  .total-row td { font-weight: bold; background: #f1f5f9; }
  .badge { display: inline-block; padding: 1px 6px; border-radius: 3px; font-size: 8.5px; font-weight: bold; text-transform: uppercase; }
  .badge-pending  { background: #fef3c7; color: #92400e; }
  .badge-approved { background: #dcfce7; color: #166534; }
  .badge-rejected { background: #fee2e2; color: #991b1b; }
  @media print {
    body { padding: 0 !important; background: #fff !important; }
    .no-print { display: none !important; }
    * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
  }
</style>
</head>
<body>

{{-- Print Toolbar --}}
<x-print.controls title="Purchase Requisition Document" defaultSize="a4" defaultOrientation="portrait" :backUrl="route('inventory.requisitions')" />

{{-- Watermark & Centralized Header --}}
<x-print.watermark />
<x-print.header 
  title="OFFICIAL PURCHASE REQUISITION (PR)" 
  subtitle="Internal Department Material Indent & Requirement Authorization" 
  :academicYear="now()->year . '-' . (now()->year + 1)" 
  :date="$pr->created_at->format('d M Y')"
/>

<table class="meta-table">
  <tr>
    <td style="width: 48%;">
      <div class="box">
        <div class="box-title">Requisition Particulars</div>
        <p style="margin: 2px 0;"><strong>PR Number:</strong> <span style="font-family: monospace; font-weight: bold; color: #0f172a;">{{ $pr->pr_number }}</span></p>
        <p style="margin: 2px 0;"><strong>Requisition Date:</strong> {{ $pr->created_at->format('d M Y') }}</p>
        <p style="margin: 2px 0;"><strong>Required On / By:</strong> {{ \Carbon\Carbon::parse($pr->required_by)->format('d M Y') }}</p>
      </div>
    </td>
    <td style="width: 4%;"></td>
    <td style="width: 48%;">
      <div class="box">
        <div class="box-title">Department &amp; Authorization</div>
        <p style="margin: 2px 0;"><strong>Indent Originator:</strong> {{ $pr->requestedBy?->name ?? '—' }}</p>
        <p style="margin: 2px 0;"><strong>Approval Status:</strong> <span class="badge badge-{{ $pr->status }}">{{ strtoupper($pr->status) }}</span></p>
        @if($pr->purpose)
        <p style="margin: 2px 0;"><strong>Operational Purpose:</strong> {{ $pr->purpose }}</p>
        @endif
      </div>
    </td>
  </tr>
</table>

<table class="data-table">
  <thead>
    <tr>
      <th style="width: 5%; text-align:center;">#</th>
      <th>Item Description</th>
      <th style="width: 18%;">Category / Type</th>
      <th style="width: 12%; text-align:center;">Indent Qty</th>
      <th style="width: 16%; text-align:right;">Est. Unit Cost (₹)</th>
      <th style="width: 18%; text-align:right;">Est. Total (₹)</th>
      <th style="width: 18%;">Remark / Justification</th>
    </tr>
  </thead>
  <tbody>
    @php $grandTotal = 0; @endphp
    @foreach($pr->items as $i => $line)
    @php
      $lineTotal = ($line->estimated_price ?? 0) * $line->quantity;
      $grandTotal += $lineTotal;
    @endphp
    <tr>
      <td style="text-align:center; color:#64748b;">{{ $i + 1 }}</td>
      <td style="font-weight:600;">{{ $line->inventoryItem?->name ?? '—' }}</td>
      <td>{{ $line->inventoryItem?->category ?? '—' }}</td>
      <td style="text-align:center; font-weight:bold;">{{ $line->quantity }}</td>
      <td style="text-align:right;">{{ $line->estimated_price ? number_format($line->estimated_price, 2) : '—' }}</td>
      <td style="text-align:right; font-weight:bold;">{{ $lineTotal > 0 ? number_format($lineTotal, 2) : '—' }}</td>
      <td>{{ $line->remark ?? '—' }}</td>
    </tr>
    @endforeach
    <tr class="total-row">
      <td colspan="5" style="text-align:right; text-transform:uppercase; font-size:9px; color:#475569;">Cumulative Estimated Outlay:</td>
      <td style="text-align:right; color:#166534; font-size:11px;">₹{{ number_format($grandTotal, 2) }}</td>
      <td></td>
    </tr>
  </tbody>
</table>

<div style="margin-top: 24px;">
  <x-print.signatures mode="standard" />
</div>

<x-print.footer />

</body>
</html>
