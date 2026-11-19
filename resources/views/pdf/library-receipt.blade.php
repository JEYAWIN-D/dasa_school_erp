<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Library Circulation Slip</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 10px;
    color: #1e293b;
    background: #fff;
    padding: 10px 14px;
  }
  .wrap { max-width: 320px; margin: 0 auto; }
  .center { text-align: center; }
  .school-name { font-size: 13px; font-weight: bold; color: #8C2826; text-transform: uppercase; }
  .school-sub { font-size: 8px; color: #64748b; margin-top: 2px; line-height: 1.2; }
  .divider { border-top: 1px dashed #94a3b8; margin: 8px 0; }
  .title { font-size: 11px; font-weight: bold; text-align: center; margin: 4px 0; text-transform: uppercase; letter-spacing: 0.5px; }
  table.slip-table { width: 100%; border-collapse: collapse; margin-top: 4px; }
  table.slip-table td { padding: 2.5px 0; font-size: 9.5px; vertical-align: top; }
  table.slip-table td.label { color: #64748b; width: 40%; }
  table.slip-table td.value { font-weight: bold; text-align: right; color: #0f172a; }
  .book-title { font-size: 11px; font-weight: bold; color: #0f172a; margin: 4px 0 2px 0; text-align: center; }
  .status-issued { background: #dbeafe; color: #1d4ed8; padding: 2px 8px; border-radius: 4px; display: inline-block; font-size: 9px; font-weight: bold; }
  .status-returned { background: #dcfce7; color: #166534; padding: 2px 8px; border-radius: 4px; display: inline-block; font-size: 9px; font-weight: bold; }
  .fine-box { background: #fef9c3; border: 1px solid #fde68a; padding: 5px 8px; border-radius: 4px; margin: 6px 0; text-align: center; font-size: 9.5px; font-weight: bold; color: #854d0e; }
  .footer { font-size: 8px; color: #94a3b8; text-align: center; margin-top: 8px; }
</style>
</head>
<body>
@php
    $schoolSetting = $school ?? \App\Models\SchoolSetting::instance() ?? \App\Models\SchoolSetting::first();
    $logoBase64 = null;
    if ($schoolSetting && method_exists($schoolSetting, 'getLogoBase64')) {
        $logoBase64 = $schoolSetting->getLogoBase64(1);
    }
    if (!$logoBase64 && file_exists(public_path('images/school-logo.png'))) {
        $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/school-logo.png')));
    }
    $isReturn = $issue->return_date !== null;
    $member = $issue->student ?? $issue->employee;
    $memberName = $issue->student ? ($issue->student->first_name . ' ' . $issue->student->last_name) : ($issue->employee->name ?? 'Staff');
    $admNo = $issue->student ? ($issue->student->admission_no ?? $issue->student->admission_number ?? '—') : ($issue->employee?->employee_code ?? '—');
@endphp

<div class="wrap">
  <div class="center">
    @if($logoBase64)
      <img src="{{ $logoBase64 }}" alt="Logo" style="max-height: 42px; display: inline-block; margin-bottom: 3px;">
    @endif
    <div class="school-name">{{ $schoolSetting?->school_name ?? config('app.name', 'Erode Public School') }}</div>
    @if($schoolSetting?->address)
      <div class="school-sub">{{ $schoolSetting->address }}, {{ $schoolSetting->city }}</div>
    @endif
    @if($schoolSetting?->affiliation_no)
      <div class="school-sub">Affiliation No: {{ $schoolSetting->affiliation_no }}</div>
    @endif
    <div class="school-sub" style="font-weight: bold; color: #475569; margin-top: 2px;">LIBRARY MANAGEMENT DIVISION</div>
  </div>

  <div class="divider"></div>

  <div class="title">{{ $isReturn ? 'Book Return Receipt' : 'Book Issue Receipt' }}</div>

  <div class="book-title">{{ $issue->book->title }}</div>
  @if($issue->book->author)
    <div class="center" style="font-size: 8.5px; color: #64748b; margin-bottom: 4px;">by {{ $issue->book->author }}</div>
  @endif

  <div class="divider"></div>

  <table class="slip-table">
    <tr>
      <td class="label">Receipt No.</td>
      <td class="value">{{ $isReturn ? ($issue->return_receipt_no ?? '#' . $issue->id) : ($issue->issue_receipt_no ?? '#' . $issue->id) }}</td>
    </tr>
    @if($issue->copy)
      <tr>
        <td class="label">Accession No.</td>
        <td class="value">{{ $issue->copy->accession_number }}</td>
      </tr>
      <tr>
        <td class="label">Barcode</td>
        <td class="value" style="font-family: monospace;">{{ $issue->copy->barcode }}</td>
      </tr>
    @endif
    <tr>
      <td class="label">Borrower</td>
      <td class="value">{{ $memberName }}</td>
    </tr>
    <tr>
      <td class="label">{{ $issue->student ? 'Adm. No.' : 'Staff ID' }}</td>
      <td class="value">{{ $admNo }}</td>
    </tr>
    <tr>
      <td class="label">Issue Date</td>
      <td class="value">{{ $issue->issue_date->format('d M Y') }}</td>
    </tr>
    <tr>
      <td class="label">Due Date</td>
      <td class="value">{{ $issue->due_date->format('d M Y') }}</td>
    </tr>
    @if($isReturn)
      <tr>
        <td class="label">Return Date</td>
        <td class="value">{{ $issue->return_date->format('d M Y') }}</td>
      </tr>
    @endif
    <tr>
      <td class="label">Status</td>
      <td class="value">
        <span class="{{ $isReturn ? 'status-returned' : 'status-issued' }}">{{ $isReturn ? 'Returned' : 'Issued' }}</span>
      </td>
    </tr>
  </table>

  @if($issue->fine_amount > 0)
    <div class="fine-box">
      Overdue Fine: ₹{{ number_format($issue->fine_amount, 2) }}
      <span style="font-size: 8px;">({{ $issue->fine_paid ? 'PAID' : 'PENDING' }})</span>
    </div>
  @endif

  <div class="divider"></div>

  <div class="footer">
    Issued By: <strong>{{ $issue->issuedBy?->name ?? 'Librarian' }}</strong><br>
    <em>Please keep this receipt for library records.</em>
  </div>
</div>
</body>
</html>
