<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Hostel Gate Pass #{{ str_pad($outpass->id, 6, '0', STR_PAD_LEFT) }}</title>
<style>
  @page {
    size: A5 portrait;
    margin: 10mm 12mm 12mm 12mm;
  }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 9.5px;
    color: #1e293b;
    margin: 0;
    padding: 0;
  }
  table.pass-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 6px;
    margin-bottom: 10px;
  }
  table.pass-table td {
    padding: 4.5px 6px;
    border-bottom: 1px solid #e2e8f0;
    font-size: 9px;
    vertical-align: top;
  }
  table.pass-table td.lbl {
    width: 35%;
    font-size: 8.5px;
    text-transform: uppercase;
    color: #64748b;
    font-weight: bold;
    background: #f8fafc;
  }
  table.pass-table td.val {
    font-weight: 600;
    color: #0f172a;
  }
  .highlight-box {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    padding: 6px 10px;
    margin: 8px 0;
    border-radius: 4px;
  }
  .qr-section {
    text-align: center;
    margin: 10px 0;
  }
  .qr-section img {
    width: 80px;
    height: 80px;
    border: 1px solid #cbd5e1;
    padding: 2px;
    display: inline-block;
  }
</style>
</head>
<body>
@include('pdf.partials.watermark')

@include('pdf.partials.header', [
  'title' => 'Hostel Resident Outpass & Gate Pass',
  'subtitle' => 'Pass #' . str_pad($outpass->id, 6, '0', STR_PAD_LEFT) . ' | Status: APPROVED'
])

<div class="highlight-box">
  <table style="width: 100%; border-collapse: collapse; border: none;">
    <tr>
      <td style="width: 50%; font-size: 9px; border: none; padding: 0;">
        <span style="color: #1e40af; font-weight: bold;">Valid From:</span><br>
        <strong>{{ \Carbon\Carbon::parse($outpass->from_datetime)->format('d M Y, h:i A') }}</strong>
      </td>
      <td style="width: 50%; text-align: right; font-size: 9px; border: none; padding: 0;">
        <span style="color: #1e40af; font-weight: bold;">Valid Until:</span><br>
        <strong>{{ \Carbon\Carbon::parse($outpass->to_datetime)->format('d M Y, h:i A') }}</strong>
      </td>
    </tr>
  </table>
</div>

<table class="pass-table">
  <tr>
    <td class="lbl">Student Resident</td>
    <td class="val">{{ $outpass->student?->full_name }}</td>
  </tr>
  <tr>
    <td class="lbl">Admission Number</td>
    <td class="val" style="font-family: monospace;">{{ $outpass->student?->admission_no ?? $outpass->student?->admission_number ?? '—' }}</td>
  </tr>
  <tr>
    <td class="lbl">Assigned Room</td>
    <td class="val">Room {{ $outpass->allotment?->room?->room_number ?? '—' }} ({{ $outpass->allotment?->room?->hostel?->name ?? 'Hostel' }})</td>
  </tr>
  @if($outpass->destination)
  <tr>
    <td class="lbl">Destination</td>
    <td class="val">{{ $outpass->destination }}</td>
  </tr>
  @endif
  @if($outpass->reason)
  <tr>
    <td class="lbl">Reason / Purpose</td>
    <td class="val">{{ $outpass->reason }}</td>
  </tr>
  @endif
  @if($outpass->parent_contact)
  <tr>
    <td class="lbl">Parent Contact</td>
    <td class="val">{{ $outpass->parent_contact }}</td>
  </tr>
  @endif
  <tr>
    <td class="lbl">Approved Date</td>
    <td class="val">{{ \Carbon\Carbon::parse($outpass->approved_at ?? $outpass->updated_at)->format('d M Y, h:i A') }}</td>
  </tr>
</table>

@if($qrCode)
<div class="qr-section">
  <img src="data:image/png;base64,{{ $qrCode }}" alt="Gate Pass QR">
  <div style="font-size: 7.5px; color: #64748b; margin-top: 2px;">Scan at Security Gate to verify</div>
</div>
@endif

<table style="width: 100%; border-collapse: collapse; border: none; margin-top: 20px; page-break-inside: avoid;">
  <tr>
    <td style="width: 50%; text-align: left; vertical-align: bottom; border: none;">
      <div style="border-top: 1px dashed #64748b; width: 110px; margin-bottom: 3px;"></div>
      <span style="font-size: 8px; font-weight: bold; color: #334155;">Hostel Warden</span>
    </td>
    <td style="width: 50%; text-align: right; vertical-align: bottom; border: none;">
      <div style="border-top: 1px dashed #64748b; width: 110px; margin-left: auto; margin-bottom: 3px;"></div>
      <span style="font-size: 8px; font-weight: bold; color: #334155;">Security Check / Out</span>
    </td>
  </tr>
</table>

@include('pdf.partials.footer', [
  'footerNote' => 'Valid gate pass of ' . ($school?->school_name ?? 'the Institution') . '. Student must report back to warden on arrival.'
])
</body>
</html>
