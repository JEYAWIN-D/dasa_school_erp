@props([
    'type' => 'standard', // 'standard', 'financial', 'academic', 'certificate'
    'school' => null,
])

@php
    $schoolSetting = $school ?? \App\Models\SchoolSetting::instance() ?? \App\Models\SchoolSetting::first();
    $sigUrl = null;
    if (!empty($schoolSetting?->principal_signature) && \Illuminate\Support\Facades\Storage::disk('public')->exists($schoolSetting->principal_signature)) {
        $sigUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($schoolSetting->principal_signature);
    }
    $stampUrl = null;
    if ($schoolSetting && method_exists($schoolSetting, 'getSealUrl')) {
        $stampUrl = $schoolSetting->getSealUrl(1);
    }
@endphp

<div class="school-print-signatures mt-12 pt-4 select-none break-inside-avoid" style="-webkit-print-color-adjust: exact; print-color-adjust: exact;">
  <div class="grid grid-cols-3 gap-6 text-center items-end">
    @if($type === 'financial')
      <div class="text-left">
        <div class="border-t border-dashed border-slate-400 w-36 mb-1.5"></div>
        <span class="text-xs font-bold text-slate-800">Cashier / Accountant</span>
      </div>
      <div class="text-center">
        <div class="border-t border-dashed border-slate-400 w-36 mx-auto mb-1.5"></div>
        <span class="text-xs font-bold text-slate-800">Audited / Verified By</span>
      </div>
      <div class="text-right">
        @if($sigUrl)
          <img src="{{ $sigUrl }}" alt="Signature" class="h-10 inline-block mb-1">
        @endif
        <div class="border-t border-dashed border-slate-400 w-36 ml-auto mb-1.5"></div>
        <span class="text-xs font-bold text-slate-800">Principal / Director</span>
      </div>

    @elseif($type === 'academic')
      <div class="text-left">
        <div class="border-t border-dashed border-slate-400 w-36 mb-1.5"></div>
        <span class="text-xs font-bold text-slate-800">Class Teacher</span>
      </div>
      <div class="text-center">
        <div class="border-t border-dashed border-slate-400 w-36 mx-auto mb-1.5"></div>
        <span class="text-xs font-bold text-slate-800">Exam In-Charge</span>
      </div>
      <div class="text-right">
        @if($sigUrl)
          <img src="{{ $sigUrl }}" alt="Signature" class="h-10 inline-block mb-1">
        @endif
        <div class="border-t border-dashed border-slate-400 w-36 ml-auto mb-1.5"></div>
        <span class="text-xs font-bold text-slate-800">Principal</span>
      </div>

    @elseif($type === 'certificate')
      <div class="text-left">
        <div class="border-t border-dashed border-slate-400 w-32 mb-1.5"></div>
        <span class="text-xs font-bold text-slate-800">Prepared By</span>
      </div>
      <div class="text-center">
        @if($stampUrl)
          <img src="{{ $stampUrl }}" alt="Seal" class="h-12 inline-block mb-1 opacity-85">
        @else
          <div class="h-10 border border-dashed border-slate-300 w-28 mx-auto mb-1 flex items-center justify-center text-[10px] text-slate-400 uppercase tracking-wider font-semibold">
            Office Seal
          </div>
        @endif
        <div class="text-xs font-bold text-slate-800">Institutional Seal</div>
      </div>
      <div class="text-right">
        @if($sigUrl)
          <img src="{{ $sigUrl }}" alt="Signature" class="h-10 inline-block mb-1">
        @endif
        <div class="border-t border-dashed border-slate-400 w-32 ml-auto mb-1.5"></div>
        <span class="text-xs font-bold text-slate-800">Principal</span>
      </div>

    @else
      <div class="text-left">
        <div class="border-t border-dashed border-slate-400 w-36 mb-1.5"></div>
        <span class="text-xs font-bold text-slate-800">Prepared By</span>
      </div>
      <div class="text-center">
        <div class="border-t border-dashed border-slate-400 w-36 mx-auto mb-1.5"></div>
        <span class="text-xs font-bold text-slate-800">Verified By</span>
      </div>
      <div class="text-right">
        @if($sigUrl)
          <img src="{{ $sigUrl }}" alt="Signature" class="h-10 inline-block mb-1">
        @endif
        <div class="border-t border-dashed border-slate-400 w-36 ml-auto mb-1.5"></div>
        <span class="text-xs font-bold text-slate-800">Principal</span>
      </div>
    @endif
  </div>
</div>

