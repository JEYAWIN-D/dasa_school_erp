<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Admission Confirmation — {{ $enquiry->student_name }}</title>
<style>
  @page {
    size: A4 portrait;
    margin: 12mm 15mm 16mm 15mm;
  }
  * { box-sizing: border-box; }
  body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 10px;
    color: #1e293b;
    margin: 0;
    padding: 0;
    line-height: 1.5;
  }
  .ref-table {
    width: 100%;
    border-collapse: collapse;
    margin: 8px 0 12px 0;
    font-size: 9.5px;
    color: #475569;
  }
  .ref-table td { padding: 0; border: none; }
  .salutation {
    margin-bottom: 10px;
    font-size: 10.5px;
    line-height: 1.4;
  }
  .body-text {
    line-height: 1.6;
    font-size: 10px;
    text-align: justify;
    color: #1e293b;
  }
  .highlight { font-weight: bold; color: #0f172a; }
  table.details {
    width: 100%;
    border-collapse: collapse;
    margin: 12px 0;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
  }
  table.details td {
    padding: 5px 10px;
    font-size: 9.5px;
    border-bottom: 1px solid #e2e8f0;
  }
  table.details td:first-child {
    font-weight: bold;
    width: 35%;
    color: #334155;
  }
  .notice {
    margin-top: 15px;
    background: #eff6ff;
    border-left: 3px solid #3b82f6;
    padding: 8px 12px;
    font-size: 9px;
    border-radius: 2px;
    color: #1e40af;
  }
</style>
</head>
<body>
@include('pdf.partials.watermark')

@include('pdf.partials.header', [
  'title' => 'Provisional Admission Confirmation Letter',
  'subtitle' => 'Ref: ' . $enquiry->enquiry_number . ' | Date: ' . now()->format('d F Y')
])

<table class="ref-table">
  <tr>
    <td style="text-align: left;"><strong>Reference No:</strong> {{ $enquiry->enquiry_number }}</td>
    <td style="text-align: right;"><strong>Date of Issue:</strong> {{ now()->format('d F Y') }}</td>
  </tr>
</table>

<div class="salutation">
  To,<br>
  <strong>{{ $enquiry->parent_name }}</strong><br>
  @if($enquiry->address){{ $enquiry->address }}<br>@endif
  Contact: {{ $enquiry->parent_mobile }}
</div>

<div class="body-text">
  <p>Dear {{ $enquiry->parent_name }},</p>
  <p>We are pleased to inform you that your ward <span class="highlight">{{ $enquiry->student_name }}</span> has been provisionally selected and offered admission to <span class="highlight">{{ $school->school_name ?? config('app.name', 'Erode Public School') }}</span> for the academic session <span class="highlight">{{ $enquiry->academicYear?->name ?? (date('Y') . '-' . (date('Y') + 1)) }}</span>.</p>
</div>

<table class="details">
  <tr><td>Candidate Name</td><td><strong>{{ $enquiry->student_name }}</strong></td></tr>
  <tr><td>Class Admitted</td><td><strong>{{ $enquiry->class?->name ?? '—' }}</strong></td></tr>
  <tr><td>Academic Session</td><td>{{ $enquiry->academicYear?->name ?? (date('Y') . '-' . (date('Y') + 1)) }}</td></tr>
  <tr><td>Enquiry Reference</td><td>{{ $enquiry->enquiry_number }}</td></tr>
  <tr><td>Parent / Guardian Name</td><td>{{ $enquiry->parent_name }}</td></tr>
  <tr><td>Registered Contact</td><td>{{ $enquiry->parent_mobile }}</td></tr>
  <tr><td>Confirmation Date</td><td>{{ now()->format('d F Y') }}</td></tr>
</table>

<div class="body-text">
  <p>Please report to the school admissions office with the following original credentials and self-attested photocopies within <strong>7 working days</strong> to finalize the enrollment:</p>
  <ol style="margin-left: 20px; line-height: 1.7; margin-top: 4px;">
    <li>Original Transfer Certificate (TC) issued by the previous recognized school.</li>
    <li>Official Birth Certificate issued by the competent authority (original + photocopy).</li>
    <li>Student and Parent Aadhaar Cards.</li>
    <li>Recent passport-size colour photographs (4 copies).</li>
    <li>Academic Marksheet / Cumulative Progress Record of the previous completed grade.</li>
    <li>Community / Category Certificate (if applicable).</li>
  </ol>
  <p style="margin-top: 6px;">We look forward to partnering with your family to nurture {{ $enquiry->student_name }}'s educational journey and character development.</p>
</div>

<div class="notice">
  <strong>Important Note:</strong> This letter confirms provisional eligibility. Final admission and roll number generation will be finalized upon verification of original documents and receipt of term fees.
</div>

@include('pdf.partials.signatures', ['type' => 'certificate'])

@include('pdf.partials.footer')
</body>
</html>
