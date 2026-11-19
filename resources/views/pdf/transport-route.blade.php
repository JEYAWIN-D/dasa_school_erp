<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <title>Route Schedule — {{ $route->route_name }}</title>
  <style>
    @page { margin: 10mm 12mm 10mm 15mm; }
    body {
      font-family: Arial, Helvetica, sans-serif;
      font-size: 8pt;
      line-height: 1.3;
      color: #0f172a;
      margin: 0;
      padding: 0;
    }
    .sec-title {
      font-size: 8.5pt;
      font-weight: bold;
      text-transform: uppercase;
      color: #8C2826;
      border-bottom: 1.5px solid #8C2826;
      padding-bottom: 2px;
      margin: 10px 0 4px 0;
    }
    table.data-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 4px;
      font-size: 7.5pt;
    }
    table.data-table th {
      background-color: #f1f5f9;
      border: 1px solid #94a3b8;
      padding: 4px 5px;
      text-align: left;
      font-weight: bold;
      text-transform: uppercase;
      font-size: 7pt;
      color: #1e293b;
    }
    table.data-table td {
      border: 1px solid #cbd5e1;
      padding: 4px 5px;
      vertical-align: middle;
    }
    .inner-box {
      border: 1px solid #cbd5e1;
      background-color: #f8fafc;
      padding: 6px 10px;
      margin-bottom: 8px;
    }
    .signatures-table {
      width: 100%;
      margin-top: 30px;
      border-collapse: collapse;
    }
    .signatures-table td {
      width: 25%;
      text-align: center;
      vertical-align: top;
      border: none;
      font-size: 8pt;
    }
    .sig-line {
      border-top: 1px solid #475569;
      width: 80%;
      margin: 0 auto;
      padding-top: 3px;
      font-weight: bold;
    }
  </style>
</head>
<body>

  {{-- Centralized Official Header --}}
  @include('pdf.partials.header', [
    'title' => 'OFFICIAL TRANSIT ROUTE & STOP DIRECTORY',
    'subtitle' => 'Corridor Navigation, Sequential Stops, Boarding Timings & Assigned Crew',
    'academicYear' => $academicYear?->name ?? (now()->year . '-' . (now()->year + 1)),
    'school' => $school
  ])

  @php
    $vehicle = $route->vehicle;
  @endphp

  {{-- Route & Vehicle Overview --}}
  <div class="sec-title">1. Transit Corridor &amp; Fleet Association</div>
  <div class="inner-box">
    <table style="width: 100%; font-size: 8pt; border-collapse: collapse;">
      <tr>
        <td style="width: 50%; vertical-align: top; border: none; padding: 2px;">
          <div style="margin-bottom: 2px;"><strong style="color: #64748b;">Route Corridor:</strong> <strong style="color: #8C2826;">{{ $route->route_name }}</strong></div>
          <div style="margin-bottom: 2px;"><strong style="color: #64748b;">Origin &rarr; Destination:</strong> {{ $route->origin ?? 'School Campus' }} &rarr; {{ $route->destination ?? 'Terminal' }}</div>
          <div><strong style="color: #64748b;">Distance:</strong> {{ $route->distance_km ? number_format($route->distance_km, 1) . ' km' : '—' }}</div>
        </td>
        <td style="width: 50%; vertical-align: top; border: none; padding: 2px;">
          <div style="margin-bottom: 2px;"><strong style="color: #64748b;">Assigned Vehicle:</strong> <strong style="color: #4338ca;">{{ $vehicle?->vehicle_number ?? 'Not Assigned' }}</strong></div>
          <div style="margin-bottom: 2px;"><strong style="color: #64748b;">Driver:</strong> {{ $vehicle?->driver_name ?? '—' }} ({{ $vehicle?->driver_phone ?? $vehicle?->driver_mobile ?? '—' }})</div>
          <div><strong style="color: #64748b;">Attendant:</strong> {{ $vehicle?->attendants?->first()?->name ?? $vehicle?->attendant_name ?? '—' }}</div>
        </td>
      </tr>
    </table>
  </div>

  {{-- Sequential Stops Table --}}
  <div class="sec-title">2. Sequential Stops &amp; Schedule &mdash; Total Stops: {{ $stops->count() }}</div>
  <table class="data-table">
    <thead>
      <tr>
        <th style="width: 25px; text-align: center;">Seq</th>
        <th>Stop Name</th>
        <th>Landmark</th>
        <th style="width: 75px; text-align: center;">Morning Pickup</th>
        <th style="width: 75px; text-align: center;">Evening Drop</th>
        <th style="width: 55px; text-align: right;">Distance</th>
        <th style="width: 60px; text-align: right;">Fare</th>
        <th style="width: 50px; text-align: center;">Students</th>
      </tr>
    </thead>
    <tbody>
      @forelse($stops as $idx => $stop)
        @php
          $stuCount = $stopStudentCounts[$stop->id] ?? 0;
        @endphp
        <tr>
          <td style="text-align: center; font-weight: bold;">{{ $stop->stop_order ?? ($idx + 1) }}</td>
          <td style="font-weight: bold;">{{ $stop->stop_name ?? $stop->name }}</td>
          <td>{{ $stop->landmark ?? '—' }}</td>
          <td style="text-align: center; font-weight: bold; color: #166534;">
            {{ $stop->pickup_time ? \Carbon\Carbon::parse($stop->pickup_time)->format('h:i A') : ($stop->morning_time ?? '—') }}
          </td>
          <td style="text-align: center; font-weight: bold; color: #991b1b;">
            {{ $stop->drop_time ? \Carbon\Carbon::parse($stop->drop_time)->format('h:i A') : ($stop->evening_time ?? '—') }}
          </td>
          <td style="text-align: right;">{{ $stop->distance_km ? number_format($stop->distance_km, 1) . ' km' : '—' }}</td>
          <td style="text-align: right; font-weight: bold;">{{ $stop->fare ? '₹' . number_format($stop->fare, 0) : '—' }}</td>
          <td style="text-align: center; font-weight: bold; color: #4338ca;">{{ $stuCount }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="8" style="text-align: center; color: #64748b; padding: 10px;">No stops mapped to this route corridor.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  {{-- Signatures --}}
  <table class="signatures-table">
    <tr>
      <td>
        <div class="sig-line">Driver Signature</div>
        <div style="font-size: 6.5pt; color: #64748b; margin-top: 2px;">{{ $vehicle?->driver_name ?? 'Designated Driver' }}</div>
      </td>
      <td>
        <div class="sig-line">Attendant Signature</div>
        <div style="font-size: 6.5pt; color: #64748b; margin-top: 2px;">{{ $vehicle?->attendants?->first()?->name ?? 'Designated Attendant' }}</div>
      </td>
      <td>
        <div class="sig-line">Transport In-Charge</div>
        <div style="font-size: 6.5pt; color: #64748b; margin-top: 2px;">Operations Desk</div>
      </td>
      <td>
        <div class="sig-line">Principal / Seal</div>
        <div style="font-size: 6.5pt; color: #64748b; margin-top: 2px;">Institutional Seal</div>
      </td>
    </tr>
  </table>

  @include('pdf.partials.footer', ['showPageNumbers' => true])

</body>
</html>

