<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Roll Number Roster — Class {{ $class?->name }} {{ $section ? 'Section ' . $section->name : '' }}</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <style>
    @page { size: A4 portrait; margin: 0 !important; }
    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; color: #1e293b; font-size: 10pt; line-height: 1.4; margin: 0; padding: 10mm 12mm 10mm 18mm !important; box-sizing: border-box !important; background: #fff; }
    
    .meta-bar { display: flex; justify-content: space-between; font-size: 9.5pt; font-weight: 600; margin-bottom: 12px; border: 1px solid #cbd5e1; background: #f8fafc; padding: 8px 12px; border-radius: 6px; }
    table.data-table { width: 100%; border-collapse: collapse; margin-top: 6px; font-size: 9pt; }
    table.data-table th { background-color: #f1f5f9; border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; font-weight: bold; text-transform: uppercase; font-size: 8pt; color: #334155; }
    table.data-table td { border: 1px solid #cbd5e1; padding: 5px 8px; }
    table.data-table tr:nth-child(even) { background-color: #f8fafc; }

    @media print {
      body { padding: 0 !important; background: #fff !important; }
      .no-print { display: none !important; }
      * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    }
  </style>
</head>
<body>

  {{-- Print Toolbar --}}
  <x-print.controls title="Roll Number Roster Sheet" defaultSize="a4" defaultOrientation="portrait" :backUrl="route('students.roll-numbers')" />

  {{-- Watermark & Centralized Header --}}
  <x-print.watermark />
  <x-print.header 
    title="CLASS ROLL NUMBER &amp; ATTENDANCE ROSTER" 
    :subtitle="'Class ' . ($class?->name ?? 'All') . ($section ? ' — Section ' . $section->name : '')" 
    :academicYear="$academicYear?->name ?? (now()->year . '-' . (now()->year + 1))" 
  />

  <div class="meta-bar">
    <div>Standard: <strong>Class {{ $class?->name ?? 'All' }}</strong></div>
    @if($section) <div>Section: <strong>Section {{ $section->name }}</strong></div> @endif
    <div>Class Teacher: <strong>{{ $section?->classTeacher?->name ?? 'Not Assigned' }}</strong></div>
    <div>Total Strength: <strong>{{ $enrollments->count() }} Students</strong></div>
  </div>

  <table class="data-table">
    <thead>
      <tr>
        <th style="width: 40px; text-align:center;">#</th>
        <th style="width: 70px; text-align:center;">Roll No.</th>
        <th style="width: 100px; text-align:center;">Admission No.</th>
        <th>Student Full Name</th>
        <th style="width: 70px; text-align:center;">Gender</th>
        <th style="width: 90px; text-align:center;">DOB</th>
        <th>Parent / Guardian</th>
        <th style="width: 110px; text-align:center;">Parent Mobile</th>
      </tr>
    </thead>
    <tbody>
      @forelse($enrollments as $idx => $enr)
        @php $st = $enr->student; @endphp
        <tr>
          <td style="text-align:center; color:#64748b;">{{ $idx + 1 }}</td>
          <td style="text-align:center; font-family:monospace; font-weight:bold; color:#0f172a;">{{ $enr->roll_number ?: '—' }}</td>
          <td style="text-align:center; font-family:monospace; font-weight:bold;">{{ $st?->admission_no }}</td>
          <td style="font-weight:600; color:#0f172a;">{{ $st?->full_name }}</td>
          <td style="text-align:center;">{{ ucfirst($st?->gender ?: '') }}</td>
          <td style="text-align:center;">{{ $st?->dob ? $st->dob->format('d/m/Y') : '—' }}</td>
          <td>{{ $st?->father_name ?: ($st?->mother_name ?: '—') }}</td>
          <td style="text-align:center; font-family:monospace;">{{ $st?->father_mobile ?: ($st?->mobile ?: '—') }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="8" style="text-align:center; padding: 20px; color:#94a3b8;">No enrolled students found for the selected criteria.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  {{-- Signatures & Footer --}}
  <div style="margin-top: 30px;">
    <x-print.signatures mode="academic" />
  </div>

  <x-print.footer />

</body>
</html>
