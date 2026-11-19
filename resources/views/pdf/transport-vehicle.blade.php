<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <title>Van Dossier — {{ $vehicle->vehicle_number }}</title>
  <style>
    @page {
      margin: 10mm 12mm 10mm 15mm;
    }
    body {
      font-family: Arial, Helvetica, sans-serif;
      font-size: 8pt;
      line-height: 1.3;
      color: #0f172a;
      margin: 0;
      padding: 0;
    }
    .sec-title {
      font-size: 8.5pt;
      font-weight: bold;
      text-transform: uppercase;
      color: #8C2826;
      border-bottom: 1.5px solid #8C2826;
      padding-bottom: 2px;
      margin: 10px 0 4px 0;
    }
    table.data-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 3px;
      font-size: 7.5pt;
    }
    table.data-table th {
      background-color: #f1f5f9;
      border: 1px solid #94a3b8;
      padding: 3.5px 5px;
      text-align: left;
      font-weight: bold;
      text-transform: uppercase;
      font-size: 7pt;
      color: #1e293b;
    }
    table.data-table td {
      border: 1px solid #cbd5e1;
      padding: 3.5px 5px;
      vertical-align: middle;
    }
    .badge {
      display: inline-block;
      padding: 1px 4px;
      font-size: 6.5pt;
      font-weight: bold;
      text-transform: uppercase;
      border-radius: 2px;
    }
    .badge-ok { background-color: #dcfce7; color: #166534; border: 0.5px solid #86efac; }
    .badge-danger { background-color: #fee2e2; color: #991b1b; border: 0.5px solid #fca5a5; }
    .box-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 6px;
    }
    .box-cell {
      width: 50%;
      vertical-align: top;
      padding: 4px;
    }
    .inner-box {
      border: 1px solid #cbd5e1;
      background-color: #f8fafc;
      padding: 5px 8px;
    }
    .signatures-table {
      width: 100%;
      margin-top: 25px;
      border-collapse: collapse;
    }
    .signatures-table td {
      width: 25%;
      text-align: center;
      vertical-align: top;
      border: none;
      font-size: 8pt;
    }
    .sig-line {
      border-top: 1px solid #475569;
      width: 80%;
      margin: 0 auto;
      padding-top: 3px;
      font-weight: bold;
    }
  </style>
</head>
<body>

  {{-- Centralized Official Header --}}
  @include('pdf.partials.header', [
    'title' => 'OFFICIAL TRANSPORT VAN DOSSIER',
    'subtitle' => 'Vehicle Specifications, Route Schedule, Driver & Attendant Credentials',
    'academicYear' => $academicYear?->name ?? (now()->year . '-' . (now()->year + 1)),
    'school' => $school
  ])

  @php
    $vanCodes = [5 => 'VAN I', 6 => 'VAN II', 7 => 'VAN III', 8 => 'VAN IV', 9 => 'VAN V'];
    $vanCode = $vanCodes[$vehicle->id] ?? ('VAN #' . $vehicle->id);
    $attendant = $vehicle->attendants->first();
  @endphp

  {{-- 1. Vehicle Specifications & Regulatory Compliance --}}
  <div class="sec-title">
    1. Vehicle Profile &amp; Regulatory Compliance &mdash; Fleet Code: {{ $vanCode }}
  </div>
  <table class="box-table">
    <tr>
      <td class="box-cell" style="padding-left: 0;">
        <div class="inner-box">
          <table style="width: 100%; font-size: 7.5pt; border-collapse: collapse;">
            <tr>
              <td style="width: 110px; color: #64748b; font-weight: bold; border: none; padding: 1.5px 0;">Vehicle Reg. No:</td>
              <td style="font-weight: bold; color: #8C2826; font-size: 8.5pt; border: none; padding: 1.5px 0;">{{ $vehicle->vehicle_number }}</td>
            </tr>
            <tr>
              <td style="color: #64748b; font-weight: bold; border: none; padding: 1.5px 0;">Make &amp; Model:</td>
              <td style="font-weight: bold; border: none; padding: 1.5px 0;">{{ $vehicle->make }} {{ $vehicle->model }} ({{ $vehicle->year ?? '—' }})</td>
            </tr>
            <tr>
              <td style="color: #64748b; font-weight: bold; border: none; padding: 1.5px 0;">Seating Capacity:</td>
              <td style="font-weight: bold; border: none; padding: 1.5px 0;">{{ $vehicle->effective_capacity }} Seats (Allocated: {{ $allotments->count() }})</td>
            </tr>
            <tr>
              <td style="color: #64748b; font-weight: bold; border: none; padding: 1.5px 0;">Fuel &amp; GPS:</td>
              <td style="border: none; padding: 1.5px 0;">{{ ucfirst($vehicle->fuel_type ?? 'Diesel') }} &bull; GPS: {{ $vehicle->gps_device_id ? 'Installed' : 'Configured' }}</td>
            </tr>
          </table>
        </div>
      </td>
      <td class="box-cell" style="padding-right: 0;">
        <div class="inner-box">
          <table style="width: 100%; font-size: 7.5pt; border-collapse: collapse;">
            <tr>
              <td style="width: 110px; color: #64748b; font-weight: bold; border: none; padding: 1.5px 0;">Assigned Route:</td>
              <td style="font-weight: bold; color: #4338ca; border: none; padding: 1.5px 0;">{{ $vehicle->route?->route_name ?? '—' }}</td>
            </tr>
            <tr>
              <td style="color: #64748b; font-weight: bold; border: none; padding: 1.5px 0;">Fitness Expiry:</td>
              <td style="font-weight: bold; border: none; padding: 1.5px 0;">
                {{ $vehicle->fitness_expiry ? \Carbon\Carbon::parse($vehicle->fitness_expiry)->format('d M Y') : '—' }}
                @if($vehicle->fitness_expiry)
                  <span class="badge {{ \Carbon\Carbon::parse($vehicle->fitness_expiry)->isPast() ? 'badge-danger' : 'badge-ok' }}">
                    {{ \Carbon\Carbon::parse($vehicle->fitness_expiry)->isPast() ? 'EXPIRED' : 'VALID' }}
                  </span>
                @endif
              </td>
            </tr>
            <tr>
              <td style="color: #64748b; font-weight: bold; border: none; padding: 1.5px 0;">Insurance Expiry:</td>
              <td style="font-weight: bold; border: none; padding: 1.5px 0;">
                {{ $vehicle->insurance_expiry ? \Carbon\Carbon::parse($vehicle->insurance_expiry)->format('d M Y') : '—' }}
                @if($vehicle->insurance_expiry)
                  <span class="badge {{ \Carbon\Carbon::parse($vehicle->insurance_expiry)->isPast() ? 'badge-danger' : 'badge-ok' }}">
                    {{ \Carbon\Carbon::parse($vehicle->insurance_expiry)->isPast() ? 'EXPIRED' : 'VALID' }}
                  </span>
                @endif
              </td>
            </tr>
            <tr>
              <td style="color: #64748b; font-weight: bold; border: none; padding: 1.5px 0;">PUC / Permit:</td>
              <td style="border: none; padding: 1.5px 0;">
                PUC: {{ $vehicle->puc_expiry ? \Carbon\Carbon::parse($vehicle->puc_expiry)->format('d/m/Y') : 'Valid' }} &bull;
                Permit: {{ $vehicle->permit_expiry ? \Carbon\Carbon::parse($vehicle->permit_expiry)->format('d/m/Y') : 'Valid' }}
              </td>
            </tr>
          </table>
        </div>
      </td>
    </tr>
  </table>

  {{-- 2. Designated Crew Details --}}
  <div class="sec-title">2. Designated Crew Details</div>
  <table class="box-table">
    <tr>
      <td class="box-cell" style="padding-left: 0;">
        <div class="inner-box">
          <div style="font-weight: bold; font-size: 7.5pt; color: #1e293b; text-transform: uppercase; border-bottom: 1px dashed #cbd5e1; padding-bottom: 2px; margin-bottom: 3px;">
            Designated Driver
          </div>
          <table style="width: 100%; font-size: 7.5pt; border-collapse: collapse;">
            <tr>
              <td style="width: 95px; color: #64748b; font-weight: bold; border: none; padding: 1.5px 0;">Driver Name:</td>
              <td style="font-weight: bold; border: none; padding: 1.5px 0;">{{ $vehicle->driver_name ?? '—' }}</td>
            </tr>
            <tr>
              <td style="color: #64748b; font-weight: bold; border: none; padding: 1.5px 0;">Mobile Contact:</td>
              <td style="font-weight: bold; color: #4338ca; border: none; padding: 1.5px 0;">{{ $vehicle->driver_phone ?? $vehicle->driver_mobile ?? '—' }}</td>
            </tr>
            <tr>
              <td style="color: #64748b; font-weight: bold; border: none; padding: 1.5px 0;">License No:</td>
              <td style="border: none; padding: 1.5px 0;">{{ $vehicle->driver_license ?? $vehicle->driver_license_number ?? '—' }}</td>
            </tr>
            <tr>
              <td style="color: #64748b; font-weight: bold; border: none; padding: 1.5px 0;">License Expiry:</td>
              <td style="border: none; padding: 1.5px 0;">{{ $vehicle->driver_license_expiry ? \Carbon\Carbon::parse($vehicle->driver_license_expiry)->format('d M Y') : '—' }}</td>
            </tr>
          </table>
        </div>
      </td>
      <td class="box-cell" style="padding-right: 0;">
        <div class="inner-box">
          <div style="font-weight: bold; font-size: 7.5pt; color: #1e293b; text-transform: uppercase; border-bottom: 1px dashed #cbd5e1; padding-bottom: 2px; margin-bottom: 3px;">
            Nanny / Bus Attendant
          </div>
          <table style="width: 100%; font-size: 7.5pt; border-collapse: collapse;">
            <tr>
              <td style="width: 95px; color: #64748b; font-weight: bold; border: none; padding: 1.5px 0;">Attendant Name:</td>
              <td style="font-weight: bold; border: none; padding: 1.5px 0;">{{ $attendant?->name ?? $vehicle->attendant_name ?? '—' }}</td>
            </tr>
            <tr>
              <td style="color: #64748b; font-weight: bold; border: none; padding: 1.5px 0;">Mobile Contact:</td>
              <td style="font-weight: bold; color: #4338ca; border: none; padding: 1.5px 0;">{{ $attendant?->phone ?? $attendant?->mobile ?? $vehicle->attendant_mobile ?? '—' }}</td>
            </tr>
            <tr>
              <td style="color: #64748b; font-weight: bold; border: none; padding: 1.5px 0;">Aadhaar / ID:</td>
              <td style="border: none; padding: 1.5px 0;">{{ $attendant?->aadhaar_no ?? $vehicle->attendant_aadhaar ?? 'Verified' }}</td>
            </tr>
            <tr>
              <td style="color: #64748b; font-weight: bold; border: none; padding: 1.5px 0;">Emergency Tel:</td>
              <td style="border: none; padding: 1.5px 0;">{{ $attendant?->emergency_contact ?? 'School Desk' }}</td>
            </tr>
          </table>
        </div>
      </td>
    </tr>
  </table>

  {{-- 3. Sequential Van Stops & Transit Schedule --}}
  <div class="sec-title">
    3. Sequential Van Stops &amp; Transit Schedule &mdash; Total Stops: {{ $stops->count() }}
  </div>
  <table class="data-table">
    <thead>
      <tr>
        <th style="width: 25px; text-align: center;">Seq</th>
        <th>Bus Stop Name</th>
        <th>Landmark / Location</th>
        <th style="width: 70px; text-align: center;">Morning Pickup</th>
        <th style="width: 70px; text-align: center;">Evening Drop</th>
        <th style="width: 50px; text-align: right;">Distance</th>
        <th style="width: 55px; text-align: right;">Fare</th>
        <th style="width: 45px; text-align: center;">Students</th>
      </tr>
    </thead>
    <tbody>
      @forelse($stops as $idx => $stop)
        @php
          $stuCount = $stopStudentCounts[$stop->id] ?? 0;
        @endphp
        <tr>
          <td style="text-align: center; font-weight: bold;">{{ $stop->stop_order ?? ($idx + 1) }}</td>
          <td style="font-weight: bold;">{{ $stop->stop_name ?? $stop->name }}</td>
          <td>{{ $stop->landmark ?? '—' }}</td>
          <td style="text-align: center; font-weight: bold; color: #166534;">
            {{ $stop->pickup_time ? \Carbon\Carbon::parse($stop->pickup_time)->format('h:i A') : ($stop->morning_time ?? '—') }}
          </td>
          <td style="text-align: center; font-weight: bold; color: #991b1b;">
            {{ $stop->drop_time ? \Carbon\Carbon::parse($stop->drop_time)->format('h:i A') : ($stop->evening_time ?? '—') }}
          </td>
          <td style="text-align: right;">{{ $stop->distance_km ? number_format($stop->distance_km, 1) . ' km' : '—' }}</td>
          <td style="text-align: right; font-weight: bold;">{{ $stop->fare ? '₹' . number_format($stop->fare, 0) : '—' }}</td>
          <td style="text-align: center; font-weight: bold; color: #4338ca;">{{ $stuCount }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="8" style="text-align: center; color: #64748b; padding: 10px;">No stops mapped to this vehicle yet.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  {{-- 4. Allocated Student Passenger Manifest --}}
  <div class="sec-title" style="page-break-before: auto; margin-top: 10px;">
    4. Allocated Student Passenger Manifest &mdash; Total Passengers: {{ $allotments->count() }}
  </div>
  <table class="data-table">
    <thead>
      <tr>
        <th style="width: 25px; text-align: center;">#</th>
        <th style="width: 60px;">Adm No.</th>
        <th>Student Name</th>
        <th style="width: 75px;">Class &amp; Sec</th>
        <th>Boarding Stop</th>
        <th style="width: 80px;">Parent Contact</th>
        <th style="width: 80px;">Emergency Tel</th>
      </tr>
    </thead>
    <tbody>
      @forelse($allotments as $idx => $allotment)
        @php
          $student = $allotment->student;
          $enrollment = $allotment->enrollment;
          $cls = $enrollment?->class?->name ?? '—';
          $sec = $enrollment?->section?->name ? ' - ' . $enrollment->section->name : '';
        @endphp
        <tr>
          <td style="text-align: center;">{{ $idx + 1 }}</td>
          <td style="font-weight: bold; color: #4338ca;">{{ $student?->admission_number ?? $student?->admission_no ?? '—' }}</td>
          <td style="font-weight: bold;">{{ $student?->full_name ?? ($student?->first_name . ' ' . $student?->last_name) }}</td>
          <td>{{ $cls }}{{ $sec }}</td>
          <td>{{ $allotment->stop?->stop_name ?? $allotment->stop?->name ?? '—' }}</td>
          <td>{{ $student?->father_mobile ?? $student?->mobile ?? '—' }}</td>
          <td>{{ $student?->emergency_contact ?? $student?->mother_mobile ?? '—' }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="7" style="text-align: center; color: #64748b; padding: 10px;">No student passenger allocations active for this van.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  {{-- Signatures --}}
  <table class="signatures-table">
    <tr>
      <td>
        <div class="sig-line">Driver Signature</div>
        <div style="font-size: 6.5pt; color: #64748b; margin-top: 2px;">{{ $vehicle->driver_name ?? 'Designated Driver' }}</div>
      </td>
      <td>
        <div class="sig-line">Attendant Signature</div>
        <div style="font-size: 6.5pt; color: #64748b; margin-top: 2px;">{{ $attendant?->name ?? 'Designated Attendant' }}</div>
      </td>
      <td>
        <div class="sig-line">Transport In-Charge</div>
        <div style="font-size: 6.5pt; color: #64748b; margin-top: 2px;">Operations Desk</div>
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

