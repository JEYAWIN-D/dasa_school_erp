@props([
    'title' => null,
    'subtitle' => null,
    'academicYear' => null,
    'school' => null,
])

@php
    $schoolSetting = $school ?? \App\Models\SchoolSetting::instance() ?? \App\Models\SchoolSetting::first();
    $logoUrl = null;
    if ($schoolSetting && method_exists($schoolSetting, 'getLogoUrl')) {
        $logoUrl = $schoolSetting->getLogoUrl(1);
    }
    if (!$logoUrl) {
        $logoUrl = asset('images/school-logo.png');
    }

    $crestUrl = null;
    if ($schoolSetting && method_exists($schoolSetting, 'getLogoUrl') && $schoolSetting->hasLogo(2)) {
        $crestUrl = $schoolSetting->getLogoUrl(2);
    } elseif (file_exists(public_path('images/school-crest.png'))) {
        $crestUrl = asset('images/school-crest.png');
    }

    $primaryColor = $schoolSetting?->primary_color ?? '#8C2826';
    $schoolName = $schoolSetting?->school_name ?? config('app.name', 'Erode Public School');

    // Postal Address
    $addressParts = array_filter([
        $schoolSetting?->address,
        $schoolSetting?->city,
        $schoolSetting?->district && ($schoolSetting?->district !== $schoolSetting?->city) ? $schoolSetting?->district : null,
        $schoolSetting?->state,
        $schoolSetting?->pincode ? 'PIN: ' . $schoolSetting?->pincode : null,
    ]);
    $addressLine = implode(', ', $addressParts);

    // Affiliation Line
    $affParts = [];
    if (!empty($schoolSetting?->board)) {
        $affParts[] = 'Affiliated to ' . $schoolSetting->board;
    }
    if (!empty($schoolSetting?->affiliation_no)) {
        $affParts[] = 'Affiliation No: ' . $schoolSetting->affiliation_no;
    }
    if (!empty($schoolSetting?->school_code)) {
        $affParts[] = 'School Code: ' . $schoolSetting->school_code;
    }
    if (!empty($schoolSetting?->udise_no)) {
        $affParts[] = 'UDISE: ' . $schoolSetting->udise_no;
    }
    if (!empty($schoolSetting?->recognition_no)) {
        $affParts[] = 'Recognition: ' . $schoolSetting->recognition_no;
    }
    $affLine = implode(' | ', $affParts);

    // Contact Line
    $contactParts = [];
    if (!empty($schoolSetting?->phone)) {
        $contactParts[] = 'Tel: ' . $schoolSetting->phone;
    }
    if (!empty($schoolSetting?->email)) {
        $contactParts[] = 'Email: ' . $schoolSetting->email;
    }
    if (!empty($schoolSetting?->website)) {
        $contactParts[] = 'Web: ' . $schoolSetting->website;
    }
    $contactLine = implode(' | ', $contactParts);
@endphp

<div class="school-print-header mb-3 select-none" style="-webkit-print-color-adjust: exact; print-color-adjust: exact;">
  <div class="flex items-center justify-between gap-3 pb-2">
    {{-- Left Logo --}}
    <div class="w-16 shrink-0 flex items-center justify-start">
      @if($logoUrl)
        <img src="{{ $logoUrl }}" alt="School Logo" class="max-h-14 max-w-16 object-contain drop-shadow-2xs">
      @endif
    </div>

    {{-- Center School Identity --}}
    <div class="flex-1 text-center px-2">
      <h1 class="text-xl sm:text-2xl font-black tracking-tight uppercase leading-tight" style="color: {{ $primaryColor }}; font-family: 'Plus Jakarta Sans', Arial, sans-serif;">
        {{ $schoolName }}
      </h1>

      @if($affLine)
        <p class="text-[11px] font-bold text-slate-700 mt-1 leading-snug">
          {{ $affLine }}
        </p>
      @endif

      @if($addressLine)
        <p class="text-[10.5px] text-slate-600 mt-0.5 leading-snug">
          {{ $addressLine }}
        </p>
      @endif

      @if($contactLine)
        <p class="text-[10px] text-slate-500 mt-0.5">
          {{ $contactLine }}
        </p>
      @endif
    </div>

    {{-- Right Crest / Emblem --}}
    <div class="w-16 shrink-0 flex items-center justify-end">
      @if($crestUrl)
        <img src="{{ $crestUrl }}" alt="Emblem" class="max-h-14 max-w-16 object-contain opacity-95">
      @endif
    </div>
  </div>

  {{-- Dual Professional Divider --}}
  <div class="w-full border-b-2" style="border-color: {{ $primaryColor }};"></div>
  <div class="w-full border-b border-slate-300 mt-0.5"></div>

  {{-- Document Title Badge --}}
  @if($title)
    <div class="mt-3 text-center">
      <span class="inline-block px-4 py-1 text-xs font-extrabold uppercase tracking-widest text-slate-900 bg-slate-100 border border-slate-300 rounded shadow-2xs">
        {{ $title }}
      </span>
      @if($subtitle || $academicYear)
        <p class="text-[11px] text-slate-500 mt-1 font-medium">
          @if($subtitle) {{ $subtitle }} @endif
          @if($subtitle && $academicYear) &nbsp;|&nbsp; @endif
          @if($academicYear) Academic Year: <strong class="text-slate-700">{{ $academicYear }}</strong> @endif
        </p>
      @endif
    </div>
  @endif
</div>

