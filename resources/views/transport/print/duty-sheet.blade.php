<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Daily Transport Duty Sheet — {{ \Carbon\Carbon::parse($date)->format('d M Y') }} — {{ $school?->school_name ?? 'Erode Public School' }}</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <style>
    @page { 
      size: A4 landscape; 
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
      max-width: 297mm;
      padding: 10mm 12mm 10mm 16mm; 
      background: #ffffff; 
    }
    @media print {
      body { 
        padding: 10mm 12mm 10mm 16mm !important; 
        margin: 0 !important; 
        max-width: 100% !important;
        background: #ffffff !important;
      }
      .no-print { display: none !important; }
      .page-break { page-break-after: always; }
    }
    .sec-title { 
      font-size: 8.5pt; 
      font-weight: 800; 
      text-transform: uppercase; 
      color: #8C2826; 
      border-bottom: 1.5px solid #8C2826; 
      padding-bottom: 2px; 
      margin: 8px 0 4px 0; 
      letter-spacing: 0.5px; 
    }
    .info-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 6px;
      margin-bottom: 6px;
    }
    .info-card {
      border: 1px solid #cbd5e1;
      border-radius: 4px;
      padding: 4px 8px;
      background: #f8fafc;
      font-size: 7.5pt;
    }
    table.print-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 4px;
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
      padding: 3.5px 5px;
      vertical-align: middle;
    }
    .checklist-box {
      border: 1px solid #cbd5e1;
      border-radius: 4px;
      padding: 6px 10px;
      background: #ffffff;
      font-size: 7.5pt;
      margin-top: 6px;
    }
    .signatures-block { 
      margin-top: 20px; 
      display: flex; 
      justify-content: space-between; 
      font-weight: 800; 
      font-size: 8pt; 
      page-break-inside: avoid;
    }
    .sig-line { 
      border-top: 1px solid #475569; 
      width: 170px; 
      text-align: center; 
      padding-top: 4px; 
      color: #334155;
    }
  </style>
