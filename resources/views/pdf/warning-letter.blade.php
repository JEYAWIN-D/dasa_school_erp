<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Disciplinary Warning Notice — {{ $student->full_name }}</title>
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
    line-height: 1.6;
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
    line-height: 1.7;
    font-size: 10px;
    text-align: justify;
    color: #1e293b;
  }
  .highlight { font-weight: bold; color: #0f172a; }
  table.details {
    width: 100%;
    border-collapse: collapse;
    margin: 12px 0;
    background: #fff5f5;
    border: 1px solid #fca5a5;
  }
  table.details td {
    padding: 5px 10px;
    font-size: 9.5px;
    border-bottom: 1px solid #fee2e2;
  }
  table.details td:first-child {
    font-weight: bold;
    width: 35%;
    color: #991b1b;
  }
  .warning-box {
    background: #fef2f2;
    border: 1.5px solid #ef4444;
    border-radius: 4px;
    padding: 10px 14px;
    margin: 12px 0;
    font-size: 9.5px;
    color: #991b1b;
    line-height: 1.5;
  }
  .ack {
    margin-top: 24px;
    border-top: 1px dashed #94a3b8;
    padding-top: 12px;
    font-size: 9px;
    color: #475569;
  }
</style>
</head>
<body>
@php
  $admNo = $student->admission_no ?? $student->admission_number ?? '—';
@endphp

@include('pdf.partials.watermark')

@include('pdf.partials.header', [
  'title' => 'Formal Disciplinary Advisory & Warning Notice',
  'subtitle' => 'Ref: WRN/' . $admNo . '/' . now()->format('Y') . ' | Date: ' . now()->format('d F Y')
])

<table class="ref-table">
  <tr>
    <td style="text-align: left;"><strong>Reference No:</strong> WRN/{{ $admNo }}/{{ now()->format('Y') }}</td>
    <td style="text-align: right;"><strong>Date of Issue:</strong> {{ now()->format('d F Y') }}</td>
  </tr>
</table>

<div class="salutation">
  To,<br>
  <strong>The Parent / Guardian of {{ $student->full_name }}</strong><br>
  @if($student->residential_address ?? $student->address)
    {{ $student->residential_address ?? $student->address }}<br>
  @endif
</div>

<div class="body-text">
  <p>Dear Parent / Guardian,</p>
  <p>We regret to inform you that your ward <span class="highlight">{{ $student->full_name }}</span> (Admission No: <strong>{{ $admNo }}</strong>), studying in <span class="highlight">{{ $student->currentEnrollment?->class?->name }} {{ $student->currentEnrollment?->section?->name }}</span>, has been reported for a breach of institutional discipline as detailed below:</p>
</div>

<table class="details">
  <tr><td>Student Full Name</td><td><strong>{{ $student->full_name }}</strong></td></tr>
  <tr><td>Admission Number</td><td style="font-family: monospace;"><strong>{{ $admNo }}</strong></td></tr>
  <tr><td>Class &amp; Section</td><td>{{ $student->currentEnrollment?->class?->name }} {{ $student->currentEnrollment?->section?->name }}</td></tr>
  <tr><td>Incident Recorded Date</td><td>{{ $incident->incident_date ? \Carbon\Carbon::parse($incident->incident_date)->format('d F Y') : '—' }}</td></tr>
  <tr><td>Incident Classification</td><td><strong>{{ ucfirst(str_replace('_', ' ', $incident->incident_type)) }}</strong></td></tr>
  <tr><td>Factual Description</td><td>{{ $incident->description }}</td></tr>
  @if($incident->action_taken)
    <tr><td>Corrective Action Mandated</td><td><strong>{{ $incident->action_taken }}</strong></td></tr>
  @endif
</table>

<div class="warning-box">
  <strong>MANDATORY DISCIPLINARY ADVISORY:</strong> This serves as an official institutional warning. Repetition of similar conduct may result in serious administrative action including suspension, loss of privileges, or expulsion in accordance with the institutional code of conduct.
</div>

<div class="body-text">
  <p>You are requested to attend a joint counselling conference with the Class Teacher and School Principal within 3 working days to address this issue constructively.</p>
</div>

<table style="width: 100%; border-collapse: collapse; border: none; margin-top: 30px; page-break-inside: avoid;">
  <tr>
    <td style="width: 33%; text-align: left; vertical-align: bottom; border: none;">
      <div style="border-top: 1px dashed #64748b; width: 120px; margin-bottom: 3px;"></div>
      <span style="font-size: 8.5px; font-weight: bold; color: #334155;">Class Teacher</span>
    </td>
    <td style="width: 34%; text-align: center; vertical-align: bottom; border: none;">
      <div style="border-top: 1px dashed #64748b; width: 120px; margin: 0 auto 3px auto;"></div>
      <span style="font-size: 8.5px; font-weight: bold; color: #334155;">Discipline Committee</span>
    </td>
    <td style="width: 33%; text-align: right; vertical-align: bottom; border: none;">
      @php
        $sigBase64 = null;
        if (!empty($school?->principal_signature) && \Illuminate\Support\Facades\Storage::disk('public')->exists($school->principal_signature)) {
            $sigBase64 = 'data:image/png;base64,' . base64_encode(\Illuminate\Support\Facades\Storage::disk('public')->get($school->principal_signature));
        }
      @endphp
      @if($sigBase64)
        <img src="{{ $sigBase64 }}" alt="Signature" style="height: 28px; display: inline-block; margin-bottom: 2px;">
      @endif
      <div style="border-top: 1px dashed #64748b; width: 120px; margin-left: auto; margin-bottom: 3px;"></div>
      <span style="font-size: 8.5px; font-weight: bold; color: #334155;">Principal</span>
    </td>
  </tr>
</table>

<div class="ack">
  <p><strong>Parent / Guardian Acknowledgement:</strong> I, the parent/guardian of {{ $student->full_name }}, acknowledge receipt of this formal warning notice and have discussed its implications with my ward.</p>
  <table style="width: 100%; border: none; margin-top: 10px;">
    <tr>
      <td style="width: 45%; border: none;">Signature: __________________________</td>
      <td style="width: 25%; border: none;">Date: ______________</td>
      <td style="width: 30%; border: none;">Contact: _________________</td>
    </tr>
  </table>
</div>

@include('pdf.partials.footer')
</body>
</html>
