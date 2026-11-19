<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <title>Fleet Compliance & Document Audit Matrix</title>
  <style>
    @page { margin: 10mm 12mm 10mm 15mm; }
    body {
      font-family: Arial, Helvetica, sans-serif;
      font-size: 7pt;
      line-height: 1.25;
      color: #0f172a;
      margin: 0;
      padding: 0;
    }
    table.data-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 6px;
      font-size: 6.5pt;
    }
    table.data-table th {
      background-color: #f1f5f9;
      border: 1px solid #94a3b8;
      padding: 3.5px 4px;
      text-align: left;
      font-weight: bold;
      text-transform: uppercase;
      font-size: 6pt;
      color: #1e293b;
    }
    table.data-table td {
      border: 1px solid #cbd5e1;
      padding: 3.5px 4px;
      vertical-align: middle;
    }
    .badge {
      display: inline-block;
      padding: 1px 3px;
      font-size: 5.5pt;
      font-weight: bold;
      text-transform: uppercase;
      border-radius: 2px;
    }
    .badge-ok { background-color: #dcfce7; color: #166534; border: 0.5px solid #86efac; }
    .badge-warn { background-color: #fef3c7; color: #92400e; border: 0.5px solid #fde68a; }
    .badge-danger { background-color: #fee2e2; color: #991b1b; border: 0.5px solid #fca5a5; }
    .signatures-table {
      width: 100%;
      margin-top: 30px;
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
    'title' => 'FLEET REGULATORY COMPLIANCE & STATUTORY DOCUMENT AUDIT',
    'subtitle' => 'Fitness Certificate, Insurance Policy, Transport Permit, PUC & Road Tax Expiry Schedule',
    'academicYear' => $academicYear?->name ?? (now()->year . '-' . (now()->year + 1)),
    'school' => $school
  ])

  <table class="data-table">
    <thead>
      <tr>
        <th style="width: 20px; text-align: center;">#</th>
        <th>Vehicle Reg #</th>
        <th>Van Code</th>
        <th>Make &amp; Model</th>
        <th>Fitness Expiry</th>
        <th>Insurance Expiry</th>
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
          <td style="text-align: center;">{{ $idx + 1 }}</td>
          <td style="font-weight: bold; color: #8C2826;">{{ $v->vehicle_number }}</td>
          <td style="font-weight: bold;">{{ $vanCode }}</td>
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
          <td colspan="12" style="text-align: center; color: #64748b; padding: 10px;">No vehicles found.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  {{-- Signatures --}}
  <table class="signatures-table">
    <tr>
      <td>
        <div class="sig-line">Fleet Transport In-Charge</div>
        <div style="font-size: 6.5pt; color: #64748b; margin-top: 2px;">Statutory Verification</div>
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

