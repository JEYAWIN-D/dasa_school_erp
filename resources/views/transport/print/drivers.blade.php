<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Driver Master Register — {{ $school?->school_name ?? 'Erode Public School' }}</title>
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
      width: 150px; 
      text-align: center; 
      padding-top: 4px; 
      color: #334155;
    }
  </style>
</head>
<body>

  {{-- Print Controls --}}
  <x-print.controls title="Fleet Drivers Register" defaultSize="a4" defaultOrientation="portrait" :backUrl="route('transport.drivers')" />

  {{-- Background Watermark --}}
  <x-print.watermark />

  {{-- Centralized Official Header --}}
  <x-print.header 
    title="FLEET DRIVER REGISTER & CREDENTIALS DIRECTORY" 
    subtitle="Official Institutional Transport Personnel & Driving License Verification" 
    :academicYear="$academicYear?->name ?? (now()->year . '-' . (now()->year + 1))" 
    :school="$school" 
  />

  <table class="print-table">
    <thead>
      <tr>
        <th style="width: 30px; text-align: center;">#</th>
        <th>Driver Name</th>
        <th>Mobile Contact</th>
        <th>Driving License #</th>
        <th>License Expiry</th>
        <th>Assigned Vehicle</th>
        <th>Assigned Route</th>
        <th style="text-align: center;">Status</th>
      </tr>
    </thead>
    <tbody>
      @forelse($drivers as $idx => $d)
        @php
          $v = $d->assigned_vehicle ?? $d->vehicle;
        @endphp
        <tr>
          <td style="text-align: center; font-weight: 800; color: #64748b;">{{ $idx + 1 }}</td>
          <td style="font-weight: 700; color: #0f172a;">{{ $d->driver_name ?? $d->name }}</td>
          <td style="font-family: monospace; font-weight: 700; color: #4338ca;">{{ $d->driver_phone ?? $d->driver_mobile ?? $d->phone ?? '—' }}</td>
          <td style="font-family: monospace;">{{ $d->driver_license ?? $d->driver_license_number ?? $d->license_number ?? '—' }}</td>
          <td>
            @php $exp = $d->driver_license_expiry ?? $d->license_expiry; @endphp
            {{ $exp ? \Carbon\Carbon::parse($exp)->format('d/m/Y') : '—' }}
            @if($exp)
              <span class="badge {{ \Carbon\Carbon::parse($exp)->isPast() ? 'badge-danger' : 'badge-ok' }}">
                {{ \Carbon\Carbon::parse($exp)->isPast() ? 'EXPIRED' : 'VALID' }}
              </span>
            @endif
          </td>
          <td style="font-family: monospace; font-weight: 700; color: #8C2826;">{{ $v?->vehicle_number ?? '—' }}</td>
          <td>{{ $v?->route?->route_name ?? '—' }}</td>
          <td style="text-align: center;">
            <span class="badge badge-ok">ACTIVE</span>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="8" style="text-align: center; color: #64748b; padding: 12px;">No driver records found in fleet directory.</td>
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

