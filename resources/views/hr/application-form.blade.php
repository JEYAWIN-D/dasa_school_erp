<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staff Employment Application — {{ !empty($data['name']) ? $data['name'] : 'Erode Public School' }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Playfair+Display:wght@700;800;900&display=swap" rel="stylesheet">
  
  <style>
    @page {
      size: A4 portrait;
      margin: 8mm 10mm;
    }

    body {
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      background-color: #f1f5f9;
      color: #0f172a;
      margin: 0;
      padding: 0;
      -webkit-font-smoothing: antialiased;
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
    }

    .a4-sheet {
      width: 210mm;
      min-height: 297mm;
      padding: 10mm 14mm;
      margin: 20px auto;
      background: #ffffff;
      box-sizing: border-box;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 10px 25px -5px rgba(0, 0, 0, 0.12);
      position: relative;
    }

    .section-title {
      background-color: #1e293b !important;
      color: #ffffff !important;
      font-size: 10.5px;
      font-weight: 800;
      letter-spacing: 0.06em;
      text-transform: uppercase;
      padding: 4px 8px;
      margin-top: 8px;
      margin-bottom: 5px;
      border-radius: 3px;
    }

    table.data-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 11px;
    }

    table.data-table th, table.data-table td {
      border: 1px solid #94a3b8;
      padding: 4.5px 7px;
      vertical-align: middle;
    }

    table.data-table th {
      background-color: #f8fafc !important;
      font-weight: 700;
      color: #1e293b;
      text-align: left;
    }

    @media print {
      body {
        background-color: #ffffff !important;
        margin: 0 !important;
        padding: 0 !important;
      }
      .no-print {
        display: none !important;
      }
      .a4-sheet {
        box-shadow: none !important;
        margin: 0 !important;
        padding: 6mm 8mm !important;
        width: 100% !important;
        min-height: auto !important;
      }
      .page-break {
        page-break-after: always;
        break-after: page;
      }
    }
  </style>
