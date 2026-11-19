@php
    $schoolSetting = $school ?? \App\Models\SchoolSetting::instance() ?? \App\Models\SchoolSetting::first();
    $schoolName = $schoolSetting?->school_name ?? config('app.name', 'School Library');
@endphp
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Bulk Book QR Labels</title>
<style>
  @page {
    size: A4 portrait;
    margin: 10mm;
  }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 8px;
    color: #1e293b;
    background: #ffffff;
  }
  table.sheet {
    width: 100%;
    border-collapse: separate;
    border-spacing: 6px;
  }
  table.sheet td {
    width: 33.33%;
    vertical-align: top;
    padding: 0;
  }
  table.label-box {
    width: 100%;
    border-collapse: collapse;
    border: 1px solid #94a3b8;
    border-radius: 4px;
    background: #ffffff;
  }
  table.label-box td {
    padding: 3px;
    vertical-align: middle;
  }
  .school-tag {
    font-size: 6px;
    font-weight: bold;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.3px;
  }
  .accession-no {
    font-size: 9.5px;
    font-weight: bold;
    color: #0f172a;
  }
  .book-title {
    font-size: 7px;
    font-weight: 600;
    color: #334155;
    line-height: 1.2;
    margin-top: 1px;
  }
  .isbn-tag {
    font-size: 5.5px;
    color: #64748b;
    margin-top: 1px;
  }
</style>
</head>
<body>

@include('pdf.partials.watermark')

@include('pdf.partials.header', [
    'title' => 'LIBRARY BOOK QR BARCODE LABELS',
    'subtitle' => 'Total Labels: ' . $booksWithQr->count() . ' physical units',
    'date' => now()->format('d M Y')
])

<table class="sheet">
  @php
    $chunks = $booksWithQr->chunk(3);
  @endphp
  @foreach($chunks as $row)
  <tr>
    @foreach($row as $item)
    <td>
      <table class="label-box">
        <tr>
          <td style="width: 24mm; text-align: center; border-right: 1px dashed #cbd5e1;">
            <div style="display:inline-block;">{!! $item['qr'] !!}</div>
          </td>
          <td style="padding-left: 4px;">
            <div class="school-tag">{{ \Illuminate\Support\Str::limit($schoolName, 22) }}</div>
            <div class="accession-no">{{ $item['qrData'] }}</div>
            <div class="book-title">{{ \Illuminate\Support\Str::limit($item['book']->title, 28) }}</div>
            @if($item['book']->author)
              <div class="isbn-tag">by {{ \Illuminate\Support\Str::limit($item['book']->author, 18) }}</div>
            @endif
            @if($item['book']->isbn)
              <div class="isbn-tag">ISBN: {{ $item['book']->isbn }}</div>
            @endif
          </td>
        </tr>
      </table>
    </td>
    @endforeach
    @for($i = count($row); $i < 3; $i++)
    <td></td>
    @endfor
  </tr>
  @endforeach
</table>

@include('pdf.partials.footer', ['computerGenerated' => true])

</body>
</html>
