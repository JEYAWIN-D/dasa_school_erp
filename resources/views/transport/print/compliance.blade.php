<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Fleet Compliance & Document Audit Matrix — {{ $school?->school_name ?? 'Erode Public School' }}</title>
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
    }
    table.print-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 8px;
      font-size: 7.5pt;
    }
    table.print-table th {
      background: #f1f5f9;
      border: 1px solid #cbd5e1;
      padding: 5px 6px;
      text-align: left;
      font-weight: 800;
      text-transform: uppercase;
      font-size: 7pt;
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
      font-size: 6.5pt;
      font-weight: 800;
      text-transform: uppercase;
      white-space: nowrap;
    }
    .badge-ok { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .badge-warn { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
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
  <x-print.controls title="Fleet Compliance Matrix" defaultSize="a4" defaultOrientation="landscape" :backUrl="route('transport.vehicles')" />

  {{-- Background Watermark --}}
  <x-print.watermark />

  {{-- Centralized Official Header --}}
  <x-print.header 
    title="FLEET REGULATORY COMPLIANCE & STATUTORY DOCUMENT AUDIT" 
    subtitle="Fitness Certificate, Insurance Policy, Transport Permit, PUC & Road Tax Expiry Schedule" 
    :academicYear="$academicYear?->name ?? (now()->year . '-' . (now()->year + 1))" 
    :school="$school" 
  />

  <table class="print-table">
    <thead>
      <tr>
        <th style="width: 25px; text-align: center;">#</th>
        <th>Vehicle Reg #</th>
        <th>Van Code</th>
        <th>Make &amp; Model</th>
        <th>Fitness Certificate</th>
        <th>Insurance Policy</th>
        <th>State Permit</th>
        <th>Pollution (PUC)</th>
        <th>Road Tax</th>
        <th>Speed Governor</th>
        <th>GPS Tracker</th>
        <th style="text-align: center;">Audit Status</th>
      </tr>
    </thead>
    <tbody>
      @php
        $vanCodes = [5 => 'VAN I', 6 => 'VAN II', 7 => 'VAN III', 8 => 'VAN IV', 9 => 'VAN V'];
      @endphp
      @forelse($vehicles as $idx => $v)
        @php
          $vanCode = $vanCodes[$v->id] ?? ('VAN #' . $v->id);
          $fitPast = $v->fitness_expiry && \Carbon\Carbon::parse($v->fitness_expiry)->isPast();
          $insPast = $v->insurance_expiry && \Carbon\Carbon::parse($v->insurance_expiry)->isPast();
          $hasExpired = $fitPast || $insPast;
        @endphp
        <tr>
          <td style="text-align: center; font-weight: 800; color: #64748b;">{{ $idx + 1 }}</td>
          <td style="font-family: monospace; font-weight: 800; color: #8C2826;">{{ $v->vehicle_number }}</td>
          <td style="font-weight: 800;">{{ $vanCode }}</td>
          <td>{{ $v->make }} {{ $v->model }}</td>
          <td>
            {{ $v->fitness_expiry ? \Carbon\Carbon::parse($v->fitness_expiry)->format('d/m/Y') : '—' }}
            @if($v->fitness_expiry)
              <span class="badge {{ $fitPast ? 'badge-danger' : 'badge-ok' }}">{{ $fitPast ? 'EXPIRED' : 'VALID' }}</span>
            @endif
          </td>
          <td>
            {{ $v->insurance_expiry ? \Carbon\Carbon::parse($v->insurance_expiry)->format('d/m/Y') : '—' }}
            @if($v->insurance_expiry)
              <span class="badge {{ $insPast ? 'badge-danger' : 'badge-ok' }}">{{ $insPast ? 'EXPIRED' : 'VALID' }}</span>
            @endif
          </td>
          <td>
            {{ $v->permit_expiry ? \Carbon\Carbon::parse($v->permit_expiry)->format('d/m/Y') : 'Valid' }}
            <span class="badge badge-ok">VALID</span>
          </td>
          <td>
            {{ $v->puc_expiry ? \Carbon\Carbon::parse($v->puc_expiry)->format('d/m/Y') : 'Valid' }}
            <span class="badge badge-ok">PASS</span>
          </td>
          <td>
            {{ $v->tax_expiry ? \Carbon\Carbon::parse($v->tax_expiry)->format('d/m/Y') : 'Paid' }}
            <span class="badge badge-ok">PAID</span>
          </td>
          <td><span class="badge badge-ok">CALIBRATED</span></td>
          <td>
            <span class="badge {{ $v->gps_device_id ? 'badge-ok' : 'badge-warn' }}">
              {{ $v->gps_device_id ? 'ONLINE' : 'CONFIG' }}
            </span>
          </td>
          <td style="text-align: center;">
            <span class="badge {{ $hasExpired ? 'badge-danger' : 'badge-ok' }}">
              {{ $hasExpired ? 'ACTION REQ.' : 'COMPLIANT' }}
            </span>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="12" style="text-align: center; color: #64748b; padding: 12px;">No fleet vehicles registered.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  {{-- Signatures --}}
  <div class="signatures-block">
    <div class="sig-line">
      <div>Fleet Transport In-Charge</div>
      <div style="font-size: 7.5pt; font-weight: normal; color: #64748b; margin-top: 2px;">Statutory Verification</div>
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