</head>
<body>

  {{-- Print & Action Bar --}}
  <div class="no-print bg-slate-900 text-white px-6 py-3 sticky top-0 z-50 shadow-md flex items-center justify-between">
    <div class="flex items-center gap-3">
      <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center">
        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
      </div>
      <div>
        <p class="font-extrabold text-sm leading-tight">Official Staff Employment Application Form (A4)</p>
        <p class="text-xs text-slate-400">CBSE Affiliated Institutional Service Record &bull; {{ $data['employee_code'] ?? 'Application' }}</p>
      </div>
    </div>

    <div class="flex items-center gap-2">
      <button onclick="window.print()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg font-bold text-xs flex items-center gap-1.5 shadow transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        <span>Print Application / Save as PDF</span>
      </button>
      <button onclick="window.close(); history.back();" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-lg font-semibold text-xs transition">
        Close / Back
      </button>
    </div>
  </div>

  @php
    $fullName = !empty($data['name']) ? $data['name'] : trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
    if (empty($fullName)) $fullName = '—';
  @endphp

  {{-- Page 1: Institutional Header, Personal, Statutory, Contact & Position --}}
  <div class="a4-sheet">
    {{-- Institutional Header with Logo & CBSE Context --}}
    <div class="flex items-center justify-between border-b-2 border-slate-900 pb-3 mb-2.5">
      <div class="flex items-center gap-3.5">
        <img src="{{ asset('images/school-seal-badge.png') }}" class="w-16 h-16 object-contain flex-shrink-0" alt="EPS Crest">
        <div>
          <h1 class="text-xl font-black tracking-tight text-slate-900 uppercase" style="font-family:'Playfair Display', serif;">Erode Public School</h1>
          <p class="text-[11.5px] font-extrabold text-slate-800 uppercase tracking-wide">
            Senior Secondary &bull; Affiliated to Central Board of Secondary Education (CBSE), New Delhi
          </p>
          <p class="text-[10px] font-bold text-indigo-900 mt-0.5">
            Affiliation No: 1930811 &bull; School Code: 55762
          </p>
          <p class="text-[9.5px] text-slate-600 mt-0.5">
            Chennimalai Road, Erode, Tamil Nadu — 638001 | Phone: 0424-2223344 | Email: hr@erodepublicschool.edu.in
          </p>
        </div>
      </div>

      {{-- Photo Box --}}
      <div class="w-24 h-28 border-2 border-dashed border-slate-400 rounded flex flex-col items-center justify-center text-center p-1 bg-slate-50 flex-shrink-0">
        @if(!empty($data['photo_url']))
          <img src="{{ $data['photo_url'] }}" class="w-full h-full object-cover rounded" alt="Staff Photo">
        @else
          <svg class="w-8 h-8 text-slate-300 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
          <span class="text-[8px] font-semibold text-slate-400 uppercase leading-tight">Affix Recent Passport Photo</span>
        @endif
      </div>
    </div>

    {{-- Title & Meta Banner --}}
    <div class="bg-slate-100 border border-slate-300 rounded p-2 mb-2 flex items-center justify-between flex-wrap gap-2">
      <div>
        <h2 class="text-xs font-black uppercase text-slate-900 tracking-wider">Staff Employment Application &amp; Service Record Form</h2>
        <p class="text-[10px] text-slate-600">
          Category: <span class="font-bold text-slate-900 uppercase">{{ ucwords(str_replace('_', ' ', $data['employee_type'] ?? 'Teaching Staff')) }}</span> 
          &bull; Track: <span class="font-bold text-slate-900 uppercase">{{ ucfirst($data['employment_category'] ?? ($data['experience_type'] ?? 'Experienced')) }}</span>
        </p>
      </div>
      <div class="text-right">
        <p class="text-[11px] font-bold text-slate-900">Application / Emp ID: <span class="font-mono text-indigo-700 font-extrabold text-xs">{{ $data['employee_code'] ?? 'EPSS-AUTO' }}</span></p>
        <p class="text-[10px] text-slate-500">Date: {{ date('d-M-Y') }}</p>
      </div>
    </div>

    {{-- Section 1: Personal Information --}}
    <div class="section-title">Section 1: Personal Particulars</div>
    <table class="data-table">
      <tr>
        <th style="width: 20%;">Full Name</th>
        <td style="width: 30%;" class="font-bold text-slate-900 uppercase">{{ $fullName }}</td>
        <th style="width: 20%;">Date of Birth</th>
        <td style="width: 30%;">
          {{ !empty($data['dob']) ? date('d-M-Y', strtotime($data['dob'])) : '—' }}
          @if(!empty($data['dob']))
            <span class="text-[10px] text-slate-500 ml-1">({{ \Carbon\Carbon::parse($data['dob'])->age }} yrs)</span>
          @endif
        </td>
      </tr>
      <tr>
        <th>Gender</th>
        <td>{{ ucfirst($data['gender'] ?? '—') }}</td>
        <th>Blood Group</th>
        <td>{{ $data['blood_group'] ?? 'Not Specified' }}</td>
      </tr>
      <tr>
        <th>Mother Tongue</th>
        <td>{{ $data['mother_tongue'] ?? 'Tamil' }}</td>
        <th>Nationality &amp; Religion</th>
        <td>{{ $data['nationality'] ?? 'Indian' }} &bull; {{ $data['religion'] ?? 'Hindu' }}</td>
      </tr>
      <tr>
        <th>Caste / Community</th>
        <td>{{ $data['caste'] ?? 'General' }}</td>
        <th>Marital Status</th>
        <td>
          <span class="font-semibold">{{ ucfirst($data['marital_status'] ?? 'Single') }}</span>
          @if(strcasecmp($data['marital_status'] ?? '', 'Married') === 0 && !empty($data['number_of_children']))
            <span class="text-[10px] text-slate-600 block mt-0.5">Children: {{ $data['number_of_children'] }} (Male: {{ $data['children_male'] ?? 0 }}, Female: {{ $data['children_female'] ?? 0 }})</span>
          @endif
        </td>
      </tr>
    </table>

    {{-- Section 2: Statutory & Government Identification --}}
    <div class="section-title">Section 2: Statutory &amp; Government Identification</div>
    <table class="data-table">
      <tr>
        <th style="width: 25%;">Aadhaar Card No.</th>
        <td style="width: 25%;" class="font-mono font-bold">{{ !empty($data['aadhaar_no']) ? substr($data['aadhaar_no'], 0, 4) . ' ' . substr($data['aadhaar_no'], 4, 4) . ' ' . substr($data['aadhaar_no'], 8, 4) : '—' }}</td>
        <th style="width: 25%;">PAN Card No.</th>
        <td style="width: 25%;" class="font-mono font-bold">{{ $data['pan_no'] ?? '—' }}</td>
      </tr>
      <tr>
        <th>EPF / PF Account No.</th>
        <td class="font-mono">{{ $data['epf_no'] ?? ($data['pf_account_no'] ?? '—') }}</td>
        <th>UAN Number</th>
        <td class="font-mono">{{ $data['uan_no'] ?? '—' }}</td>
      </tr>
      <tr>
        <th>Professional Tax No.</th>
        <td class="font-mono">{{ $data['professional_tax_no'] ?? '—' }}</td>
        <th>Passport No.</th>
        <td class="font-mono">{{ $data['passport_no'] ?? '—' }}</td>
      </tr>
    </table>

    {{-- Section 3: Contact & Address Information --}}
    <div class="section-title">Section 3: Contact &amp; Residential Information</div>
    <table class="data-table">
      <tr>
        <th style="width: 20%;">Residential Address</th>
        <td style="width: 45%;" class="leading-snug">{{ $data['address'] ?? ($data['residential_address'] ?? '—') }}</td>
        <th style="width: 15%;">Postal PIN Code</th>
        <td style="width: 20%;" class="font-bold">{{ $data['pincode'] ?? '—' }}</td>
      </tr>
      <tr>
        <th>Permanent Address</th>
        <td colspan="3" class="leading-snug">{{ $data['permanent_address'] ?? ($data['address'] ?? 'Same as Residential Address') }}</td>
      </tr>
      <tr>
        <th>Contact Mobile</th>
        <td class="font-bold">{{ $data['mobile'] ?? ($data['contact_no'] ?? '—') }}</td>
        <th>Alternate Phone</th>
        <td>{{ $data['alternate_phone'] ?? '—' }}</td>
      </tr>
      <tr>
        <th>Official Email</th>
        <td class="font-mono text-indigo-700">{{ $data['official_email'] ?? ($data['email'] ?? '—') }}</td>
        <th>Personal Email</th>
        <td class="font-mono">{{ $data['personal_email'] ?? ($data['official_email'] ?? '—') }}</td>
      </tr>
    </table>

    {{-- Section 4: Emergency Contact Details --}}
    <div class="section-title">Section 4: Emergency Contact Particulars</div>
    <table class="data-table">
      <tr>
        <th style="width: 20%;">Contact Person</th>
        <td style="width: 30%;" class="font-bold">{{ $data['name_of_contact_person'] ?? ($data['emergency_contact_name'] ?? '—') }}</td>
        <th style="width: 20%;">Relationship</th>
        <td style="width: 30%;">{{ $data['contact_relationship'] ?? ($data['emergency_contact_relation'] ?? '—') }}</td>
      </tr>
      <tr>
        <th>Emergency Phone</th>
        <td class="font-bold">{{ $data['contact_no'] ?? ($data['emergency_contact_mobile'] ?? '—') }}</td>
        <th>Contact Address</th>
        <td>{{ $data['contact_address'] ?? 'Same as staff address' }}</td>
      </tr>
    </table>

    {{-- Section 5: Position & Employment Profile --}}
    <div class="section-title">Section 5: Position &amp; Appointment Particulars</div>
    <table class="data-table">
      <tr>
        <th style="width: 20%;">Designation / Post</th>
        <td style="width: 30%;" class="font-bold text-slate-900">{{ $data['designation'] ?? 'Staff' }}</td>
        <th style="width: 20%;">Department</th>
        <td style="width: 30%;">{{ $data['department'] ?? 'Academics' }}</td>
      </tr>
      <tr>
        <th>Date of Joining (DOJ)</th>
        <td class="font-bold">{{ !empty($data['joining_date']) ? date('d-M-Y', strtotime($data['joining_date'])) : '—' }}</td>
        <th>First Appt. as Teacher</th>
        <td>{{ !empty($data['first_appointment_date']) ? date('d-M-Y', strtotime($data['first_appointment_date'])) : (!empty($data['joining_date']) ? date('d-M-Y', strtotime($data['joining_date'])) : '—') }}</td>
      </tr>
      <tr>
        <th>Employment Status</th>
        <td class="font-semibold">{{ ucfirst($data['current_employment_status'] ?? 'Permanent') }}</td>
        <th>Pay Scale / Grade</th>
        <td>{{ $data['pay_scale'] ?? 'Standard CBSE Scale' }}</td>
      </tr>
      @if(!empty($data['basic_salary']) && (float)$data['basic_salary'] > 0)
      <tr>
        <th>Basic Pay (Monthly)</th>
        <td class="font-bold text-emerald-700">₹{{ number_format((float)$data['basic_salary'], 2) }}</td>
        <th>Allowances</th>
        <td>
          HRA: ₹{{ number_format((float)($data['hra'] ?? 0), 0) }} | DA: ₹{{ number_format((float)($data['da'] ?? 0), 0) }} | TA: ₹{{ number_format((float)($data['ta'] ?? 0), 0) }}
        </td>
      </tr>
      @endif
    </table>
  </div>

  {{-- Page 2: Education, Experience, In-service Training & Declarations --}}
  <div class="a4-sheet page-break">
    {{-- Header Short --}}
    <div class="flex items-center justify-between border-b border-slate-300 pb-2 mb-2">
      <div class="flex items-center gap-2">
        <img src="{{ asset('images/school-seal-badge.png') }}" class="w-8 h-8 object-contain" alt="EPS">
        <span class="font-bold text-xs text-slate-800 uppercase">Erode Public School &bull; CBSE Affiliation No: 1930811 &bull; Staff Service Record</span>
      </div>
      <div class="text-xs text-slate-500 font-mono">
        Emp Code: <span class="font-bold text-slate-800">{{ $data['employee_code'] ?? 'EPSS-AUTO' }}</span> | Page 2 of 2
      </div>
    </div>

    {{-- Section 6: Academic Qualifications --}}
    <div class="section-title">Section 6: Academic Qualifications (Chronological Record)</div>
    <table class="data-table mb-2">
      <thead>
        <tr>
          <th style="width: 25%;">Qualification / Degree</th>
          <th style="width: 30%;">Institution / College</th>
          <th style="width: 20%;">University / Board</th>
          <th style="width: 10%;">Medium</th>
          <th style="width: 8%; text-align: center;">Year</th>
          <th style="width: 7%; text-align: right;">% / Grade</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="font-bold text-slate-900">{{ $data['academic_qualification'] ?? 'Graduation' }}</td>
          <td>{{ $data['academic_institution'] ?? '—' }}</td>
          <td>{{ $data['academic_university'] ?? '—' }}</td>
          <td>{{ $data['academic_medium'] ?? 'English' }}</td>
          <td style="text-align: center;">{{ $data['academic_year_of_passing'] ?? '—' }}</td>
          <td style="text-align: right;" class="font-bold">{{ !empty($data['academic_percentage']) ? $data['academic_percentage'] . '%' : '—' }}</td>
        </tr>
        @if(!empty($data['other_qual_1_name']))
        <tr>
          <td class="font-bold text-slate-900">{{ $data['other_qual_1_name'] }}</td>
          <td>{{ $data['other_qual_1_institution'] ?? '—' }}</td>
          <td>{{ $data['other_qual_1_university'] ?? '—' }}</td>
          <td>{{ $data['other_qual_1_medium'] ?? 'English' }}</td>
          <td style="text-align: center;">{{ $data['other_qual_1_year_of_passing'] ?? '—' }}</td>
          <td style="text-align: right;" class="font-bold">{{ !empty($data['other_qual_1_percentage']) ? $data['other_qual_1_percentage'] . '%' : '—' }}</td>
        </tr>
        @endif
        @if(!empty($data['other_qual_2_name']))
        <tr>
          <td class="font-bold text-slate-900">{{ $data['other_qual_2_name'] }}</td>
          <td>{{ $data['other_qual_2_institution'] ?? '—' }}</td>
          <td>{{ $data['other_qual_2_university'] ?? '—' }}</td>
          <td>{{ $data['other_qual_2_medium'] ?? 'English' }}</td>
          <td style="text-align: center;">{{ $data['other_qual_2_year_of_passing'] ?? '—' }}</td>
          <td style="text-align: right;" class="font-bold">{{ !empty($data['other_qual_2_percentage']) ? $data['other_qual_2_percentage'] . '%' : '—' }}</td>
        </tr>
        @endif
      </tbody>
    </table>

    {{-- Section 7: Professional Teaching Qualifications --}}
    <div class="section-title">Section 7: Professional Teaching Qualifications (B.Ed / M.Ed / D.El.Ed / Diploma)</div>
    <table class="data-table mb-2">
      <thead>
        <tr>
          <th style="width: 25%;">Professional Degree</th>
          <th style="width: 30%;">College / Institution</th>
          <th style="width: 20%;">University / Board</th>
          <th style="width: 10%;">Medium</th>
          <th style="width: 8%; text-align: center;">Year</th>
          <th style="width: 7%; text-align: right;">% / Grade</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="font-bold text-indigo-900">{{ $data['professional_qualification'] ?? 'Professional Teaching Diploma / B.Ed' }}</td>
          <td>{{ $data['professional_institution'] ?? '—' }}</td>
          <td>{{ $data['professional_university'] ?? '—' }}</td>
          <td>{{ $data['professional_medium'] ?? 'English' }}</td>
          <td style="text-align: center;">{{ $data['professional_year_of_passing'] ?? '—' }}</td>
          <td style="text-align: right;" class="font-bold">{{ !empty($data['professional_percentage']) ? $data['professional_percentage'] . '%' : '—' }}</td>
        </tr>
      </tbody>
    </table>

    {{-- Section 8: Previous Employment Record or Fresher Certification --}}
    <div class="section-title">Section 8: Prior Employment Experience / Candidate Track</div>
    @php
      $isFresher = ($data['experience_type'] ?? '') === 'fresher' || ($data['employment_category'] ?? '') === 'fresher' || (empty($data['previous_school']) && empty($data['experience_years']));
    @endphp

    @if($isFresher)
      <div class="p-3 bg-emerald-50 border border-emerald-300 rounded mb-2">
        <div class="flex items-center gap-2">
          <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-emerald-600 text-white font-bold text-xs">✓</span>
          <p class="text-xs font-bold text-emerald-950 uppercase tracking-wide">Registered as a Fresher Candidate</p>
        </div>
        <p class="text-[11px] text-emerald-900 mt-1 leading-relaxed">
          The candidate is a fresh applicant with no prior formal school teaching or institutional employment history. Onboarding is performed under the CBSE Induction &amp; Mentorship Program. Total Prior Experience: <strong>0 Years, 0 Months</strong>.
        </p>
      </div>
    @else
      <table class="data-table mb-2">
        <thead>
          <tr>
            <th style="width: 35%;">Institution / Organization</th>
            <th style="width: 25%;">Designation / Post Held</th>
            <th style="width: 15%;">From Date</th>
            <th style="width: 15%;">To Date</th>
            <th style="width: 10%;">Experience</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="font-semibold">{{ $data['previous_school'] ?? 'Previous Educational Institution' }}</td>
            <td class="font-bold text-slate-800">{{ $data['previous_designation'] ?? 'Teacher' }}</td>
            <td>{{ !empty($data['previous_from_date']) ? date('d-M-Y', strtotime($data['previous_from_date'])) : '—' }}</td>
            <td>{{ !empty($data['previous_to_date']) ? date('d-M-Y', strtotime($data['previous_to_date'])) : '—' }}</td>
            <td class="font-bold">
              {{ $data['experience_years'] ?? 0 }}y {{ $data['experience_months'] ?? 0 }}m
            </td>
          </tr>
        </tbody>
      </table>
    @endif

    {{-- Section 9: Specialized Skills & In-service Training --}}
    <div class="section-title">Section 9: In-Service Training &amp; Subject Competencies</div>
    <table class="data-table mb-3">
      <tr>
        <th style="width: 30%;">In-Service Training Attended</th>
        <td style="width: 70%;">{{ $data['training_attended_before_recruitment'] ?? 'None / Not Applicable' }}</td>
      </tr>
      <tr>
        <th>Additional Qualifications / Certifications</th>
        <td>{{ $data['additional_qual_after_recruitment'] ?? 'None / Not Applicable' }}</td>
      </tr>
      @if(!empty($data['major_subject']) || !empty($data['minor_subject']))
      <tr>
        <th>Subject Competencies</th>
        <td>Major: <strong>{{ $data['major_subject'] ?? '—' }}</strong> | Minor: <strong>{{ $data['minor_subject'] ?? '—' }}</strong> | Lab: {{ $data['lab_subjects'] ?? 'General' }}</td>
      </tr>
      @endif
      @if(!empty($data['special_skills']))
      <tr>
        <th>Special Skills / Co-Curricular</th>
        <td>{{ $data['special_skills'] }}</td>
      </tr>
      @endif
    </table>

    {{-- Section 10: Official Declarations & Signatures --}}
    <div class="section-title">Section 10: Declarations &amp; Institutional Signatures</div>
    <div class="border border-slate-300 rounded p-2.5 bg-slate-50 mb-6 text-[10px] text-slate-700 leading-normal">
      <p class="font-bold text-slate-900 mb-1">CANDIDATE DECLARATION:</p>
      <p>
        I hereby declare that all the information, particulars, and academic records furnished in this application are true, complete, and accurate to the best of my knowledge and belief. I have not suppressed or misrepresented any material fact. I understand that if any information is found incorrect or misleading at any stage, my appointment is liable to immediate termination without notice, in accordance with CBSE Affiliation By-Laws and school service rules.
      </p>
    </div>

    {{-- Signature Boxes --}}
    <div class="grid grid-cols-3 gap-6 pt-6 mt-4">
      <div class="text-center">
        <div class="border-b border-slate-900 pb-1 h-12 flex items-end justify-center">
          <span class="text-[10px] text-slate-400 font-mono italic">[Signature on File]</span>
        </div>
        <p class="text-[11px] font-bold text-slate-900 mt-1 uppercase">Signature of Applicant</p>
        <p class="text-[9.5px] text-slate-500">Date: _______________</p>
      </div>

      <div class="text-center">
        <div class="border-b border-slate-900 pb-1 h-12 flex items-end justify-center">
          <span class="text-[10px] text-slate-400 font-mono italic">[Verified by HR]</span>
        </div>
        <p class="text-[11px] font-bold text-slate-900 mt-1 uppercase">HR / Scrutiny Officer</p>
        <p class="text-[9.5px] text-slate-500">Seal &amp; Signature</p>
      </div>

      <div class="text-center">
        <div class="border-b border-slate-900 pb-1 h-12 flex items-end justify-center">
          <span class="text-[10px] text-slate-400 font-mono italic">[Institutional Approval]</span>
        </div>
        <p class="text-[11px] font-bold text-slate-900 mt-1 uppercase">Principal / Correspondent</p>
        <p class="text-[9.5px] text-slate-500">Erode Public School &bull; CBSE</p>
      </div>
    </div>
  </div>

</body>
</html>
