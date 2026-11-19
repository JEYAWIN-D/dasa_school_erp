<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Application Form #{{ $app->application_number }}</title>
<style>
  @page {
    size: A4 portrait;
    margin: 12mm 15mm 16mm 15mm;
  }
  body {
    font-family: Arial, sans-serif;
    font-size: 9.5px;
    color: #1e293b;
    margin: 0;
    padding: 0;
  }
  .app-meta {
    width: 100%;
    border-collapse: collapse;
    margin: 6px 0 10px 0;
    font-size: 9px;
  }
  .app-meta td { padding: 4px 8px; border: 1px solid #cbd5e1; background: #f8fafc; }
  table.data-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 10px;
  }
  table.data-table td {
    padding: 4.5px 8px;
    vertical-align: top;
    border: 1px solid #cbd5e1;
    font-size: 9px;
  }
  table.data-table td:first-child {
    width: 35%;
    font-weight: 600;
    color: #334155;
    background: #f8fafc;
  }
  .section-title {
    font-size: 9px;
    font-weight: bold;
    background: #f1f5f9;
    padding: 4px 8px;
    margin-top: 10px;
    margin-bottom: 4px;
    border-left: 3px solid #0f172a;
    text-transform: uppercase;
    color: #0f172a;
  }
</style>
</head>
<body>
@include('pdf.partials.watermark')

@include('pdf.partials.header', [
  'title' => 'Official Student Admission Application Record',
  'subtitle' => 'Application No: ' . $app->application_number . ' | Submitted: ' . $app->created_at->format('d M Y')
])

<table class="app-meta">
  <tr>
    <td style="width: 25%;">Application No: <strong style="font-family: monospace;">{{ $app->application_number }}</strong></td>
    <td style="width: 25%;">Target Class: <strong>{{ $app->formConfig?->class?->name ?? 'General Admission' }}</strong></td>
    <td style="width: 25%;">Date: <strong>{{ $app->created_at->format('d M Y') }}</strong></td>
    <td style="width: 25%;">Current Status: <strong style="color: #1e40af;">{{ strtoupper(str_replace('_',' ',$app->status)) }}</strong></td>
  </tr>
</table>

<div class="section-title">1. Student Profile &amp; Demographics</div>
<table class="data-table">
  @foreach($app->form_data ?? [] as $key => $value)
    @if(!in_array($key, ['parent_name','parent_mobile','parent_email','address']))
    <tr>
      <td>{{ ucwords(str_replace('_', ' ', $key)) }}</td>
      <td>{{ is_array($value) ? implode(', ', $value) : $value }}</td>
    </tr>
    @endif
  @endforeach
</table>

<div class="section-title">2. Parent / Legal Guardian Details</div>
<table class="data-table">
  <tr><td>Parent / Guardian Name</td><td><strong>{{ $app->parent_name ?? ($app->form_data['parent_name'] ?? '—') }}</strong></td></tr>
  <tr><td>Contact Mobile Number</td><td>{{ $app->parent_mobile }}</td></tr>
  <tr><td>Official Email Address</td><td>{{ $app->parent_email ?? ($app->form_data['parent_email'] ?? '—') }}</td></tr>
  @if(!empty($app->form_data['address']))
    <tr><td>Residential Campus Address</td><td>{{ $app->form_data['address'] }}</td></tr>
  @endif
</table>

<div class="section-title">3. Uploaded Supporting Credentials</div>
<table class="data-table">
  @forelse($app->documents ?? [] as $name => $path)
  <tr>
    <td>{{ ucwords(str_replace('_', ' ', $name)) }}</td>
    <td style="color: #166534; font-weight: bold;">Verified &bull; Uploaded on File</td>
  </tr>
  @empty
  <tr><td colspan="2" style="color: #94a3b8; font-style: italic;">No electronic documents uploaded. Physical copies to be verified.</td></tr>
  @endforelse
</table>

@if($app->admin_notes)
  <div class="section-title">Administrative Scrutiny Notes</div>
  <p style="padding: 6px 10px; font-style: italic; color: #475569; background: #fffbeb; border: 1px solid #fde68a; border-radius: 3px; margin: 4px 0 10px 0;">{{ $app->admin_notes }}</p>
@endif

<table style="width: 100%; border-collapse: collapse; border: none; margin-top: 35px; page-break-inside: avoid;">
  <tr>
    <td style="width: 50%; text-align: left; vertical-align: bottom; border: none;">
      <div style="border-top: 1px dashed #64748b; width: 140px; margin-bottom: 3px;"></div>
      <span style="font-size: 8px; font-weight: bold; color: #334155;">Applicant / Parent Signature</span>
    </td>
    <td style="width: 50%; text-align: right; vertical-align: bottom; border: none;">
      @php
        $sigBase64 = null;
        if (!empty($school?->principal_signature) && \Illuminate\Support\Facades\Storage::disk('public')->exists($school->principal_signature)) {
            $sigBase64 = 'data:image/png;base64,' . base64_encode(\Illuminate\Support\Facades\Storage::disk('public')->get($school->principal_signature));
        }
      @endphp
      @if($sigBase64)
        <img src="{{ $sigBase64 }}" alt="Signature" style="height: 28px; display: inline-block; margin-bottom: 2px;">
      @endif
      <div style="border-top: 1px dashed #64748b; width: 140px; margin-left: auto; margin-bottom: 3px;"></div>
      <span style="font-size: 8px; font-weight: bold; color: #334155;">Admissions In-Charge / Principal</span>
    </td>
  </tr>
</table>

@include('pdf.partials.footer')
</body>
</html>
