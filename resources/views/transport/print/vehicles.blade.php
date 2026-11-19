<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Fleet Master Register — {{ $school?->school_name ?? 'Erode Public School' }}</title>
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
      font-size: 8.5pt; 
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
    }
    table.print-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 8px;
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
    .badge {
      display: inline-block;
      padding: 1px 5px;
      border-radius: 3px;
      font-size: 7pt;
      font-weight: 800;
      text-transform: uppercase;
      white-space: nowrap;
    }
    .badge-ok { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .badge-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
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
      width: 180px; 
      text-align: center; 
      padding-top: 4px; 
      color: #334155;
    }
  </style>
</head>
<body>

  {{-- Print Controls --}}
  <x-print.controls title="Fleet Master Register" defaultSize="a4" defaultOrientation="landscape" :backUrl="route('transport.vehicles')" />

  {{-- Background Watermark --}}
  <x-print.watermark />

  {{-- Centralized Official Institutional Letterhead --}}
  <x-print.header 
    title="FLEET MASTER REGISTER & VEHICLE COMPLIANCE DIRECTORY" 
    subtitle="Official Institutional Transport Inventory & Assigned Crew Manifest" 
    :academicYear="$academicYear?->name ?? (now()->year . '-' . (now()->year + 1))" 
    :school="$school" 
  />

  <table class="print-table">
    <thead>
      <tr>
        <th style="width: 30px; text-align: center;">#</th>
        <th>Vehicle Reg #</th>
        <th>Van Code</th>
        <th>Make &amp; Model</th>
        <th style="text-align: center;">Capacity</th>
        <th>Assigned Route</th>
        <th>Designated Driver &amp; Contact</th>
        <th>Nanny / Attendant</th>
        <th>Fitness Expiry</th>
        <th>Insurance Expiry</th>
        <th style="text-align: center;">Status</th>
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
          <td style="font-weight: 800; color: #0f172a;">{{ $vanCode }}</td>
          <td>{{ $v->make }} {{ $v->model }}</td>
          <td style="text-align: center; font-weight: 700;">{{ $v->effective_capacity }}</td>
          <td style="font-weight: 700; color: #4338ca;">{{ $v->route?->route_name ?? '—' }}</td>
          <td>
            <strong>{{ $v->driver_name ?? '—' }}</strong><br>
            <span style="font-family: monospace; color: #4338ca;">{{ $v->driver_phone ?? $v->driver_mobile ?? '—' }}</span>
          </td>
          <td>
            <strong>{{ $attendant?->name ?? $v->attendant_name ?? '—' }}</strong><br>
            <span style="font-family: monospace; color: #475569;">{{ $attendant?->phone ?? $v->attendant_mobile ?? '—' }}</span>
          </td>
          <td>
            {{ $v->fitness_expiry ? \Carbon\Carbon::parse($v->fitness_expiry)->format('d/m/Y') : '—' }}
            @if($v->fitness_expiry)
              <span class="badge {{ \Carbon\Carbon::parse($v->fitness_expiry)->isPast() ? 'badge-danger' : 'badge-ok' }}">
                {{ \Carbon\Carbon::parse($v->fitness_expiry)->isPast() ? 'EXPIRED' : 'VALID' }}
              </span>
            @endif
          </td>
          <td>
            {{ $v->insurance_expiry ? \Carbon\Carbon::parse($v->insurance_expiry)->format('d/m/Y') : '—' }}
            @if($v->insurance_expiry)
              <span class="badge {{ \Carbon\Carbon::parse($v->insurance_expiry)->isPast() ? 'badge-danger' : 'badge-ok' }}">
                {{ \Carbon\Carbon::parse($v->insurance_expiry)->isPast() ? 'EXPIRED' : 'VALID' }}
              </span>
            @endif
          </td>
          <td style="text-align: center;">
            <span class="badge {{ $v->is_active ? 'badge-ok' : 'badge-danger' }}">
              {{ $v->is_active ? 'ACTIVE' : 'INACTIVE' }}
            </span>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="11" style="text-align: center; color: #64748b; padding: 12px;">No vehicles found in fleet registry.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  {{-- Signatures --}}
  <div class="signatures-block">
    <div class="sig-line">
      <div>Transport Supervisor</div>
      <div style="font-size: 7.5pt; font-weight: normal; color: #64748b; margin-top: 2px;">Fleet Operations</div>
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

