<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <title>Master Bus Stop Directory</title>
  <style>
    @page { margin: 10mm 12mm 10mm 15mm; }
    body {
      font-family: Arial, Helvetica, sans-serif;
      font-size: 7.5pt;
      line-height: 1.3;
      color: #0f172a;
      margin: 0;
      padding: 0;
    }
    table.data-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 6px;
      font-size: 7pt;
    }
    table.data-table th {
      background-color: #f1f5f9;
      border: 1px solid #94a3b8;
      padding: 3.5px 5px;
      text-align: left;
      font-weight: bold;
      text-transform: uppercase;
      font-size: 6.5pt;
      color: #1e293b;
    }
    table.data-table td {
      border: 1px solid #cbd5e1;
      padding: 3.5px 5px;
      vertical-align: middle;
    }
    .signatures-table {
      width: 100%;
      margin-top: 25px;
      border-collapse: collapse;
    }
    .signatures-table td {
      width: 33.33%;
      text-align: center;
      vertical-align: top;
      border: none;
      font-size: 7.5pt;
    }
    .sig-line {
      border-top: 1px solid #475569;
      width: 75%;
      margin: 0 auto;
      padding-top: 3px;
      font-weight: bold;
    }
  </style>
</head>
<body>

  {{-- Centralized Official Header --}}
  @include('pdf.partials.header', [
    'title' => 'MASTER BUS STOP DIRECTORY & SCHEDULE',
    'subtitle' => 'Sequential Boarding Points, Morning Pickup, Evening Drop & Transit Fare Schedule',
    'academicYear' => $academicYear?->name ?? (now()->year . '-' . (now()->year + 1)),
    'school' => $school
  ])

  <table class="data-table">
    <thead>
      <tr>
        <th style="width: 20px; text-align: center;">#</th>
        <th>Stop Name &amp; Landmark</th>
        <th>Corridor / Van</th>
        <th style="width: 65px; text-align: center;">Morning Pickup</th>
        <th style="width: 65px; text-align: center;">Evening Drop</th>
        <th style="width: 45px; text-align: right;">Distance</th>
        <th style="width: 50px; text-align: right;">Fare</th>
        <th style="width: 40px; text-align: center;">Students</th>
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
          <td style="text-align: center;">{{ $idx + 1 }}</td>
          <td>
            <strong>{{ $s->stop_name ?? $s->name }}</strong>
            @if($s->landmark)
              <div style="font-size: 6pt; color: #64748b;">Near {{ $s->landmark }}</div>
            @endif
          </td>
          <td>
            <strong style="color: #4338ca;">{{ $corridor }}</strong>
            @if($vanLabel)
              <div style="font-size: 6pt; color: #8C2826; font-weight: bold;">{{ $vanLabel }}</div>
            @endif
          </td>
          <td style="text-align: center; font-weight: bold; color: #166534;">
            {{ $s->pickup_time ? \Carbon\Carbon::parse($s->pickup_time)->format('h:i A') : ($s->morning_time ?? '—') }}
          </td>
          <td style="text-align: center; font-weight: bold; color: #991b1b;">
            {{ $s->drop_time ? \Carbon\Carbon::parse($s->drop_time)->format('h:i A') : ($s->evening_time ?? '—') }}
          </td>
          <td style="text-align: right;">{{ $s->distance_km ? number_format($s->distance_km, 1) . ' km' : '—' }}</td>
          <td style="text-align: right; font-weight: bold;">{{ $s->fare ? '₹' . number_format($s->fare, 0) : '—' }}</td>
          <td style="text-align: center; font-weight: bold; color: #4338ca;">{{ $s->students_count ?? 0 }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="8" style="text-align: center; color: #64748b; padding: 10px;">No bus stops found.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  {{-- Signatures --}}
  <table class="signatures-table">
    <tr>
      <td>
        <div class="sig-line">Transport Coordinator</div>
        <div style="font-size: 6.5pt; color: #64748b; margin-top: 2px;">Operations Desk</div>
      </td>
      <td>
        <div class="sig-line">Administrative Officer</div>
        <div style="font-size: 6.5pt; color: #64748b; margin-top: 2px;">School Administration</div>
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

