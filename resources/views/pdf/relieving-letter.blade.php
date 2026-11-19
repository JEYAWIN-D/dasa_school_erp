<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Official Relieving Letter — {{ $employee->full_name ?? ($employee->first_name . ' ' . $employee->last_name) }}</title>
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
  'title' => 'Official Relieving Order'
])

<table class="ref-table">
  <tr>
    <td style="text-align: left;"><strong>Ref No:</strong> {{ $refNumber ?? ('REL-' . date('Y') . '-' . str_pad($employee->id, 4, '0', STR_PAD_LEFT)) }}</td>
    <td style="text-align: right;"><strong>Date of Issue:</strong> {{ now()->format('d F Y') }}</td>
  </tr>
</table>

<p style="font-weight: bold; margin-bottom: 14px;">TO WHOMSOEVER IT MAY CONCERN</p>

<p>
  This is to certify that <strong>{{ $employee->full_name ?? ($employee->first_name . ' ' . $employee->last_name) }}</strong>,
  bearing Employee Code <strong>{{ $employee->employee_code ?? $employee->employee_number ?? $employee->id }}</strong>, was employed with
  <strong>{{ $school->school_name ?? config('app.name', 'Erode Public School') }}</strong> as
  <strong>{{ is_object($employee->designation) ? $employee->designation->name : ($employee->designation ?? 'Staff Member') }}</strong> in the
  Department of <strong>{{ is_object($employee->department) ? $employee->department->name : ($employee->department ?? 'General') }}</strong> from
  <strong>{{ $employee->joining_date ? \Carbon\Carbon::parse($employee->joining_date)->format('d F Y') : ($employee->date_of_joining ? \Carbon\Carbon::parse($employee->date_of_joining)->format('d F Y') : '—') }}</strong>
  to <strong>{{ $employee->date_of_leaving ? \Carbon\Carbon::parse($employee->date_of_leaving)->format('d F Y') : now()->format('d F Y') }}</strong>.
</p>

<p>
  {{ $employee->gender === 'female' ? 'She' : 'He' }} has been relieved from all institutional duties and responsibilities with effect from the close of working hours on
  <strong>{{ $employee->date_of_leaving ? \Carbon\Carbon::parse($employee->date_of_leaving)->format('d F Y') : now()->format('d F Y') }}</strong> pursuant to the acceptance of {{ $employee->gender === 'female' ? 'her' : 'his' }} resignation.
</p>

<p>
  It is further verified that all institution assets, records, and keys entrusted to {{ $employee->gender === 'female' ? 'her' : 'him' }} have been fully handed over, and no administrative or financial dues remain outstanding against {{ $employee->gender === 'female' ? 'her' : 'him' }}.
</p>

<p>
  We appreciate {{ $employee->gender === 'female' ? 'her' : 'his' }} services to {{ $school->school_name ?? config('app.name', 'the School') }} and wish {{ $employee->gender === 'female' ? 'her' : 'him' }} success in all future pursuits.
</p>

@include('pdf.partials.signatures', ['type' => 'certificate'])

@include('pdf.partials.footer')
</body>
</html>
