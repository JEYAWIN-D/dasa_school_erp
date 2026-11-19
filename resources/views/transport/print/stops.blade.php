<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Master Bus Stop Directory — {{ $school?->school_name ?? 'Erode Public School' }}</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <style>
    @page { 
      size: A4 portrait; 
      margin: 0 !important; 
    }
    * { 
      box-sizing: border-box; 
      -webkit-print-color-adjust: exact !important; 
      print-color-adjust: exact !important; 
    }
    body { 
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; 
      color: #0f172a; 
      font-size: 8pt; 
      line-height: 1.35; 
      margin: 0 auto; 
      max-width: 210mm;
      padding: 10mm 12mm 10mm 18mm; 
      background: #ffffff; 
    }
    @media print {
      body { 
        padding: 10mm 12mm 10mm 18mm !important; 
        margin: 0 !important; 
        max-width: 100% !important;
        background: #ffffff !important;
      }
      .no-print { display: none !important; }
    }
    table.print-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 6px;
      font-size: 7.5pt;
    }
    table.print-table th {
      background: #f1f5f9;
      border: 1px solid #cbd5e1;
      padding: 4px 5px;
      text-align: left;
      font-weight: 800;
      text-transform: uppercase;
      font-size: 7pt;
      color: #334155;
      white-space: nowrap;
    }
    table.print-table td {
      border: 1px solid #cbd5e1;
      padding: 4px 5px;
      vertical-align: middle;
    }
    .signatures-block { 
      margin-top: 24px; 
      display: flex; 
      justify-content: space-between; 
      font-weight: 800; 
      font-size: 8.5pt; 
      page-break-inside: avoid;
    }
    .sig-line { 
      border-top: 1px solid #475569; 
      width: 140px; 
      text-align: center; 
      padding-top: 4px; 
      color: #334155;
    }
  </style>
</head>
<body>

  {{-- Print Controls --}}
  <x-print.controls title="Master Bus Stops Register" defaultSize="a4" defaultOrientation="portrait" :backUrl="route('transport.stops')" />

  {{-- Background Watermark --}}
  <x-print.watermark />

  {{-- Centralized Official Header --}}
  <x-print.header 
    title="MASTER BUS STOP DIRECTORY & SCHEDULE" 
    subtitle="Sequential Boarding Points, Morning Pickup, Evening Drop & Transit Fare Schedule" 
    :academicYear="$academicYear?->name ?? (now()->year . '-' . (now()->year + 1))" 
    :school="$school" 
  />

  <div style="font-size: 8pt; font-weight: 700; color: #64748b; margin-bottom: 4px; display: flex; justify-content: space-between;">
    <span>Total Registered Stops: {{ $stops->count() }}</span>
    <span>Printed On: {{ now()->format('d M Y, h:i A') }}</span>
  </div>

  <table class="print-table">
    <thead>
      <tr>
        <th style="width: 25px; text-align: center;">#</th>
        <th>Stop Name &amp; Landmark</th>
        <th>Corridor / Van</th>
        <th style="width: 70px; text-align: center;">Morning Pickup</th>
        <th style="width: 70px; text-align: center;">Evening Drop</th>
        <th style="width: 50px; text-align: right;">Distance</th>
        <th style="width: 55px; text-align: right;">Fare</th>
        <th style="width: 45px; text-align: center;">Students</th>
      </tr>
    </thead>
    <tbody>
      @php
        $vanCodes = [5 => 'VAN I', 6 => 'VAN II', 7 => 'VAN III', 8 => 'VAN IV', 9 => 'VAN V'];
      @endphp
      @forelse($stops as $idx => $s)
        @php
          $vanLabel = $s->vehicle_id && isset($vanCodes[$s->vehicle_id]) ? $vanCodes[$s->vehicle_id] : ($s->vehicle?->vehicle_number ?? '');
          $corridor = $s->route?->route_name ?? '—';
        @endphp
        <tr>
          <td style="text-align: center; font-weight: 700; color: #64748b;">{{ $idx + 1 }}</td>
          <td>
            <strong style="color: #0f172a;">{{ $s->stop_name ?? $s->name }}</strong>
            @if($s->landmark)
              <div style="font-size: 7pt; color: #64748b;">Near {{ $s->landmark }}</div>
            @endif
          </td>
          <td>
            <div style="font-weight: 700; color: #4338ca;">{{ $corridor }}</div>
            @if($vanLabel)
              <div style="font-size: 7pt; color: #8C2826; font-weight: 700;">{{ $vanLabel }}</div>
            @endif
          </td>
          <td style="text-align: center; font-weight: 700; color: #166534; font-family: monospace;">
            {{ $s->pickup_time ? \Carbon\Carbon::parse($s->pickup_time)->format('h:i A') : ($s->morning_time ?? '—') }}
          </td>
          <td style="text-align: center; font-weight: 700; color: #991b1b; font-family: monospace;">
            {{ $s->drop_time ? \Carbon\Carbon::parse($s->drop_time)->format('h:i A') : ($s->evening_time ?? '—') }}
          </td>
          <td style="text-align: right; color: #475569;">{{ $s->distance_km ? number_format($s->distance_km, 1) . ' km' : '—' }}</td>
          <td style="text-align: right; font-weight: 700;">{{ $s->fare ? '₹' . number_format($s->fare, 0) : '—' }}</td>
          <td style="text-align: center; font-weight: 800; color: #4338ca;">{{ $s->students_count ?? 0 }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="8" style="text-align: center; color: #64748b; padding: 12px;">No bus stops found.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  {{-- Signatures --}}
  <div class="signatures-block">
    <div class="sig-line">
      <div>Transport Coordinator</div>
      <div style="font-size: 7pt; font-weight: normal; color: #64748b; margin-top: 2px;">Operations Desk</div>
    </div>
    <div class="sig-line">
      <div>Administrative Officer</div>
      <div style="font-size: 7pt; font-weight: normal; color: #64748b; margin-top: 2px;">School Administration</div>
    </div>
    <div class="sig-line">
      <div>Principal / Headmistress</div>
      <div style="font-size: 7pt; font-weight: normal; color: #64748b; margin-top: 2px;">Institutional Seal</div>
    </div>
  </div>

</body>
</html>