</head>
<body>

  {{-- Print Controls --}}
  <x-print.controls title="Daily Transport Duty Sheet" defaultSize="a4" defaultOrientation="landscape" :backUrl="route('transport.duty-sheet')" />

  @foreach($vehicles as $vehicleIndex => $v)
    @php
      $attendant = $v->attendants->first();
      $stops = $v->route?->stops ?? collect();
    @endphp

    <div class="{{ !$loop->last ? 'page-break' : '' }}" style="position: relative;">
      {{-- Background Watermark --}}
      <x-print.watermark />

      {{-- Header --}}
      <x-print.header 
        title="DAILY TRANSPORT OPERATIONAL DUTY SHEET & RUN LOG" 
        subtitle="Duty Date: {{ \Carbon\Carbon::parse($date)->format('l, d F Y') }} | Shift: {{ strtoupper($trip) }}" 
        :academicYear="$academicYear?->name ?? (now()->year . '-' . (now()->year + 1))" 
        :school="$school" 
      />

      {{-- Operational Metadata Grid --}}
      <div class="info-grid">
        <div class="info-card">
          <span style="color: #64748b; font-weight: 600;">Van Number:</span>
          <strong style="color: #8C2826; font-size: 8.5pt;">{{ $v->vehicle_number }}</strong>
          <div style="color: #475569; font-size: 7pt; margin-top: 2px;">{{ $v->make }} {{ $v->model }} ({{ $v->effective_capacity }} Seats)</div>
        </div>
        <div class="info-card">
          <span style="color: #64748b; font-weight: 600;">Assigned Route:</span>
          <strong style="color: #4338ca;">{{ $v->route?->route_name ?? 'Unassigned Corridor' }}</strong>
          <div style="color: #475569; font-size: 7pt; margin-top: 2px;">Dist: {{ $v->route?->distance_km ? $v->route->distance_km.' km' : '—' }}</div>
        </div>
        <div class="info-card">
          <span style="color: #64748b; font-weight: 600;">Designated Driver:</span>
          <strong>{{ $v->driver_name ?? '—' }}</strong>
          <div style="color: #475569; font-size: 7pt; margin-top: 2px;">Tel: {{ $v->driver_phone ?? $v->driver_mobile ?? '—' }}</div>
        </div>
        <div class="info-card">
          <span style="color: #64748b; font-weight: 600;">Designated Attendant:</span>
          <strong>{{ $attendant?->name ?? $v->attendant_name ?? '—' }}</strong>
          <div style="color: #475569; font-size: 7pt; margin-top: 2px;">Tel: {{ $attendant?->phone ?? $v->attendant_mobile ?? '—' }}</div>
        </div>
      </div>

      {{-- Route Stops & Arrival Execution Log --}}
      <div class="sec-title">Route Stops &amp; Schedule Execution Log</div>
      <table class="print-table">
        <thead>
          <tr>
            <th style="width: 30px; text-align: center;">Seq</th>
            <th>Bus Stop Name</th>
            <th>Landmark</th>
            <th style="width: 80px; text-align: center;">Sched Pickup</th>
            <th style="width: 80px; text-align: center;">Actual Arrival</th>
            <th style="width: 80px; text-align: center;">Sched Drop</th>
            <th style="width: 80px; text-align: center;">Actual Drop</th>
            <th style="width: 65px; text-align: center;">Students In</th>
            <th style="width: 65px; text-align: center;">Students Out</th>
            <th style="width: 85px;">Crew Initial</th>
          </tr>
        </thead>
        <tbody>
          @forelse($stops as $idx => $s)
            <tr>
              <td style="text-align: center; font-weight: 800; color: #64748b;">{{ $s->stop_order ?? ($idx + 1) }}</td>
              <td style="font-weight: 700; color: #0f172a;">{{ $s->stop_name ?? $s->name }}</td>
              <td style="color: #64748b;">{{ $s->landmark ?? '—' }}</td>
              <td style="text-align: center; font-weight: 700; color: #166534; font-family: monospace;">
                {{ $s->pickup_time ? \Carbon\Carbon::parse($s->pickup_time)->format('h:i A') : ($s->morning_time ?? '—') }}
              </td>
              <td style="text-align: center; color: #94a3b8;">___ : ___</td>
              <td style="text-align: center; font-weight: 700; color: #991b1b; font-family: monospace;">
                {{ $s->drop_time ? \Carbon\Carbon::parse($s->drop_time)->format('h:i A') : ($s->evening_time ?? '—') }}
              </td>
              <td style="text-align: center; color: #94a3b8;">___ : ___</td>
              <td style="text-align: center; color: #94a3b8;">[ &nbsp; &nbsp; ]</td>
              <td style="text-align: center; color: #94a3b8;">[ &nbsp; &nbsp; ]</td>
              <td></td>
            </tr>
          @empty
            <tr>
              <td colspan="10" style="text-align: center; color: #64748b; padding: 8px;">No stops mapped to this corridor.</td>
            </tr>
          @endforelse
        </tbody>
      </table>

      {{-- Daily Vehicle Safety Checklist & Metrics --}}
      <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 8px; margin-top: 6px;">
        <div class="checklist-box">
          <div style="font-weight: 800; color: #1e293b; margin-bottom: 4px; text-transform: uppercase;">
            Vehicle Inspection &amp; Safety Verification (Tick to confirm)
          </div>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4px; font-size: 7.5pt;">
            <div>[ &nbsp; ] Tyre Pressure &amp; Spare Wheel OK</div>
            <div>[ &nbsp; ] First Aid Kit &amp; Medicine Valid</div>
            <div>[ &nbsp; ] Foot &amp; Hand Brakes Functional</div>
            <div>[ &nbsp; ] Fire Extinguisher Charged &amp; Inspected</div>
            <div>[ &nbsp; ] Headlights, Indicators &amp; Horn OK</div>
            <div>[ &nbsp; ] Emergency Door Operable &amp; Unobstructed</div>
            <div>[ &nbsp; ] GPS &amp; Speed Governor Active</div>
            <div>[ &nbsp; ] Interior Clean &amp; Sanitized</div>
          </div>
        </div>

        <div class="checklist-box">
          <div style="font-weight: 800; color: #1e293b; margin-bottom: 4px; text-transform: uppercase;">
            Trip Fuel &amp; Odometer Log
          </div>
          <div style="font-size: 7.5pt; line-height: 1.6;">
            <div>Start Odometer: ___________________ km</div>
            <div>End Odometer: &nbsp; ___________________ km</div>
            <div>Fuel Added: &nbsp; &nbsp; ___________________ Ltr &nbsp; (₹ ______________)</div>
          </div>
        </div>
      </div>

      {{-- Signatures --}}
      <div class="signatures-block">
        <div class="sig-line">
          <div>Driver Signature</div>
          <div style="font-size: 7pt; font-weight: normal; color: #64748b; margin-top: 2px;">{{ $v->driver_name ?? 'Designated Driver' }}</div>
        </div>
        <div class="sig-line">
          <div>Attendant Signature</div>
          <div style="font-size: 7pt; font-weight: normal; color: #64748b; margin-top: 2px;">{{ $attendant?->name ?? 'Designated Attendant' }}</div>
        </div>
        <div class="sig-line">
          <div>Transport Supervisor</div>
          <div style="font-size: 7pt; font-weight: normal; color: #64748b; margin-top: 2px;">Operations Sign-off</div>
        </div>
        <div class="sig-line">
          <div>Principal / Headmistress</div>
          <div style="font-size: 7pt; font-weight: normal; color: #64748b; margin-top: 2px;">Institutional Seal</div>
        </div>
      </div>
    </div>
  @endforeach

</body>
</html>

