@php
  $schoolSetting = \App\Models\SchoolSetting::instance() ?? \App\Models\SchoolSetting::first();
  $schoolName = $schoolSetting?->school_name ?? config('app.name', 'ERODE PUBLIC SCHOOL');
  $affLine = $schoolSetting?->affiliation_line ?? ($schoolSetting?->board ? 'Affiliated to ' . $schoolSetting->board : 'Affiliated to CBSE, New Delhi');
  $fullAddress = $schoolSetting?->full_address ?? $schoolSetting?->address;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>{{ $student->full_name }} ({{ $student->admission_no }}) — Student Profile — {{ $schoolName }}</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <style>
    @page { 
      size: A4 portrait; 
      margin: 0 !important; 
    }
    * { box-sizing: border-box; }
    body { 
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; 
      color: #0f172a; 
      font-size: 9.5pt; 
      line-height: 1.4; 
      margin: 0 auto; 
      max-width: 210mm;
      padding: 10mm 12mm 10mm 18mm; 
      background: #ffffff; 
    }

    /* Hero Identity Box */
    .hero-box { 
      display: flex; 
      align-items: flex-start; 
      justify-content: space-between; 
      border: 1px solid #cbd5e1; 
      border-radius: 8px; 
      padding: 8px 10px; 
      margin-bottom: 8px; 
      background: #f8fafc; 
    }
    .hero-details { flex: 1; }
    .hero-photo { 
      width: 90px; 
      height: 110px; 
      border: 1px solid #94a3b8; 
      border-radius: 6px; 
      overflow: hidden; 
      background: #e2e8f0; 
      display: flex; 
      align-items: center; 
      justify-content: center; 
      font-weight: bold; 
      font-size: 11pt; 
      color: #64748b; 
      flex-shrink: 0;
      margin-left: 15px;
    }
    .hero-photo img { width: 100%; height: 100%; object-fit: cover; }

    /* Section Titles matching official dossier */
    .sec-title { 
      font-size: 9.5pt; 
      font-weight: bold; 
      text-transform: uppercase; 
      color: #8C2826; 
      border-bottom: 1.5px solid #8C2826; 
      padding-bottom: 3px; 
      margin: 12px 0 6px 0; 
      letter-spacing: 0.5px; 
    }

    .grid-2 { 
      display: grid; 
      grid-template-columns: repeat(2, 1fr); 
      gap: 5px 16px; 
      margin-bottom: 6px; 
    }
    .item { 
      display: flex; 
      font-size: 8.5pt; 
    }
    .item-label { 
      width: 140px; 
      color: #64748b; 
      font-weight: 600; 
      flex-shrink: 0; 
    }
    .item-value { 
      font-weight: bold; 
      color: #0f172a; 
      flex: 1; 
    }

    table.data-table { 
      width: 100%; 
      border-collapse: collapse; 
      margin-top: 6px; 
      font-size: 8.5pt; 
    }
    table.data-table th { 
      background: #f1f5f9; 
      border: 1px solid #cbd5e1; 
      padding: 5px 8px; 
      text-align: left; 
      font-weight: bold; 
      text-transform: uppercase; 
      font-size: 8pt; 
      color: #334155; 
    }
    table.data-table td { 
      border: 1px solid #cbd5e1; 
      padding: 5px 8px; 
    }

    /* Print Signatures Block */
    .signatures-block { 
      margin-top: 30px; 
      display: flex; 
      justify-content: space-between; 
      font-weight: bold; 
      font-size: 9pt; 
    }
    .sig-line { 
      border-top: 1px solid #475569; 
      width: 160px; 
      text-align: center; 
      padding-top: 5px; 
      color: #334155;
    }

    @media print {
      body { 
        max-width: 100% !important;
        padding: 10mm 12mm 10mm 18mm !important; 
        margin: 0 !important;
        box-sizing: border-box !important;
        background: #fff !important; 
      }
      .no-print { 
        display: none !important; 
      }
      * { 
        -webkit-print-color-adjust: exact !important; 
        print-color-adjust: exact !important; 
      }
    }
  </style>
