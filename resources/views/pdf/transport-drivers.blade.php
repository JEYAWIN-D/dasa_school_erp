<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <title>Driver Master Register</title>
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
      padding: 4px 5px;
      text-align: left;
      font-weight: bold;
      text-transform: uppercase;
      font-size: 6.5pt;
      color: #1e293b;
    }
    table.data-table td {
      border: 1px solid #cbd5e1;
      padding: 4px 5px;
      vertical-align: middle;
    }
    .badge {
      display: inline-block;
      padding: 1px 4px;
      font-size: 6pt;
      font-weight: bold;
      text-transform: uppercase;
      border-radius: 2px;
    }
    .badge-ok { background-color: #dcfce7; color: #166534; border: 0.5px solid #86efac; }
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
      font-size: 8pt;
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
    'title' => 'FLEET DRIVER REGISTER & CREDENTIALS DIRECTORY',
    'subtitle' => 'Official Institutional Transport Personnel & Driving License Verification',
    'academicYear' => $academicYear?->name ?? (now()->year . '-' . (now()->year + 1)),
    'school' => $school
  ])

  <table class="data-table">
    <thead>
      <tr>
        <th style="width: 20px; text-align: center;">#</th>
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
          <td style="text-align: center;">{{ $idx + 1 }}</td>
          <td style="font-weight: bold;">{{ $d->driver_name ?? $d->name }}</td>
          <td style="font-weight: bold; color: #4338ca;">{{ $d->driver_phone ?? $d->driver_mobile ?? $d->phone ?? '—' }}</td>
          <td>{{ $d->driver_license ?? $d->driver_license_number ?? $d->license_number ?? '—' }}</td>
          <td>
            @php $exp = $d->driver_license_expiry ?? $d->license_expiry; @endphp
            {{ $exp ? \Carbon\Carbon::parse($exp)->format('d/m/Y') : '—' }}
            @if($exp)
              <span class="badge {{ \Carbon\Carbon::parse($exp)->isPast() ? 'badge-danger' : 'badge-ok' }}">
                {{ \Carbon\Carbon::parse($exp)->isPast() ? 'EXPIRED' : 'VALID' }}
              </span>
            @endif
          </td>
          <td style="font-weight: bold; color: #8C2826;">{{ $v?->vehicle_number ?? '—' }}</td>
          <td>{{ $v?->route?->route_name ?? '—' }}</td>
          <td style="text-align: center;">
            <span class="badge badge-ok">ACTIVE</span>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="8" style="text-align: center; color: #64748b; padding: 10px;">No driver records found.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  {{-- Signatures --}}
  <table class="signatures-table">
    <tr>
      <td>
        <div class="sig-line">Transport Supervisor</div>
        <div style="font-size: 6.5pt; color: #64748b; margin-top: 2px;">Fleet Operations</div>
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

