<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <title>Fleet Master Register</title>
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
    'title' => 'FLEET MASTER REGISTER & VEHICLE COMPLIANCE DIRECTORY',
    'subtitle' => 'Official Institutional Transport Inventory & Assigned Crew Manifest',
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
        <th style="text-align: center;">Capacity</th>
        <th>Assigned Route</th>
        <th>Driver &amp; Contact</th>
        <th>Attendant &amp; Contact</th>
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
          <td style="text-align: center;">{{ $idx + 1 }}</td>
          <td style="font-weight: bold; color: #8C2826;">{{ $v->vehicle_number }}</td>
          <td style="font-weight: bold;">{{ $vanCode }}</td>
          <td>{{ $v->make }} {{ $v->model }}</td>
          <td style="text-align: center; font-weight: bold;">{{ $v->effective_capacity }}</td>
          <td style="font-weight: bold; color: #4338ca;">{{ $v->route?->route_name ?? '—' }}</td>
          <td>
            <strong>{{ $v->driver_name ?? '—' }}</strong><br>
            <span style="color: #4338ca;">{{ $v->driver_phone ?? $v->driver_mobile ?? '—' }}</span>
          </td>
          <td>
            <strong>{{ $attendant?->name ?? $v->attendant_name ?? '—' }}</strong><br>
            <span style="color: #475569;">{{ $attendant?->phone ?? $v->attendant_mobile ?? '—' }}</span>
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
          <td colspan="11" style="text-align: center; color: #64748b; padding: 10px;">No vehicles found.</td>
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

