@props([
    'text' => null,
    'enabled' => null,
    'school' => null,
])

@php
    $schoolSetting = $school ?? \App\Models\SchoolSetting::instance() ?? \App\Models\SchoolSetting::first();
    $isWatermarkEnabled = $enabled ?? ($schoolSetting?->print_watermark_enabled ?? true);
    $watermarkText = $text ?? ($schoolSetting?->print_watermark_text ?: ($schoolSetting?->school_name ?? 'ERODE PUBLIC SCHOOL'));
@endphp

@if($isWatermarkEnabled && !empty($watermarkText))
  <div class="school-print-watermark pointer-events-none select-none" style="
      position: fixed;
      top: 35%;
      left: 5%;
      width: 90%;
      text-align: center;
      font-size: 58px;
      font-weight: 900;
      letter-spacing: 5px;
      text-transform: uppercase;
      color: #0f172a;
      opacity: 0.04;
      transform: rotate(-30deg);
      -webkit-transform: rotate(-30deg);
      z-index: 0;
      line-height: 1.2;
      font-family: 'Plus Jakarta Sans', Arial, sans-serif;
  ">
    {{ $watermarkText }}
  </div>
@endif