</head>
<body>

  {{-- Interactive Sticky Print Controls (Screen Only) --}}
  <x-print.controls title="Student Profile Dossier" defaultSize="a4" defaultOrientation="portrait" :backUrl="route('students.show', $student->id)" />

  {{-- Subdued Background Watermark --}}
  <x-print.watermark />

  {{-- Centralized Official Institutional Letterhead --}}
  <x-print.header 
    title="OFFICIAL STUDENT PROFILE DOSSIER" 
    subtitle="Cumulative Student Record & Demographic Dossier" 
    :academicYear="$student->currentEnrollment?->academicYear?->name ?? $academicYear?->name ?? (now()->year . '-' . (now()->year + 1))" 
  />

  {{-- Hero Identity Card --}}
  <div class="hero-box">
    <div class="hero-details">
      <h2 style="margin: 0; font-size: 13pt; color: #0f172a; font-weight: 800;">{{ $student->full_name }}</h2>
      <div style="margin-top: 3px; font-size: 9pt; color: #8C2826; font-weight: bold;">
        Admission No: <span style="font-family: monospace;">{{ $student->admission_no }}</span>
        &bull; Roll No: <span style="font-family: monospace;">{{ $student->currentEnrollment?->roll_number ?: 'Not Assigned' }}</span>
      </div>
      <div style="margin-top: 3px; font-size: 8.5pt; color: #475569;">
        Standard: <strong>Class {{ $student->currentEnrollment?->class?->name ?? '—' }}</strong>
        &bull; Section: <strong>Section {{ $student->currentEnrollment?->section?->name ?? 'None' }}</strong>
        &bull; House: <strong>{{ $student->currentEnrollment?->houseGroup?->name ?? ($student->currentEnrollment?->house ?: 'Unallotted') }}</strong>
      </div>
      <div style="margin-top: 3px; font-size: 8.5pt; color: #475569;">
        Academic Year: <strong>{{ $student->currentEnrollment?->academicYear?->name ?? $academicYear?->name }}</strong>
        &bull; Status: <strong style="text-transform: uppercase; color: {{ $student->status === 'active' ? '#166534' : '#991b1b' }}">{{ $student->status }}</strong>
      </div>
    </div>
    <div class="hero-photo">
      @if($student->photo)
        <img src="{{ asset('storage/' . $student->photo) }}" alt="Student Photo">
      @else
        <span>PHOTO</span>
      @endif
    </div>
  </div>

  {{-- 1. Personal Information --}}
  <div class="sec-title">1. Personal Information</div>
  <div class="grid-2">
    <div class="item">
      <span class="item-label">Date of Birth:</span>
      <span class="item-value">{{ $student->dob ? $student->dob->format('d F Y') : '—' }} ({{ $student->dob ? $student->dob->age . ' yrs' : '—' }})</span>
    </div>
    <div class="item">
      <span class="item-label">Gender:</span>
      <span class="item-value">{{ ucfirst($student->gender) }}</span>
    </div>
    <div class="item">
      <span class="item-label">Blood Group:</span>
      <span class="item-value">{{ $student->blood_group ?: '—' }}</span>
    </div>
    <div class="item">
      <span class="item-label">Nationality:</span>
      <span class="item-value">{{ $student->nationality ?: 'Indian' }}</span>
    </div>
    <div class="item">
      <span class="item-label">Mother Tongue:</span>
      <span class="item-value">{{ $student->mother_tongue ?: '—' }}</span>
    </div>
    <div class="item">
      <span class="item-label">Religion / Caste:</span>
      <span class="item-value">{{ $student->religion ?: '—' }} / {{ $student->caste ?: '—' }}</span>
    </div>
    <div class="item">
      <span class="item-label">Social Category:</span>
      <span class="item-value">
        @if(is_object($student->category))
          {{ $student->category->name ?? 'General' }}
        @elseif(is_string($student->category) && !empty($student->category))
          {{ ucfirst($student->category) }}
        @else
          General
        @endif
      </span>
    </div>
    <div class="item">
      <span class="item-label">Aadhaar Number:</span>
      <span class="item-value" style="font-family: monospace;">{{ $student->aadhaar_no ?: ($student->national_id ?: '—') }}</span>
    </div>
    <div class="item">
      <span class="item-label">Student Contact:</span>
      <span class="item-value">{{ $student->mobile ?: ($student->phone ?: '—') }}</span>
    </div>
    <div class="item">
      <span class="item-label">PEN / EMIS No:</span>
      <span class="item-value" style="font-family: monospace;">{{ $student->emis_no ?: ($student->pen_number ?: '—') }}</span>
    </div>
  </div>
  <div class="item" style="margin-top: 3px;">
    <span class="item-label" style="width: 140px;">Residential Address:</span>
    <span class="item-value">{{ $student->residential_address ?: ($student->current_address ?: '—') }} @if($student->pincode)(PIN: {{ $student->pincode }})@endif</span>
  </div>

  {{-- 2. Parent & Guardian Information --}}
  <div class="sec-title">2. Parent &amp; Guardian Information</div>
  <div class="grid-2">
    <div class="item">
      <span class="item-label">Father's Name:</span>
      <span class="item-value">{{ $student->father_name ?: '—' }}</span>
    </div>
    <div class="item">
      <span class="item-label">Father's Contact:</span>
      <span class="item-value" style="font-family: monospace;">{{ $student->father_mobile ?: ($student->father_phone ?: '—') }}</span>
    </div>
    <div class="item">
      <span class="item-label">Father's Occupation:</span>
      <span class="item-value">{{ $student->father_occupation ?: '—' }}</span>
    </div>
    <div class="item">
      <span class="item-label">Father's Email:</span>
      <span class="item-value">{{ $student->father_email ?: '—' }}</span>
    </div>
    <div class="item">
      <span class="item-label">Mother's Name:</span>
      <span class="item-value">{{ $student->mother_name ?: '—' }}</span>
    </div>
    <div class="item">
      <span class="item-label">Mother's Contact:</span>
      <span class="item-value" style="font-family: monospace;">{{ $student->mother_mobile ?: ($student->mother_phone ?: '—') }}</span>
    </div>
    <div class="item">
      <span class="item-label">Mother's Occupation:</span>
      <span class="item-value">{{ $student->mother_occupation ?: '—' }}</span>
    </div>
    <div class="item">
      <span class="item-label">Emergency Contact:</span>
      <span class="item-value" style="font-family: monospace;">
        {{ $student->emergency_contact_name ? $student->emergency_contact_name . ' (' . $student->emergency_contact_mobile . ')' : ($student->emergency_contact ?: ($student->father_mobile ?: '—')) }}
      </span>
    </div>
  </div>

  {{-- 3. Academic & Admission Details --}}
  <div class="sec-title">3. Academic &amp; Admission Profile</div>
  <div class="grid-2">
    <div class="item">
      <span class="item-label">Admission Date:</span>
      <span class="item-value">{{ $student->admission_date ? $student->admission_date->format('d/m/Y') : '—' }}</span>
    </div>
    <div class="item">
      <span class="item-label">Student Type:</span>
      <span class="item-value">{{ ucwords(str_replace('_', ' ', $student->student_type ?: 'Day Scholar')) }}</span>
    </div>
    <div class="item">
      <span class="item-label">EMIS / PEN Number:</span>
      <span class="item-value" style="font-family: monospace;">{{ $student->emis_no ?: ($student->pen_number ?: '—') }}</span>
    </div>
    <div class="item">
      <span class="item-label">Previous School:</span>
      <span class="item-value">{{ $student->previous_school_name ?: '—' }} @if($student->previous_school_board)({{ $student->previous_school_board }})@endif</span>
    </div>
    <div class="item">
      <span class="item-label">Transport Route:</span>
      <span class="item-value">{{ $student->transportRoute?->route_name ?: 'Not Enrolled (Own Transport)' }}</span>
    </div>
    <div class="item">
      <span class="item-label">Designated Stop:</span>
      <span class="item-value">{{ $student->transportStop?->stop_name ?: '—' }}</span>
    </div>
  </div>

  {{-- 4. Submitted Documents Checklist --}}
  <div class="sec-title">4. Document Verification Checklist</div>
  <table class="data-table">
    <thead>
      <tr>
        <th style="width: 45px; text-align: center;">S.NO</th>
        <th>DOCUMENT CATEGORY</th>
        <th style="width: 140px; text-align: center;">VERIFICATION STATUS</th>
        <th style="width: 130px; text-align: center;">UPLOAD DATE</th>
      </tr>
    </thead>
    <tbody>
      @forelse($documents as $idx => $doc)
        <tr>
          <td style="text-align: center; color: #64748b;">{{ $idx + 1 }}</td>
          <td><strong>{{ ucwords(str_replace('_', ' ', $doc->document_type)) }}</strong></td>
          <td style="text-align: center;">
            <span style="font-weight: bold; color: {{ $doc->status === 'verified' ? '#166534' : '#b45309' }};">
              {{ strtoupper($doc->status ?: 'Pending') }}
            </span>
          </td>
          <td style="text-align: center; color: #475569;">{{ $doc->created_at ? $doc->created_at->format('d/m/Y') : '—' }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="4" style="text-align: center; color: #94a3b8; padding: 10px;">No document records uploaded in file vault.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  {{-- Signatures Section --}}
  <div class="signatures-block">
    <div class="sig-line">Parent / Guardian Signature</div>
    <div class="sig-line">Class Teacher Signature</div>
    <div class="sig-line">Principal / Authorized Seal</div>
  </div>

  {{-- Print Footer --}}
  <x-print.footer />

</body>
</html>
