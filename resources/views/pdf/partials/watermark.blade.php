@php
    $schoolSetting = $school ?? \App\Models\SchoolSetting::instance() ?? \App\Models\SchoolSetting::first();
    $enabled = $watermarkEnabled ?? ($schoolSetting?->print_watermark_enabled ?? true);
    $text = $watermarkText ?? ($schoolSetting?->print_watermark_text ?: ($schoolSetting?->school_name ?? 'ERODE PUBLIC SCHOOL'));
@endphp

@if($enabled && !empty($text))
  <div class="school-pdf-watermark" style="position: fixed; top: 34%; left: 5%; width: 90%; text-align: center; font-family: Arial, Helvetica, sans-serif; font-size: 52px; font-weight: 800; color: #0f172a; opacity: 0.05; transform: rotate(-30deg); -webkit-transform: rotate(-30deg); z-index: -1000; text-transform: uppercase; letter-spacing: 4px; pointer-events: none; line-height: 1.2;">
    {{ $text }}
  </div>
@endif

