<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>{{ $request->paper_title }}</title>
<style>
  @page {
    size: A4 portrait;
    margin: 12mm 15mm 16mm 15mm;
  }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body {
    font-family: Arial, sans-serif;
    font-size: 10px;
    color: #1e293b;
    line-height: 1.5;
  }
  table.candidate-strip {
    width: 100%;
    border-collapse: collapse;
    margin: 6px 0 10px 0;
    border: 1px solid #cbd5e1;
    background: #f8fafc;
  }
  table.candidate-strip td {
    padding: 5px 8px;
    font-size: 9px;
  }
  .instructions {
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 6px 10px;
    background: #fffbeb;
    margin-bottom: 12px;
    font-size: 9px;
    color: #92400e;
  }
  .section-header {
    background: #1e293b;
    color: white;
    padding: 4px 8px;
    font-weight: bold;
    font-size: 9.5px;
    margin: 12px 0 8px;
    border-radius: 2px;
    text-transform: uppercase;
  }
  .question {
    margin-bottom: 10px;
    page-break-inside: avoid;
  }
  table.q-bar {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 2px;
  }
  table.q-bar td { padding: 0; border: none; font-size: 10px; }
  .q-num { font-weight: bold; }
  .q-marks { color: #475569; font-weight: bold; font-size: 9px; }
  .q-text { font-size: 10px; color: #0f172a; line-height: 1.4; }
  .options { margin-top: 4px; padding-left: 12px; }
  .option { font-size: 9.5px; color: #334155; margin: 2px 0; }
  .answer-space { border-bottom: 1px dashed #cbd5e1; height: 16px; margin: 3px 0; }
</style>
</head>
<body>
@include('pdf.partials.watermark')

@include('pdf.partials.header', [
  'title' => $request->paper_title,
  'subtitle' => ($exam ? $exam->name . ' | ' : '') . 'Total Marks: ' . $totalMarks . ' | Allocated Duration: ' . $duration . ' Minutes'
])

<table class="candidate-strip">
  <tr>
    <td style="width: 50%;">Candidate Name: ________________________________</td>
    <td style="width: 25%;">Grade / Class: ____________</td>
    <td style="width: 25%;">Roll No: ____________</td>
  </tr>
</table>

@if($request->instructions)
<div class="instructions">
  <strong>General Examination Instructions:</strong>
  {{ $request->instructions }}
</div>
@endif

@php
  $grouped = $questions->groupBy('question_type');
  $sectionLetters = ['A','B','C','D','E','F'];
  $typeLabels = ['mcq'=>'Multiple Choice Questions','true_false'=>'True or False','short'=>'Short Answer Questions','long'=>'Long Answer Questions','descriptive'=>'Descriptive / Essay Questions'];
  $sectionIdx = 0;
  $qNum = 1;
@endphp

@foreach($grouped as $type => $typeQuestions)
<div class="section-header">
  SECTION {{ $sectionLetters[$sectionIdx++] }}: {{ strtoupper($typeLabels[$type] ?? str_replace('_',' ',$type)) }}
  ({{ $typeQuestions->count() }} Questions)
</div>

@foreach($typeQuestions as $q)
<div class="question">
  <table class="q-bar">
    <tr>
      <td style="text-align: left;">
        <span class="q-num">Q{{ $qNum++ }}.</span>
        @if($q->chapter) <span style="font-size: 8.5px; color: #64748b; font-weight: normal;">[{{ $q->chapter }}]</span> @endif
      </td>
      <td style="text-align: right; width: 80px;" class="q-marks">
        [{{ $q->marks }} Mark{{ $q->marks != 1 ? 's' : '' }}]
      </td>
    </tr>
  </table>
  <div class="q-text">{{ $q->question_text }}</div>
  @if($type === 'mcq' && $q->options)
    @php $opts = is_array($q->options) ? $q->options : json_decode($q->options, true); @endphp
    @if(is_array($opts))
    <div class="options">
      @foreach($opts as $i => $opt)
      <div class="option">({{ chr(65+$i) }}) {{ $opt['text'] ?? '' }}</div>
      @endforeach
    </div>
    @endif
  @elseif($type === 'true_false')
    <div class="options"><span class="option">[ &nbsp; ] True &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; [ &nbsp; ] False</span></div>
  @elseif($type === 'short')
    <div class="answer-space"></div><div class="answer-space"></div>
  @elseif(in_array($type, ['long','descriptive']))
    @for($i = 0; $i < 4; $i++)
      <div class="answer-space"></div>
    @endfor
  @endif
</div>
@endforeach
@endforeach

@include('pdf.partials.footer', [
  'footerNote' => 'End of Examination Paper — ' . $request->paper_title . ' (' . ($school?->school_name ?? 'the Institution') . ')'
])
</body>
</html>
