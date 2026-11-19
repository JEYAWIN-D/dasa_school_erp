<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Van Dossier — {{ $vehicle->vehicle_number }} — {{ $school?->school_name ?? 'Erode Public School' }}</title>
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

    /* Print Visibility */
    @media print {
      body { 
        padding: 10mm 12mm 10mm 18mm !important; 
        margin: 0 !important; 
        max-width: 100% !important;
        background: #ffffff !important;
      }
      .no-print { 
        display: none !important; 
      }
    }

    /* Section Headers */
    .sec-title { 
      font-size: 9pt; 
      font-weight: 800; 
      text-transform: uppercase; 
      color: #8C2826; 
      border-bottom: 1.5px solid #8C2826; 
      padding-bottom: 2px; 
      margin: 10px 0 5px 0; 
      letter-spacing: 0.5px; 
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    /* Box Grids */
    .identity-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 8px;
      margin-bottom: 8px;
    }
    .info-card {
      border: 1px solid #cbd5e1;
      border-radius: 6px;
      padding: 6px 10px;
      background: #f8fafc;
    }
    .info-row {
      display: flex;
      font-size: 8pt;
      margin-bottom: 2px;
    }
    .info-row:last-child {
      margin-bottom: 0;
    }
    .info-label {
      width: 120px;
      color: #64748b;
      font-weight: 600;
      flex-shrink: 0;
    }
    .info-val {
      font-weight: 700;
      color: #0f172a;
      flex: 1;
    }

    /* Data Tables */
    table.print-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 4px;
      font-size: 8pt;
    }
    table.print-table th {
      background: #f1f5f9;
      border: 1px solid #cbd5e1;
      padding: 4px 6px;
      text-align: left;
      font-weight: 800;
      text-transform: uppercase;
      font-size: 7.5pt;
      color: #334155;
      white-space: nowrap;
    }
    table.print-table td {
      border: 1px solid #cbd5e1;
      padding: 4px 6px;
      vertical-align: middle;
    }

    /* Status Badges */
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
    .badge-warn { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .badge-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

    /* Signatures Block */
    .signatures-block { 
      margin-top: 24px; 
      display: flex; 
      justify-content: space-between; 
      font-weight: 800; 
      font-size: 8.5pt; 
      page-break-inside: avoid;
    }
    .sig-line { 
      border-top: 1px solid #475569; 
      width: 140px; 
      text-align: center; 
      padding-top: 4px; 
      color: #334155;
    }
  </style>
</head>
<body>

  {{-- Interactive Sticky Print Controls (Screen Only) --}}
  <x-print.controls title="Van Dossier — {{ $vehicle->vehicle_number }}" defaultSize="a4" defaultOrientation="portrait" :backUrl="route('transport.vehicle.show', $vehicle->id)" />

  {{-- Background Watermark --}}
  <x-print.watermark />

  {{-- Centralized Official Institutional Letterhead --}}
  <x-print.header 
    title="OFFICIAL TRANSPORT VAN DOSSIER" 
    subtitle="Vehicle Specifications, Route Schedule, Driver & Attendant Credentials" 
    :academicYear="$academicYear?->name ?? (now()->year . '-' . (now()->year + 1))" 
    :school="$school" 
  />

  @php
    $vanCodes = [5 => 'VAN I', 6 => 'VAN II', 7 => 'VAN III', 8 => 'VAN IV', 9 => 'VAN V'];
    $vanCode = $vanCodes[$vehicle->id] ?? ('VAN #' . $vehicle->id);
    $attendant = $vehicle->attendants->first();
  @endphp

  {{-- 1. Vehicle Specifications & Operational Metadata --}}
  <div class="sec-title">
    <span>1. Vehicle Profile &amp; Regulatory Compliance</span>
    <span style="font-size: 8pt; font-weight: 800; color: #0f172a;">Fleet Code: {{ $vanCode }}</span>
  </div>
  <div class="identity-grid">
    <div class="info-card">
      <div class="info-row">
        <span class="info-label">Vehicle Reg. No:</span>
        <span class="info-val font-mono" style="color: #8C2826; font-size: 9pt;">{{ $vehicle->vehicle_number }}</span>
      </div>
      <div class="info-row">
        <span class="info-label">Vehicle Model:</span>
        <span class="info-val">{{ $vehicle->make }} {{ $vehicle->model }} ({{ $vehicle->year ?? '—' }})</span>
      </div>
      <div class="info-row">
        <span class="info-label">Seating Capacity:</span>
        <span class="info-val"><strong>{{ $vehicle->effective_capacity }} Seats</strong> (Allocated: {{ $allotments->count() }})</span>
      </div>
      <div class="info-row">
        <span class="info-label">Fuel &amp; GPS:</span>
        <span class="info-val">{{ ucfirst($vehicle->fuel_type ?? 'Diesel') }} &bull; GPS: {{ $vehicle->gps_device_id ? 'Installed' : 'Configured' }}</span>
      </div>
    </div>

    <div class="info-card">
      <div class="info-row">
        <span class="info-label">Assigned Route:</span>
        <span class="info-val" style="color: #4338ca;">{{ $vehicle->route?->route_name ?? '—' }} ({{ $vehicle->route?->route_code ?? 'RT-'.$vehicle->id }})</span>
      </div>
      <div class="info-row">
        <span class="info-label">Fitness Expiry:</span>
        <span class="info-val">
          {{ $vehicle->fitness_expiry ? \Carbon\Carbon::parse($vehicle->fitness_expiry)->format('d M Y') : '—' }}
          @if($vehicle->fitness_expiry)
            <span class="badge {{ \Carbon\Carbon::parse($vehicle->fitness_expiry)->isPast() ? 'badge-danger' : 'badge-ok' }}">
              {{ \Carbon\Carbon::parse($vehicle->fitness_expiry)->isPast() ? 'EXPIRED' : 'VALID' }}
            </span>
          @endif
        </span>
      </div>
      <div class="info-row">
        <span class="info-label">Insurance Expiry:</span>
        <span class="info-val">
          {{ $vehicle->insurance_expiry ? \Carbon\Carbon::parse($vehicle->insurance_expiry)->format('d M Y') : '—' }}
          @if($vehicle->insurance_expiry)
            <span class="badge {{ \Carbon\Carbon::parse($vehicle->insurance_expiry)->isPast() ? 'badge-danger' : 'badge-ok' }}">
              {{ \Carbon\Carbon::parse($vehicle->insurance_expiry)->isPast() ? 'EXPIRED' : 'VALID' }}
            </span>
          @endif
        </span>
      </div>
      <div class="info-row">
        <span class="info-label">PUC / Permit:</span>
        <span class="info-val">
          PUC: {{ $vehicle->puc_expiry ? \Carbon\Carbon::parse($vehicle->puc_expiry)->format('d/m/Y') : 'Valid' }} &bull;
          Permit: {{ $vehicle->permit_expiry ? \Carbon\Carbon::parse($vehicle->permit_expiry)->format('d/m/Y') : 'Valid' }}
        </span>
      </div>
    </div>
  </div>

  {{-- 2. Driver & Nanny / Attendant Profiles --}}
  <div class="sec-title">2. Designated Crew Details</div>
  <div class="identity-grid">
    {{-- Driver Card --}}
    <div class="info-card">
      <div style="font-weight: 800; color: #1e293b; font-size: 8pt; margin-bottom: 4px; text-transform: uppercase; border-bottom: 1px dashed #cbd5e1; padding-bottom: 2px;">
        🚍 Designated Driver
      </div>
      <div class="info-row">
        <span class="info-label">Driver Name:</span>
        <span class="info-val">{{ $vehicle->driver_name ?? '—' }}</span>
      </div>
      <div class="info-row">
        <span class="info-label">Mobile Contact:</span>
        <span class="info-val font-mono" style="color: #4338ca;">{{ $vehicle->driver_phone ?? $vehicle->driver_mobile ?? '—' }}</span>
      </div>
      <div class="info-row">
        <span class="info-label">Driving License:</span>
        <span class="info-val font-mono">{{ $vehicle->driver_license ?? $vehicle->driver_license_number ?? '—' }}</span>
      </div>
      <div class="info-row">
        <span class="info-label">License Expiry:</span>
        <span class="info-val">{{ $vehicle->driver_license_expiry ? \Carbon\Carbon::parse($vehicle->driver_license_expiry)->format('d M Y') : '—' }}</span>
      </div>
      <div class="info-row">
        <span class="info-label">Address:</span>
        <span class="info-val" style="font-size: 7.5pt;">{{ $vehicle->driver_address ?? 'Registered with Transport Dept' }}</span>
      </div>
    </div>

    {{-- Attendant / Nanny Card --}}
    <div class="info-card">
      <div style="font-weight: 800; color: #1e293b; font-size: 8pt; margin-bottom: 4px; text-transform: uppercase; border-bottom: 1px dashed #cbd5e1; padding-bottom: 2px;">
        👩‍👧 Nanny / Bus Attendant
      </div>
      <div class="info-row">
        <span class="info-label">Attendant Name:</span>
        <span class="info-val">{{ $attendant?->name ?? $vehicle->attendant_name ?? '—' }}</span>
      </div>
      <div class="info-row">
        <span class="info-label">Mobile Contact:</span>
        <span class="info-val font-mono" style="color: #4338ca;">{{ $attendant?->phone ?? $attendant?->mobile ?? $vehicle->attendant_mobile ?? '—' }}</span>
      </div>
      <div class="info-row">
        <span class="info-label">Aadhaar / ID:</span>
        <span class="info-val font-mono">{{ $attendant?->aadhaar_no ?? $vehicle->attendant_aadhaar ?? 'Verified' }}</span>
      </div>
      <div class="info-row">
        <span class="info-label">Emergency Tel:</span>
        <span class="info-val font-mono">{{ $attendant?->emergency_contact ?? 'School Desk' }}</span>
      </div>
      <div class="info-row">
        <span class="info-label">Address:</span>
        <span class="info-val" style="font-size: 7.5pt;">{{ $attendant?->address ?? $vehicle->attendant_address ?? 'Registered with Transport Dept' }}</span>
      </div>
    </div>
  </div>

  {{-- 3. Sequential Van Stop Details & Timings --}}
  <div class="sec-title">
    <span>3. Sequential Van Stops &amp; Transit Schedule</span>
    <span style="font-size: 8pt; font-weight: 800; color: #0f172a;">Total Stops: {{ $stops->count() }}</span>
  </div>
  <table class="print-table">
    <thead>
      <tr>
        <th style="width: 35px; text-align: center;">Seq</th>
        <th>Bus Stop Name</th>
        <th>Landmark / Intersection</th>
        <th style="width: 75px; text-align: center;">Morning Pickup</th>
        <th style="width: 75px; text-align: center;">Evening Drop</th>
        <th style="width: 60px; text-align: right;">Distance</th>
        <th style="width: 65px; text-align: right;">Monthly Fare</th>
        <th style="width: 55px; text-align: center;">Students</th>
      </tr>
    </thead>
    <tbody>
      @forelse($stops as $idx => $stop)
        @php
          $stuCount = $stopStudentCounts[$stop->id] ?? 0;
        @endphp
        <tr>
          <td style="text-align: center; font-weight: 800; color: #475569;">{{ $stop->stop_order ?? ($idx + 1) }}</td>
          <td style="font-weight: 700; color: #0f172a;">{{ $stop->stop_name ?? $stop->name }}</td>
          <td style="color: #475569;">{{ $stop->landmark ?? '—' }}</td>
          <td style="text-align: center; font-weight: 700; color: #166534; font-family: monospace;">
            {{ $stop->pickup_time ? \Carbon\Carbon::parse($stop->pickup_time)->format('h:i A') : ($stop->morning_time ?? '—') }}
          </td>
          <td style="text-align: center; font-weight: 700; color: #991b1b; font-family: monospace;">
            {{ $stop->drop_time ? \Carbon\Carbon::parse($stop->drop_time)->format('h:i A') : ($stop->evening_time ?? '—') }}
          </td>
          <td style="text-align: right; color: #475569;">{{ $stop->distance_km ? number_format($stop->distance_km, 1) . ' km' : '—' }}</td>
          <td style="text-align: right; font-weight: 700;">{{ $stop->fare ? '₹' . number_format($stop->fare, 0) : '—' }}</td>
          <td style="text-align: center; font-weight: 800; color: #4338ca;">{{ $stuCount }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="8" style="text-align: center; color: #64748b; padding: 12px;">No stops mapped to this vehicle or route corridor yet.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  {{-- 4. Allocated Students Passenger Manifest --}}
  <div class="sec-title" style="page-break-before: auto; margin-top: 14px;">
    <span>4. Allocated Student Passenger Manifest</span>
    <span style="font-size: 8pt; font-weight: 800; color: #0f172a;">Total Passengers: {{ $allotments->count() }}</span>
  </div>
  <table class="print-table">
    <thead>
      <tr>
        <th style="width: 30px; text-align: center;">#</th>
        <th style="width: 70px;">Adm No.</th>
        <th>Student Name</th>
        <th style="width: 80px;">Class &amp; Sec</th>
        <th>Designated Boarding Stop</th>
        <th style="width: 85px;">Parent Contact</th>
        <th style="width: 85px;">Emergency Tel</th>
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
          <td style="text-align: center; color: #64748b;">{{ $idx + 1 }}</td>
          <td style="font-family: monospace; font-weight: 700; color: #4338ca;">{{ $student?->admission_number ?? $student?->admission_no ?? '—' }}</td>
          <td style="font-weight: 700; color: #0f172a;">{{ $student?->full_name ?? ($student?->first_name . ' ' . $student?->last_name) }}</td>
          <td>{{ $cls }}{{ $sec }}</td>
          <td>{{ $allotment->stop?->stop_name ?? $allotment->stop?->name ?? '—' }}</td>
          <td style="font-family: monospace;">{{ $student?->father_mobile ?? $student?->mobile ?? '—' }}</td>
          <td style="font-family: monospace;">{{ $student?->emergency_contact ?? $student?->mother_mobile ?? '—' }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="7" style="text-align: center; color: #64748b; padding: 12px;">No student passenger allocations active for this van.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  {{-- 5. Verification & Official Signatures --}}
  <div class="signatures-block">
    <div class="sig-line">
      <div>Driver Signature</div>
      <div style="font-size: 7pt; font-weight: normal; color: #64748b; margin-top: 2px;">{{ $vehicle->driver_name ?? 'Designated Driver' }}</div>
    </div>
    <div class="sig-line">
      <div>Attendant Signature</div>
      <div style="font-size: 7pt; font-weight: normal; color: #64748b; margin-top: 2px;">{{ $attendant?->name ?? 'Designated Attendant' }}</div>
    </div>
    <div class="sig-line">
      <div>Transport In-Charge</div>
      <div style="font-size: 7pt; font-weight: normal; color: #64748b; margin-top: 2px;">Operations Desk</div>
    </div>
    <div class="sig-line">
      <div>Principal / Headmistress</div>
      <div style="font-size: 7pt; font-weight: normal; color: #64748b; margin-top: 2px;">Institutional Seal</div>
    </div>
  </div>

</body>
</html>

