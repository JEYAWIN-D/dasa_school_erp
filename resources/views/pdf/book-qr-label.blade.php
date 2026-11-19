@php
    $schoolSetting = $school ?? \App\Models\SchoolSetting::instance() ?? \App\Models\SchoolSetting::first();
    $schoolName = $schoolSetting?->school_name ?? config('app.name', 'School Library');
@endphp
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Book Label - {{ $book->accession_number ?? $book->id }}</title>
<style>
  @page {
    size: 60mm 35mm;
    margin: 2mm;
  }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body {
    font-family: Arial, Helvetica, sans-serif;
    background: #ffffff;
    font-size: 8px;
    color: #1e293b;
  }
  table.label-card {
    width: 100%;
    border-collapse: collapse;
    border: 1px solid #94a3b8;
    border-radius: 4px;
    height: 31mm;
  }
  table.label-card td {
    vertical-align: middle;
    padding: 3px;
  }
  .school-tag {
    font-size: 6.5px;
    font-weight: bold;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    margin-bottom: 2px;
  }
  .accession-no {
    font-size: 11px;
    font-weight: bold;
    color: #0f172a;
    letter-spacing: 0.5px;
  }
  .book-title {
    font-size: 7.5px;
    font-weight: bold;
    color: #334155;
    line-height: 1.2;
    margin-top: 2px;
  }
  .book-author {
    font-size: 6.5px;
    color: #64748b;
    margin-top: 1px;
  }
  .isbn-tag {
    font-size: 6px;
    color: #94a3b8;
    margin-top: 1px;
  }
</style>
</head>
<body>
<table class="label-card">
  <tr>
    <td style="width: 32mm; text-align: center; border-right: 1px dashed #cbd5e1;">
      <div style="display: inline-block;">
        {!! $qr !!}
      </div>
    </td>
    <td style="padding-left: 5px;">
      <div class="school-tag">{{ \Illuminate\Support\Str::limit($schoolName, 26) }}</div>
      <div class="accession-no">{{ $book->accession_number ?? ('ACC-'.$book->id) }}</div>
      <div class="book-title">{{ \Illuminate\Support\Str::limit($book->title, 35) }}</div>
      @if($book->author)
        <div class="book-author">by {{ \Illuminate\Support\Str::limit($book->author, 22) }}</div>
      @endif
      @if($book->isbn)
        <div class="isbn-tag">ISBN: {{ $book->isbn }}</div>
      @endif
    </td>
  </tr>
</table>
</body>
</html>
