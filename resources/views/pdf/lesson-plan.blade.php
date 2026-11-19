<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Lesson Plan — {{ $plan->subject?->name }} ({{ $plan->class?->name }})</title>
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
  table.meta-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 6px;
    margin-bottom: 12px;
  }
  table.meta-table td {
    padding: 4.5px 7px;
    border: 1px solid #cbd5e1;
    font-size: 9px;
  }
  table.meta-table td:first-child, table.meta-table td:nth-child(3) {
    font-weight: bold;
    background: #f8fafc;
    width: 20%;
    color: #334155;
  }
  .section { margin-bottom: 10px; }
  .section-title {
    background: #f1f5f9;
    padding: 4px 8px;
    font-weight: bold;
    font-size: 9px;
    border-left: 3px solid #0f172a;
    margin-bottom: 4px;
    color: #0f172a;
    text-transform: uppercase;
  }
  .content-box {
    border: 1px solid #cbd5e1;
    padding: 6px 9px;
    border-radius: 3px;
    font-size: 9px;
    line-height: 1.5;
    background: #fff;
  }
  .badge { display: inline-block; padding: 1.5px 6px; border-radius: 4px; font-size: 8px; font-weight: bold; }
  .badge-green { background: #d1fae5; color: #065f46; }
  .badge-blue  { background: #dbeafe; color: #1e40af; }
  .badge-amber { background: #fef3c7; color: #92400e; }
  .badge-red   { background: #fee2e2; color: #991b1b; }
</style>
</head>
<body>
@include('pdf.partials.watermark')

@include('pdf.partials.header', [
  'title' => 'Faculty Pedagogical Lesson Plan',
  'subtitle' => 'Subject: ' . ($plan->subject?->name ?? '—') . ' | Class: ' . ($plan->class?->name ?? '—') . ' | Date: ' . ($plan->plan_date ? $plan->plan_date->format('d M Y') : '—')
])

<table class="meta-table">
  <tr>
    <td>Grade / Class</td><td><strong>{{ $plan->class?->name ?? '—' }}</strong></td>
    <td>Subject</td><td><strong>{{ $plan->subject?->name ?? '—' }}</strong></td>
  </tr>
  <tr>
    <td>Lesson Topic</td><td colspan="3"><strong>{{ $plan->topic }}</strong></td>
  </tr>
  <tr>
    <td>Scheduled Date</td><td>{{ $plan->plan_date ? $plan->plan_date->format('d M Y') : '—' }}</td>
    <td>Period Slot</td><td>{{ $plan->period_number ? 'Period ' . $plan->period_number : '—' }}</td>
  </tr>
  <tr>
    <td>Allocated Duration</td><td>{{ $plan->duration_minutes ? $plan->duration_minutes . ' mins' : '—' }}</td>
    <td>Teaching Methodology</td><td>{{ $plan->teaching_method ?? 'Direct Instruction / Interactive' }}</td>
  </tr>
  <tr>
    <td>Prepared By (Faculty)</td><td>{{ $plan->createdBy?->name ?? 'Faculty' }}</td>
    <td>Approval Status</td>
    <td>
      @php $st = $plan->status ?? 'draft'; @endphp
      <span class="badge {{ match($st){ 'approved'=>'badge-green','rejected'=>'badge-red','submitted'=>'badge-blue',default=>'badge-amber' } }}">
        {{ strtoupper($st) }}
      </span>
      @if($plan->is_completed) <span class="badge badge-green" style="margin-left: 3px;">COMPLETED</span> @endif
    </td>
  </tr>
  @if($plan->syllabus)
  <tr>
    <td>Curriculum Unit</td>
    <td colspan="3">{{ $plan->syllabus->chapter ?? '' }} &bull; {{ $plan->syllabus->topic ?? $plan->syllabus->name ?? '' }}</td>
  </tr>
  @endif
</table>

@if($plan->learning_objectives)
<div class="section">
  <div class="section-title">Key Pedagogical &amp; Learning Objectives</div>
  <div class="content-box">{{ $plan->learning_objectives }}</div>
</div>
@endif

@if($plan->resources_required)
<div class="section">
  <div class="section-title">Teaching Aids, Lab Equipment &amp; Resources</div>
  <div class="content-box">{{ $plan->resources_required }}</div>
</div>
@endif

@if($plan->teacher_notes)
<div class="section">
  <div class="section-title">Instructional Plan &amp; Classroom Delivery Notes</div>
  <div class="content-box">{{ $plan->teacher_notes }}</div>
</div>
@endif

@if($plan->hod_decision)
<div class="section">
  <div class="section-title">Academic Head / HOD Review Verification</div>
  <table class="meta-table" style="margin-bottom: 0;">
    <tr>
      <td>Review Verdict</td><td><span class="badge {{ $plan->hod_decision === 'approved' ? 'badge-green' : 'badge-red' }}">{{ strtoupper($plan->hod_decision) }}</span></td>
      <td>Evaluator</td><td>{{ $plan->hodReviewedBy?->name ?? 'Head of Department' }}</td>
    </tr>
    @if($plan->hod_remarks)
      <tr><td>Academic Remarks</td><td colspan="3">{{ $plan->hod_remarks }}</td></tr>
    @endif
    <tr><td>Date Reviewed</td><td colspan="3">{{ $plan->hod_reviewed_at ? $plan->hod_reviewed_at->format('d M Y h:i A') : '—' }}</td></tr>
  </table>
</div>
@endif

<table style="width: 100%; border-collapse: collapse; border: none; margin-top: 30px; page-break-inside: avoid;">
  <tr>
    <td style="width: 33%; text-align: left; vertical-align: bottom; border: none;">
      <div style="border-top: 1px dashed #64748b; width: 120px; margin-bottom: 3px;"></div>
      <span style="font-size: 8px; font-weight: bold; color: #334155;">Subject Teacher</span>
    </td>
    <td style="width: 34%; text-align: center; vertical-align: bottom; border: none;">
      <div style="border-top: 1px dashed #64748b; width: 120px; margin: 0 auto 3px auto;"></div>
      <span style="font-size: 8px; font-weight: bold; color: #334155;">HOD / Academic Head</span>
    </td>
    <td style="width: 33%; text-align: right; vertical-align: bottom; border: none;">
      <div style="border-top: 1px dashed #64748b; width: 120px; margin-left: auto; margin-bottom: 3px;"></div>
      <span style="font-size: 8px; font-weight: bold; color: #334155;">Principal</span>
    </td>
  </tr>
</table>

@include('pdf.partials.footer')
</body>
</html>
