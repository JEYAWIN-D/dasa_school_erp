<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <title>Daily Transport Duty Sheet</title>
  <style>
    @page { margin: 8mm 10mm 8mm 12mm; }
    body {
      font-family: Arial, Helvetica, sans-serif;
      font-size: 7.5pt;
      line-height: 1.25;
      color: #0f172a;
      margin: 0;
      padding: 0;
    }
    .page-break { page-break-after: always; }
    .sec-title {
      font-size: 8pt;
      font-weight: bold;
      text-transform: uppercase;
      color: #8C2826;
      border-bottom: 1.5px solid #8C2826;
      padding-bottom: 2px;
      margin: 6px 0 3px 0;
    }
    table.data-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 4px;
      font-size: 7pt;
    }
    table.data-table th {
      background-color: #f1f5f9;
      border: 1px solid #94a3b8;
      padding: 3px 4px;
      text-align: left;
      font-weight: bold;
      text-transform: uppercase;
      font-size: 6.5pt;
      color: #1e293b;
    }
    table.data-table td {
      border: 1px solid #cbd5e1;
      padding: 3px 4px;
      vertical-align: middle;
    }
    .inner-box {
      border: 1px solid #cbd5e1;
      background-color: #f8fafc;
      padding: 4px 6px;
    }
    .signatures-table {
      width: 100%;
      margin-top: 20px;
      border-collapse: collapse;
    }
    .signatures-table td {
      width: 25%;
      text-align: center;
      vertical-align: top;
      border: none;
      font-size: 7.5pt;
    }
    .sig-line {
      border-top: 1px solid #475569;
      width: 80%;
      margin: 0 auto;
      padding-top: 2px;
      font-weight: bold;
    }
  </style>
</head>
<body>

  @foreach($vehicles as $vehicleIndex => $v)
    @php
      $attendant = $v->attendants->first();
      $stops = $v->route?->stops ?? collect();
    @endphp

    <div class="{{ !$loop->last ? 'page-break' : '' }}">
      {{-- Header --}}
      @include('pdf.partials.header', [
        'title' => 'DAILY TRANSPORT OPERATIONAL DUTY SHEET & RUN LOG',
        'subtitle' => 'Duty Date: ' . \Carbon\Carbon::parse($date)->format('l, d F Y') . ' | Shift: ' . strtoupper($trip),
        'academicYear' => $academicYear?->name ?? (now()->year . '-' . (now()->year + 1)),
        'school' => $school
      ])

      {{-- Metadata Table --}}
      <table style="width: 100%; border-collapse: collapse; margin-bottom: 4px;">
        <tr>
          <td style="width: 25%; padding: 2px; border: none;">
            <div class="inner-box">
              <span style="color: #64748b;">Van Reg:</span> <strong style="color: #8C2826;">{{ $v->vehicle_number }}</strong>
              <div style="font-size: 6.5pt; color: #475569;">{{ $v->make }} ({{ $v->effective_capacity }} Seats)</div>
            </div>
          </td>
          <td style="width: 25%; padding: 2px; border: none;">
            <div class="inner-box">
              <span style="color: #64748b;">Route:</span> <strong style="color: #4338ca;">{{ $v->route?->route_name ?? '—' }}</strong>
              <div style="font-size: 6.5pt; color: #475569;">Dist: {{ $v->route?->distance_km ? $v->route->distance_km.' km' : '—' }}</div>
            </div>
          </td>
          <td style="width: 25%; padding: 2px; border: none;">
            <div class="inner-box">
              <span style="color: #64748b;">Driver:</span> <strong>{{ $v->driver_name ?? '—' }}</strong>
              <div style="font-size: 6.5pt; color: #475569;">Tel: {{ $v->driver_phone ?? $v->driver_mobile ?? '—' }}</div>
            </div>
          </td>
          <td style="width: 25%; padding: 2px; border: none;">
            <div class="inner-box">
              <span style="color: #64748b;">Attendant:</span> <strong>{{ $attendant?->name ?? $v->attendant_name ?? '—' }}</strong>
              <div style="font-size: 6.5pt; color: #475569;">Tel: {{ $attendant?->phone ?? $v->attendant_mobile ?? '—' }}</div>
            </div>
          </td>
        </tr>
      </table>

      {{-- Stops Table --}}
      <div class="sec-title">Route Stops &amp; Schedule Execution Log</div>
      <table class="data-table">
        <thead>
          <tr>
            <th style="width: 25px; text-align: center;">Seq</th>
            <th>Bus Stop Name</th>
            <th>Landmark</th>
            <th style="width: 65px; text-align: center;">Sched Pickup</th>
            <th style="width: 65px; text-align: center;">Actual In</th>
            <th style="width: 65px; text-align: center;">Sched Drop</th>
            <th style="width: 65px; text-align: center;">Actual Out</th>
            <th style="width: 50px; text-align: center;">Boarded</th>
            <th style="width: 70px;">Initial</th>
          </tr>
        </thead>
        <tbody>
          @forelse($stops as $idx => $s)
            <tr>
              <td style="text-align: center; font-weight: bold;">{{ $s->stop_order ?? ($idx + 1) }}</td>
              <td style="font-weight: bold;">{{ $s->stop_name ?? $s->name }}</td>
              <td>{{ $s->landmark ?? '—' }}</td>
              <td style="text-align: center; font-weight: bold; color: #166534;">
                {{ $s->pickup_time ? \Carbon\Carbon::parse($s->pickup_time)->format('h:i A') : ($s->morning_time ?? '—') }}
              </td>
              <td style="text-align: center; color: #94a3b8;">___ : ___</td>
              <td style="text-align: center; font-weight: bold; color: #991b1b;">
                {{ $s->drop_time ? \Carbon\Carbon::parse($s->drop_time)->format('h:i A') : ($s->evening_time ?? '—') }}
              </td>
              <td style="text-align: center; color: #94a3b8;">___ : ___</td>
              <td style="text-align: center; color: #94a3b8;">[ &nbsp; ]</td>
              <td></td>
            </tr>
          @empty
            <tr>
              <td colspan="9" style="text-align: center; color: #64748b; padding: 8px;">No stops mapped to this corridor.</td>
            </tr>
          @endforelse
        </tbody>
      </table>

      {{-- Signatures --}}
      <table class="signatures-table">
        <tr>
          <td>
            <div class="sig-line">Driver Signature</div>
            <div style="font-size: 6pt; color: #64748b; margin-top: 1px;">{{ $v->driver_name ?? 'Designated Driver' }}</div>
          </td>
          <td>
            <div class="sig-line">Attendant Signature</div>
            <div style="font-size: 6pt; color: #64748b; margin-top: 1px;">{{ $attendant?->name ?? 'Designated Attendant' }}</div>
          </td>
          <td>
            <div class="sig-line">Transport Supervisor</div>
            <div style="font-size: 6pt; color: #64748b; margin-top: 1px;">Operations Sign-off</div>
          </td>
          <td>
            <div class="sig-line">Principal / Seal</div>
            <div style="font-size: 6pt; color: #64748b; margin-top: 1px;">Institutional Seal</div>
          </td>
        </tr>
      </table>
    </div>
  @endforeach

  @include('pdf.partials.footer', ['showPageNumbers' => true])

</body>
</html>

