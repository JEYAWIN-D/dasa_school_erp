<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Purchase Order — {{ $po->po_number }}</title>
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
  .badge-draft    { background: #f1f5f9; color: #475569; }
  .badge-sent     { background: #dbeafe; color: #1e40af; }
  .badge-partial  { background: #fef3c7; color: #92400e; }
  .badge-received { background: #dcfce7; color: #166534; }
  @media print {
    body { padding: 0 !important; background: #fff !important; }
    .no-print { display: none !important; }
    * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
  }
</style>
</head>
<body>

{{-- Print Toolbar --}}
<x-print.controls title="Purchase Order Document" defaultSize="a4" defaultOrientation="portrait" :backUrl="route('inventory.purchase-orders')" />

{{-- Watermark & Centralized Header --}}
<x-print.watermark />
<x-print.header 
  title="OFFICIAL PURCHASE ORDER" 
  subtitle="Institutional Procurement & Supply Requisition Order" 
  :academicYear="now()->year . '-' . (now()->year + 1)" 
  :date="\Carbon\Carbon::parse($po->order_date)->format('d M Y')"
/>

<table class="meta-table">
  <tr>
    <td style="width: 48%;">
      <div class="box">
        <div class="box-title">Order Information</div>
        <p style="margin: 2px 0;"><strong>PO Number:</strong> <span style="font-family: monospace; font-weight: bold; color: #0f172a;">{{ $po->po_number }}</span></p>
        <p style="margin: 2px 0;"><strong>Order Date:</strong> {{ \Carbon\Carbon::parse($po->order_date)->format('d M Y') }}</p>
        @if($po->delivery_date)
        <p style="margin: 2px 0;"><strong>Delivery Required By:</strong> {{ \Carbon\Carbon::parse($po->delivery_date)->format('d M Y') }}</p>
        @endif
        <p style="margin: 2px 0;"><strong>Order Status:</strong> <span class="badge badge-{{ $po->status }}">{{ strtoupper($po->status) }}</span></p>
        @if($po->payment_terms)
        <p style="margin: 2px 0;"><strong>Payment Terms:</strong> {{ $po->payment_terms }}</p>
        @endif
      </div>
    </td>
    <td style="width: 4%;"></td>
    <td style="width: 48%;">
      <div class="box">
        <div class="box-title">Vendor / Supplier Details</div>
        <p style="margin: 2px 0;"><strong>Company:</strong> {{ $po->vendor?->name ?? '—' }}</p>
        @if($po->vendor?->contact_person)<p style="margin: 2px 0;"><strong>Contact Person:</strong> {{ $po->vendor->contact_person }}</p>@endif
        @if($po->vendor?->phone)<p style="margin: 2px 0;"><strong>Phone:</strong> {{ $po->vendor->phone }}</p>@endif
        @if($po->vendor?->email)<p style="margin: 2px 0;"><strong>Email:</strong> {{ $po->vendor->email }}</p>@endif
        @if($po->vendor?->gstin)<p style="margin: 2px 0;"><strong>GSTIN:</strong> {{ $po->vendor->gstin }}</p>@endif
        @if($po->vendor?->address)<p style="margin: 2px 0;"><strong>Address:</strong> {{ $po->vendor->address }}</p>@endif
      </div>
    </td>
  </tr>
</table>

@if($po->shipping_address)
<div style="margin-bottom: 10px; font-size: 9px; color: #475569; background: #f8fafc; border: 1px solid #cbd5e1; padding: 6px 10px; border-radius: 4px;">
  <strong>Delivery / Shipping Destination:</strong> {{ $po->shipping_address }}
</div>
@endif

<table class="data-table">
  <thead>
    <tr>
      <th style="width: 5%; text-align:center;">#</th>
      <th>Item Description &amp; Specifications</th>
      <th style="width: 12%; text-align:center;">Ordered Qty</th>
      <th style="width: 18%; text-align:right;">Unit Price (₹)</th>
      <th style="width: 18%; text-align:right;">Total Amount (₹)</th>
      <th style="width: 12%; text-align:center;">Received Qty</th>
    </tr>
  </thead>
  <tbody>
    @php $grandTotal = 0; @endphp
    @foreach($po->items as $i => $line)
    @php
      $lineTotal = ($line->unit_price ?? 0) * $line->quantity;
      $grandTotal += $lineTotal;
    @endphp
    <tr>
      <td style="text-align:center; color:#64748b;">{{ $i + 1 }}</td>
      <td style="font-weight:600;">{{ $line->inventoryItem?->name ?? $line->description ?? '—' }}</td>
      <td style="text-align:center; font-weight:bold;">{{ $line->quantity }}</td>
      <td style="text-align:right;">{{ $line->unit_price ? number_format($line->unit_price, 2) : '—' }}</td>
      <td style="text-align:right; font-weight:bold;">{{ $lineTotal > 0 ? number_format($lineTotal, 2) : '—' }}</td>
      <td style="text-align:center;">{{ $line->received_qty ?? 0 }}</td>
    </tr>
    @endforeach
    <tr class="total-row">
      <td colspan="4" style="text-align:right; text-transform:uppercase; font-size:9px; color:#475569;">Grand Total Outlay:</td>
      <td style="text-align:right; color:#166534; font-size:11px;">₹{{ number_format($grandTotal, 2) }}</td>
      <td></td>
    </tr>
  </tbody>
</table>

@if($po->notes)
<div style="margin-top:10px; font-size:9px; color:#475569; border:1px dashed #cbd5e1; padding:6px 10px; border-radius:4px; background:#f8fafc;">
  <strong>Special Purchase Instructions / Remarks:</strong> {{ $po->notes }}
</div>
@endif

<div style="margin-top: 24px;">
  <x-print.signatures mode="financial" />
</div>

<div style="margin-top: 14px; font-size: 8px; color: #64748b; border-top: 1px solid #e2e8f0; padding-top: 6px;">
  This Purchase Order is an official legal instrument of procurement. All supplies must adhere to specifications, quality standards, and delivery commitments indicated above.
</div>

<x-print.footer />

</body>
</html>
