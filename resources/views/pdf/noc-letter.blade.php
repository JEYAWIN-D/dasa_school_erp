<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>No Objection Certificate (NOC) — {{ $employee->full_name ?? ($employee->first_name . ' ' . $employee->last_name) }}</title>
<style>
  @page {
    size: A4 portrait;
    margin: 15mm 20mm 18mm 20mm;
  }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 11px;
    color: #1e293b;
    line-height: 1.8;
  }
  .ref-table {
    width: 100%;
    border-collapse: collapse;
    margin: 10px 0 20px 0;
    font-size: 10px;
    color: #475569;
  }
  .ref-table td { padding: 0; border: none; }
  p { margin-bottom: 14px; text-align: justify; line-height: 2; }
  strong { color: #0f172a; }
</style>
</head>
<body>
@include('pdf.partials.watermark')

@include('pdf.partials.header', [
  'title' => 'No Objection Certificate (NOC)'
])

<table class="ref-table">
  <tr>
    <td style="text-align: left;"><strong>Ref No:</strong> {{ $refNumber ?? ('NOC-' . date('Y') . '-' . str_pad($employee->id, 4, '0', STR_PAD_LEFT)) }}</td>
    <td style="text-align: right;"><strong>Date of Issue:</strong> {{ now()->format('d F Y') }}</td>
  </tr>
</table>

<p style="font-weight: bold; margin-bottom: 14px;">TO WHOMSOEVER IT MAY CONCERN</p>

<p>
  This is to certify that <strong>{{ $employee->full_name ?? ($employee->first_name . ' ' . $employee->last_name) }}</strong>,
  bearing Employee Code <strong>{{ $employee->employee_code ?? $employee->employee_number ?? $employee->id }}</strong>, is employed with
  <strong>{{ $school->school_name ?? config('app.name', 'Erode Public School') }}</strong> as
  <strong>{{ is_object($employee->designation) ? $employee->designation->name : ($employee->designation ?? 'Staff Member') }}</strong> in the
  Department of <strong>{{ is_object($employee->department) ? $employee->department->name : ($employee->department ?? 'General') }}</strong>.
</p>

<p>
  The Management and Administration of this institution have <strong>no objection</strong> to {{ $employee->gender === 'female' ? 'her' : 'his' }} application for further academic education / higher study / passport / visa / examinations or external employment opportunities for which this certificate has been officially requested.
</p>

<p>
  This certificate is issued in good faith upon the specific formal request of the individual without any financial liability on the part of the institution.
</p>

@include('pdf.partials.signatures', ['type' => 'certificate'])

@include('pdf.partials.footer')
</body>
</html>
