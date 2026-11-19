<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Transport Emergency & Safety Directory — {{ $school?->school_name ?? 'Erode Public School' }}</title>
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
    .grid-2 {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 8px;
      margin-bottom: 8px;
    }
    .emergency-card {
      border: 1px solid #fecaca;
      border-radius: 6px;
      padding: 6px 10px;
      background: #fef2f2;
    }
    .emergency-card h4 {
      margin: 0 0 4px 0;
      color: #991b1b;
      font-size: 8.5pt;
      font-weight: 800;
      text-transform: uppercase;
      border-bottom: 1px dashed #fca5a5;
      padding-bottom: 2px;
    }
    table.print-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 6px;
      font-size: 8pt;
    }
    table.print-table th {
      background: #f1f5f9;
      border: 1px solid #cbd5e1;
      padding: 5px 6px;
      text-align: left;
      font-weight: 800;
      text-transform: uppercase;
      font-size: 7.5pt;
      color: #334155;
      white-space: nowrap;
    }
    table.print-table td {
      border: 1px solid #cbd5e1;
      padding: 5px 6px;
      vertical-align: middle;
    }
    .signatures-block { 
      margin-top: 28px; 
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
  <x-print.controls title="Emergency Contacts Directory" defaultSize="a4" defaultOrientation="portrait" :backUrl="route('transport.index')" />

  {{-- Background Watermark --}}
  <x-print.watermark />

  {{-- Centralized Official Header --}}
  <x-print.header 
    title="TRANSPORT EMERGENCY RESPONSE & SAFETY DIRECTORY" 
    subtitle="Immediate Operational Escalation, Fleet Crew Contacts, Trauma Centers & Police Desks" 
    :academicYear="$academicYear?->name ?? (now()->year . '-' . (now()->year + 1))" 
    :school="$school" 
  />

  {{-- Emergency Hotlines & Institutional Desks --}}
  <div class="sec-title">1. Essential Emergency Helplines</div>
  <div class="grid-2">
    <div class="emergency-card">
      <h4>🚨 Police &amp; Traffic Authorities</h4>
      <div style="font-size: 8pt; line-height: 1.5;">
        <div>Police Control Room: <strong style="font-family: monospace; color: #991b1b;">100 / 112</strong></div>
        <div>Traffic Police Helpline: <strong style="font-family: monospace; color: #991b1b;">103</strong></div>
        <div>Local Police Station (Erode Town): <strong style="font-family: monospace;">0424-2223344</strong></div>
        <div>Child Safety &amp; POCSO Helpline: <strong style="font-family: monospace; color: #991b1b;">1098</strong></div>
      </div>
    </div>

    <div class="emergency-card">
      <h4>🚑 Medical, Trauma &amp; Fire Rescue</h4>
      <div style="font-size: 8pt; line-height: 1.5;">
        <div>Ambulance Emergency: <strong style="font-family: monospace; color: #991b1b;">108</strong></div>
        <div>Fire &amp; Rescue Services: <strong style="font-family: monospace; color: #991b1b;">101</strong></div>
        <div>Erode Govt Headquarters Hospital: <strong style="font-family: monospace;">0424-2258351</strong></div>
        <div>Authorized Vehicle Breakdown: <strong style="font-family: monospace;">98424-11223</strong></div>
      </div>
    </div>
  </div>

  {{-- School Internal Crisis Response Team --}}
  <div class="sec-title">2. School Internal Transport Crisis Desk</div>
  <div class="info-card" style="border: 1px solid #cbd5e1; border-radius: 6px; padding: 6px 10px; background: #f8fafc; margin-bottom: 8px;">
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px; font-size: 8pt;">
      <div>
        <span style="color: #64748b; font-weight: 600;">Transport In-Charge:</span><br>
        <strong style="color: #0f172a;">Mr. K. Ramasamy</strong><br>
        <span style="font-family: monospace; color: #4338ca; font-weight: 700;">+91 94432-88710</span>
      </div>
      <div>
        <span style="color: #64748b; font-weight: 600;">Principal's Desk:</span><br>
        <strong style="color: #0f172a;">Principal Office</strong><br>
        <span style="font-family: monospace; color: #4338ca; font-weight: 700;">+91 98430-55421</span>
      </div>
      <div>
        <span style="color: #64748b; font-weight: 600;">School Front Office / Gate:</span><br>
        <strong style="color: #0f172a;">Main Reception</strong><br>
        <span style="font-family: monospace; color: #4338ca; font-weight: 700;">0424-2256789</span>
      </div>
    </div>
  </div>

  {{-- Vehicle Fleet Crew Emergency Roster --}}
  <div class="sec-title">3. Vehicle-Wise Crew Direct Contact Directory</div>
  <table class="print-table">
    <thead>
      <tr>
        <th style="width: 25px; text-align: center;">#</th>
        <th>Vehicle Reg #</th>
        <th>Van Code</th>
        <th>Assigned Route</th>
        <th>Driver Name &amp; Direct Mobile</th>
        <th>Attendant / Nanny &amp; Mobile</th>
        <th style="text-align: center;">Capacity</th>
      </tr>
    </thead>
    <tbody>
      @php
        $vanCodes = [5 => 'VAN I', 6 => 'VAN II', 7 => 'VAN III', 8 => 'VAN IV', 9 => 'VAN V'];
      @endphp
      @forelse($vehicles as $idx => $v)
        @php
          $vanCode = $vanCodes[$v->id] ?? ('VAN #' . $v->id);
          $attendant = $v->attendants->first();
        @endphp
        <tr>
          <td style="text-align: center; font-weight: 800; color: #64748b;">{{ $idx + 1 }}</td>
          <td style="font-family: monospace; font-weight: 800; color: #8C2826;">{{ $v->vehicle_number }}</td>
          <td style="font-weight: 800;">{{ $vanCode }}</td>
          <td style="font-weight: 700; color: #4338ca;">{{ $v->route?->route_name ?? '—' }}</td>
          <td>
            <strong>{{ $v->driver_name ?? '—' }}</strong><br>
            <span style="font-family: monospace; font-weight: 700; color: #166534;">{{ $v->driver_phone ?? $v->driver_mobile ?? '—' }}</span>
          </td>
          <td>
            <strong>{{ $attendant?->name ?? $v->attendant_name ?? '—' }}</strong><br>
            <span style="font-family: monospace; font-weight: 700; color: #4338ca;">{{ $attendant?->phone ?? $v->attendant_mobile ?? '—' }}</span>
          </td>
          <td style="text-align: center; font-weight: 700;">{{ $v->effective_capacity }} Seats</td>
        </tr>
      @empty
        <tr>
          <td colspan="7" style="text-align: center; color: #64748b; padding: 10px;">No vehicles found.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  {{-- Signatures --}}
  <div class="signatures-block">
    <div class="sig-line">
      <div>Transport In-Charge</div>
      <div style="font-size: 7.5pt; font-weight: normal; color: #64748b; margin-top: 2px;">Emergency Protocol Officer</div>
    </div>
    <div class="sig-line">
      <div>Administrative Officer</div>
      <div style="font-size: 7.5pt; font-weight: normal; color: #64748b; margin-top: 2px;">School Administration</div>
    </div>
    <div class="sig-line">
      <div>Principal / Headmistress</div>
      <div style="font-size: 7.5pt; font-weight: normal; color: #64748b; margin-top: 2px;">Institutional Seal</div>
    </div>
  </div>

</body>
</html>

