<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Route Schedule — {{ $route->route_name }} — {{ $school?->school_name ?? 'Erode Public School' }}</title>
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
      font-size: 8.5pt; 
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
    .sec-title { 
      font-size: 9pt; 
      font-weight: 800; 
      text-transform: uppercase; 
      color: #8C2826; 
      border-bottom: 1.5px solid #8C2826; 
      padding-bottom: 2px; 
      margin: 10px 0 5px 0; 
      letter-spacing: 0.5px; 
    }
    .info-card {
      border: 1px solid #cbd5e1;
      border-radius: 6px;
      padding: 6px 10px;
      background: #f8fafc;
      margin-bottom: 8px;
    }
    table.print-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 4px;
      font-size: 8pt;
    }
    table.print-table th {
      background: #f1f5f9;
      border: 1px solid #cbd5e1;
      padding: 4px 6px;
      text-align: left;
      font-weight: 800;
      text-transform: uppercase;
      font-size: 7.5pt;
      color: #334155;
      white-space: nowrap;
    }
    table.print-table td {
      border: 1px solid #cbd5e1;
      padding: 4px 6px;
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
      width: 150px; 
      text-align: center; 
      padding-top: 4px; 
      color: #334155;
    }
  </style>
</head>
<body>

  {{-- Print Controls --}}
  <x-print.controls title="Route Schedule — {{ $route->route_name }}" defaultSize="a4" defaultOrientation="portrait" :backUrl="route('transport.routes')" />

  {{-- Background Watermark --}}
  <x-print.watermark />

  {{-- Centralized Official Header --}}
  <x-print.header 
    title="OFFICIAL TRANSIT ROUTE & STOP DIRECTORY" 
    subtitle="Corridor Navigation, Sequential Stops, Boarding Timings & Assigned Crew" 
    :academicYear="$academicYear?->name ?? (now()->year . '-' . (now()->year + 1))" 
    :school="$school" 
  />

  @php
    $vehicle = $route->vehicle;
  @endphp

  {{-- Route & Vehicle Overview --}}
  <div class="sec-title">1. Transit Corridor &amp; Fleet Association</div>
  <div class="info-card">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 8pt;">
      <div>
        <div style="margin-bottom: 2px;"><span style="color: #64748b; font-weight: 600; width: 100px; display: inline-block;">Route Corridor:</span> <strong style="color: #8C2826; font-size: 9pt;">{{ $route->route_name }}</strong></div>
        <div style="margin-bottom: 2px;"><span style="color: #64748b; font-weight: 600; width: 100px; display: inline-block;">Origin &rarr; Dest:</span> <strong>{{ $route->origin ?? 'School Campus' }} &rarr; {{ $route->destination ?? 'Terminal Stop' }}</strong></div>
        <div><span style="color: #64748b; font-weight: 600; width: 100px; display: inline-block;">Total Distance:</span> <strong>{{ $route->distance_km ? number_format($route->distance_km, 1) . ' km' : '—' }}</strong></div>
      </div>
      <div>
        <div style="margin-bottom: 2px;"><span style="color: #64748b; font-weight: 600; width: 110px; display: inline-block;">Assigned Van:</span> <strong style="color: #4338ca;">{{ $vehicle?->vehicle_number ?? 'Not Assigned' }}</strong></div>
        <div style="margin-bottom: 2px;"><span style="color: #64748b; font-weight: 600; width: 110px; display: inline-block;">Driver &amp; Mobile:</span> <strong>{{ $vehicle?->driver_name ?? '—' }} ({{ $vehicle?->driver_phone ?? $vehicle?->driver_mobile ?? '—' }})</strong></div>
        <div><span style="color: #64748b; font-weight: 600; width: 110px; display: inline-block;">Nanny / Attendant:</span> <strong>{{ $vehicle?->attendants?->first()?->name ?? $vehicle?->attendant_name ?? '—' }} ({{ $vehicle?->attendants?->first()?->phone ?? $vehicle?->attendant_mobile ?? '—' }})</strong></div>
      </div>
    </div>
  </div>

  {{-- Sequential Stops Table --}}
  <div class="sec-title" style="display: flex; justify-content: space-between;">
    <span>2. Sequential Stops &amp; Schedule</span>
    <span style="font-size: 8pt; color: #0f172a;">Total Stops: {{ $stops->count() }}</span>
  </div>
  <table class="print-table">
    <thead>
      <tr>
        <th style="width: 35px; text-align: center;">Seq</th>
        <th>Stop Name</th>
        <th>Landmark</th>
        <th style="width: 80px; text-align: center;">Morning Pickup</th>
        <th style="width: 80px; text-align: center;">Evening Drop</th>
        <th style="width: 65px; text-align: right;">Distance</th>
        <th style="width: 70px; text-align: right;">Monthly Fare</th>
        <th style="width: 60px; text-align: center;">Students</th>
      </tr>
    </thead>
    <tbody>
      @forelse($stops as $idx => $stop)
        @php
          $stuCount = $stopStudentCounts[$stop->id] ?? 0;
        @endphp
        <tr>
          <td style="text-align: center; font-weight: 800; color: #475569;">{{ $stop->stop_order ?? ($idx + 1) }}</td>
          <td style="font-weight: 700; color: #0f172a;">{{ $stop->stop_name ?? $stop->name }}</td>
          <td style="color: #475569;">{{ $stop->landmark ?? '—' }}</td>
          <td style="text-align: center; font-weight: 700; color: #166534; font-family: monospace;">
            {{ $stop->pickup_time ? \Carbon\Carbon::parse($stop->pickup_time)->format('h:i A') : ($stop->morning_time ?? '—') }}
          </td>
          <td style="text-align: center; font-weight: 700; color: #991b1b; font-family: monospace;">
            {{ $stop->drop_time ? \Carbon\Carbon::parse($stop->drop_time)->format('h:i A') : ($stop->evening_time ?? '—') }}
          </td>
          <td style="text-align: right; color: #475569;">{{ $stop->distance_km ? number_format($stop->distance_km, 1) . ' km' : '—' }}</td>
          <td style="text-align: right; font-weight: 700;">{{ $stop->fare ? '₹' . number_format($stop->fare, 0) : '—' }}</td>
          <td style="text-align: center; font-weight: 800; color: #4338ca;">{{ $stuCount }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="8" style="text-align: center; color: #64748b; padding: 12px;">No stops mapped to this route corridor.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  {{-- Signatures --}}
  <div class="signatures-block">
    <div class="sig-line">
      <div>Driver Signature</div>
      <div style="font-size: 7.5pt; font-weight: normal; color: #64748b; margin-top: 2px;">{{ $vehicle?->driver_name ?? 'Designated Driver' }}</div>
    </div>
    <div class="sig-line">
      <div>Attendant Signature</div>
      <div style="font-size: 7.5pt; font-weight: normal; color: #64748b; margin-top: 2px;">{{ $vehicle?->attendants?->first()?->name ?? 'Designated Attendant' }}</div>
    </div>
    <div class="sig-line">
      <div>Transport Coordinator</div>
      <div style="font-size: 7.5pt; font-weight: normal; color: #64748b; margin-top: 2px;">Operations Desk</div>
    </div>
    <div class="sig-line">
      <div>Principal / Headmistress</div>
      <div style="font-size: 7.5pt; font-weight: normal; color: #64748b; margin-top: 2px;">Institutional Seal</div>
    </div>
  </div>

</body>
</html>

