<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Failed Students List — {{ $exam->name }}</title>
<style>
  @page {
    size: A4 portrait;
    margin: 12mm 15mm 16mm 15mm;
  }
  body { font-family: Arial, sans-serif; font-size: 9.5px; color: #1e293b; margin: 0; padding: 0; }
  table.data-table { width: 100%; border-collapse: collapse; margin-top: 8px; margin-bottom: 12px; }
  table.data-table th { background: #991b1b; color: #fff; padding: 5px 7px; text-align: left; font-size: 8.5px; text-transform: uppercase; font-weight: bold; border: 1px solid #7f1d1d; }
  table.data-table td { padding: 4.5px 7px; border: 1px solid #fecaca; font-size: 9px; }
  table.data-table tr:nth-child(even) td { background: #fef2f2; }
  .supp-yes { color: #d97706; font-weight: bold; }
  .supp-no  { color: #dc2626; font-weight: bold; }
</style>
</head>
<body>
@include('pdf.partials.watermark')

@include('pdf.partials.header', [
  'title' => 'Examination Remedial / Supplementary Eligibility Register',
  'subtitle' => 'Exam: ' . ($exam->name ?? 'Standard') . ' | Supplementary Threshold: ' . $threshold . ' subject(s)'
])

<table class="data-table">
  <thead>
    <tr>
      <th style="width: 25px;">#</th>
      <th style="width: 85px;">Adm No</th>
      <th>Student Full Name</th>
      <th>Subjects Requiring Remedial / Retest</th>
      <th style="text-align: center; width: 65px;">Fail Count</th>
      <th style="text-align: center; width: 85px;">Supplementary?</th>
    </tr>
  </thead>
  <tbody>
    @php $i = 1; @endphp
    @forelse($failed as $row)
    <tr>
      <td>{{ $i++ }}</td>
      <td style="font-family: monospace;">{{ $row['student']?->admission_no ?? $row['student']?->admission_number ?? '—' }}</td>
      <td><strong>{{ $row['student']?->full_name }}</strong></td>
      <td style="color: #991b1b; font-weight: 500;">{{ $row['subjects']->implode(', ') }}</td>
      <td style="text-align: center; font-weight: bold; color: #dc2626;">{{ $row['subjects']->count() }}</td>
      <td style="text-align: center;" class="{{ $row['supp_eligible'] ? 'supp-yes' : 'supp-no' }}">
        {{ $row['supp_eligible'] ? 'ELIGIBLE' : 'INELIGIBLE' }}
      </td>
    </tr>
    @empty
    <tr><td colspan="6" style="text-align: center; padding: 20px; color: #166534; font-weight: bold;">Congratulations! All enrolled students cleared all scheduled subjects.</td></tr>
    @endforelse
  </tbody>
</table>

@include('pdf.partials.signatures', ['type' => 'academic'])

@include('pdf.partials.footer', [
  'footerNote' => 'Remedial examination master register. Total candidates flagged: ' . $failed->count() . ' (Supplementary eligible: ' . $failed->where('supp_eligible', true)->count() . ').'
])
</body>
</html>
