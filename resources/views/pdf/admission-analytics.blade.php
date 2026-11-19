<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Admission Analytics Report</title>
<style>
  @page {
    size: A4 portrait;
    margin: 12mm 15mm 16mm 15mm;
  }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 9.5px;
    color: #1e293b;
    line-height: 1.4;
  }
  .section { margin-bottom: 12px; }
  .section-title {
    font-size: 9.5px;
    font-weight: bold;
    background: #f1f5f9;
    padding: 4px 8px;
    border-left: 3px solid #1e293b;
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  table.data-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 8px;
  }
  table.data-table th {
    background: #1e293b;
    color: white;
    font-size: 8.5px;
    padding: 5px 6px;
    text-align: left;
    text-transform: uppercase;
  }
  table.data-table td {
    border: 1px solid #e2e8f0;
    padding: 4px 6px;
    font-size: 9px;
  }
  table.data-table tr:nth-child(even) td { background: #f8fafc; }
  table.two-col { width: 100%; border-collapse: collapse; }
  table.two-col td { vertical-align: top; border: none; padding: 0; }
  .badge-up { color: #166534; font-weight: bold; }
  .badge-down { color: #991b1b; font-weight: bold; }
  .yoy-box {
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 8px 12px;
    text-align: center;
    background: #f8fafc;
  }
  .yoy-year { font-size: 9px; color: #64748b; font-weight: bold; }
  .yoy-val { font-size: 18px; font-weight: bold; color: #0f172a; margin: 2px 0; }
  .yoy-label { font-size: 8px; color: #64748b; text-transform: uppercase; }
</style>
</head>
<body>

@include('pdf.partials.watermark')

@include('pdf.partials.header', [
    'title' => 'ADMISSION ANALYTICS REPORT',
    'subtitle' => 'Institutional Enrollment & Funnel Conversion Analytics',
    'academicYear' => $currentYear?->name,
    'date' => now()->format('d M Y')
])

{{-- Year-over-Year --}}
@if($yoyData)
<div class="section">
  <div class="section-title">Year-over-Year Comparative Overview</div>
  <table style="width:100%; border-collapse:collapse; margin-bottom:10px;">
    <tr>
      <td style="width:48%; border:none; padding:0;">
        <div class="yoy-box">
          <div class="yoy-year">{{ $yoyData['prev']['year'] }}</div>
          <div class="yoy-val">{{ number_format($yoyData['prev']['enquiries']) }}</div>
          <div class="yoy-label">Total Enquiries</div>
          <div style="margin-top:4px; font-size:9.5px; font-weight:bold; color:#475569;">{{ number_format($yoyData['prev']['converted']) }} Converted</div>
        </div>
      </td>
      <td style="width:4%; border:none;"></td>
      <td style="width:48%; border:none; padding:0;">
        <div class="yoy-box">
          <div class="yoy-year">{{ $yoyData['current']['year'] }}</div>
          <div class="yoy-val">{{ number_format($yoyData['current']['enquiries']) }}</div>
          <div class="yoy-label">Total Enquiries</div>
          <div style="margin-top:4px; font-size:9.5px; font-weight:bold; color:#475569;">{{ number_format($yoyData['current']['converted']) }} Converted</div>
        </div>
      </td>
    </tr>
  </table>
</div>
@endif

{{-- Funnel and Source Breakdown --}}
<table class="two-col" style="margin-bottom:12px;">
  <tr>
    <td style="width:48%;">
      <div class="section-title">Admission Funnel Stage</div>
      <table class="data-table">
        <thead><tr><th>Stage</th><th style="text-align:right;">Count</th><th style="text-align:right;">Ratio</th></tr></thead>
        <tbody>
          @php $totFunnel = $funnelStages->sum(); @endphp
          @foreach($funnelStages as $stg => $cnt)
          <tr>
            <td>{{ ucfirst(str_replace('_',' ',$stg)) }}</td>
            <td style="text-align:right; font-weight:bold;">{{ $cnt }}</td>
            <td style="text-align:right; color:#64748b;">{{ $totFunnel > 0 ? round($cnt / $totFunnel * 100, 1) : 0 }}%</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </td>
    <td style="width:4%;"></td>
    <td style="width:48%;">
      <div class="section-title">Lead Source Breakdown</div>
      <table class="data-table">
        <thead><tr><th>Channel</th><th style="text-align:right;">Total</th><th style="text-align:right;">Conv.</th></tr></thead>
        <tbody>
          @foreach($bySource as $src)
          <tr>
            <td>{{ ucfirst($src->source ?? 'Direct / Walk-in') }}</td>
            <td style="text-align:right;">{{ $src->total }}</td>
            <td style="text-align:right; font-weight:bold;">{{ $src->converted }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </td>
  </tr>
</table>

{{-- Class-wise Breakdown --}}
<div class="section">
  <div class="section-title">Class-Wise Applications & Admissions</div>
  <table class="data-table">
    <thead>
      <tr>
        <th>Grade / Class</th>
        <th style="text-align:right;">Total Applications</th>
        <th style="text-align:right;">Confirmed Admissions</th>
        <th style="text-align:right;">Conversion Rate</th>
      </tr>
    </thead>
    <tbody>
      @forelse($byClass as $cls)
      <tr>
        <td style="font-weight:600;">{{ $cls->class_name ?? '—' }}</td>
        <td style="text-align:right;">{{ $cls->total }}</td>
        <td style="text-align:right; font-weight:bold;">{{ $cls->converted }}</td>
        <td style="text-align:right;">
          @php $crate = $cls->total > 0 ? round($cls->converted / $cls->total * 100, 1) : 0; @endphp
          <span class="{{ $crate >= 50 ? 'badge-up' : 'badge-down' }}">{{ $crate }}%</span>
        </td>
      </tr>
      @empty
      <tr><td colspan="4" style="text-align:center; color:#94a3b8;">No class-wise records found.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

{{-- Monthly Trend --}}
<div class="section">
  <div class="section-title">Monthly Enquiry Trend ({{ now()->year }})</div>
  @php $months = ['','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']; @endphp
  <table class="data-table">
    <thead><tr>
      @for($i=1;$i<=12;$i++)<th style="text-align:center;">{{ $months[$i] }}</th>@endfor
    </tr></thead>
    <tbody><tr>
      @for($i=1;$i<=12;$i++)
        <td style="text-align:center; font-weight:bold;">{{ $monthlyTrend->get($i, 0) }}</td>
      @endfor
    </tr></tbody>
  </table>
</div>

{{-- Counsellor Conversion --}}
@if($byCounsellor->isNotEmpty())
<div class="section">
  <div class="section-title">Counsellor Performance & Conversion</div>
  <table class="data-table">
    <thead><tr><th>Counsellor / Officer</th><th style="text-align:right;">Total Enquiries</th><th style="text-align:right;">Converted</th><th style="text-align:right;">Conversion Rate</th></tr></thead>
    <tbody>
      @foreach($byCounsellor->sortByDesc('total') as $row)
      <tr>
        <td>{{ $row->assignedTo?->name ?? ('Staff #'.$row->assigned_to) }}</td>
        <td style="text-align:right;">{{ $row->total }}</td>
        <td style="text-align:right; font-weight:bold;">{{ $row->converted }}</td>
        <td style="text-align:right;">
          @php $rate = $row->total > 0 ? round($row->converted/$row->total*100,1) : 0; @endphp
          <span class="{{ $rate >= 50 ? 'badge-up' : 'badge-down' }}">{{ $rate }}%</span>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endif

@include('pdf.partials.signatures', ['mode' => 'standard'])

@include('pdf.partials.footer', ['computerGenerated' => true])

</body>
</html>
