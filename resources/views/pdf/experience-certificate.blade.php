<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Experience Certificate — {{ $employee->full_name }}</title>
<style>
  @page {
    size: A4 portrait;
    margin: 15mm 20mm 18mm 20mm;
  }
  * { box-sizing: border-box; }
  body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 11px;
    color: #1e293b;
    line-height: 1.8;
    background: #fff;
  }
  .meta-bar {
    width: 100%;
    margin-bottom: 24px;
    font-size: 10px;
    color: #475569;
    border-collapse: collapse;
  }
  .meta-bar td { padding: 0; border: none; }
  .to-whom {
    text-align: center;
    font-size: 12px;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: 2px;
    margin: 20px 0 15px 0;
  }
  .cert-paragraph {
    font-size: 11.5px;
    line-height: 2.1;
    text-align: justify;
    margin-bottom: 16px;
    color: #1e293b;
  }
  .cert-paragraph strong {
    color: #0f172a;
  }
</style>
</head>
<body>
@include('pdf.partials.watermark')

@include('pdf.partials.header', [
  'title' => 'Service & Experience Certificate'
])

<table class="meta-bar">
  <tr>
    <td style="text-align: left;"><strong>Ref No:</strong> EXP-{{ date('Y') }}-{{ str_pad($employee->id, 4, '0', STR_PAD_LEFT) }}</td>
    <td style="text-align: right;"><strong>Date of Issue:</strong> {{ now()->format('F d, Y') }}</td>
  </tr>
</table>

<div class="to-whom">TO WHOMSOEVER IT MAY CONCERN</div>

<div class="cert-paragraph">
  This is to certify that <strong>{{ $employee->full_name }}</strong> (Employee Code: <strong>{{ $employee->employee_code ?? $employee->employee_number ?? $employee->id }}</strong>) was employed with <strong>{{ $school->school_name ?? config('app.name', 'Erode Public School') }}</strong> as <strong>{{ is_object($employee->designation) ? $employee->designation->name : ($employee->designation ?? 'Faculty') }}</strong> in the Department of <strong>{{ is_object($employee->department) ? $employee->department->name : ($employee->department ?? 'Academics') }}</strong>.
</div>

<div class="cert-paragraph">
  <strong>Period of Service:</strong> From <strong>{{ $employee->joining_date ? \Carbon\Carbon::parse($employee->joining_date)->format('F d, Y') : ($employee->date_of_joining ? \Carbon\Carbon::parse($employee->date_of_joining)->format('F d, Y') : '—') }}</strong> to <strong>{{ $employee->date_of_leaving ? \Carbon\Carbon::parse($employee->date_of_leaving)->format('F d, Y') : 'Present' }}</strong>.
</div>

<div class="cert-paragraph">
  During {{ $employee->gender === 'female' ? 'her' : 'his' }} tenure with our institution, {{ $employee->gender === 'female' ? 'she' : 'he' }} discharged {{ $employee->gender === 'female' ? 'her' : 'his' }} duties with exceptional competence, sincerity, and professionalism. {{ ucfirst($employee->gender === 'female' ? 'her' : 'his') }} character, moral conduct, and relationship with students, parents, and colleagues have been exemplary.
</div>

<div class="cert-paragraph">
  We express our appreciation for {{ $employee->gender === 'female' ? 'her' : 'his' }} valuable service and extend our best wishes for {{ $employee->gender === 'female' ? 'her' : 'his' }} future professional endeavours and career growth.
</div>

@include('pdf.partials.signatures', ['type' => 'certificate'])

@include('pdf.partials.footer')
</body>
</html>
