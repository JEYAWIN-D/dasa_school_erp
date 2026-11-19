@php
    $schoolSetting = $school ?? \App\Models\SchoolSetting::instance() ?? \App\Models\SchoolSetting::first();
    
    // Resolve primary logo (Base64 for rock-solid DomPDF rendering)
    $logoBase64 = null;
    if ($schoolSetting && method_exists($schoolSetting, 'getLogoBase64')) {
        $logoBase64 = $schoolSetting->getLogoBase64(1);
    }
    if (!$logoBase64 && file_exists(public_path('images/school-logo.png'))) {
        $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/school-logo.png')));
    }

    // Resolve optional secondary logo / crest
    $crestBase64 = null;
    if ($schoolSetting && method_exists($schoolSetting, 'getLogoBase64') && $schoolSetting->hasLogo(2)) {
        $crestBase64 = $schoolSetting->getLogoBase64(2);
    } elseif (file_exists(public_path('images/school-crest.png'))) {
        $crestBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/school-crest.png')));
    }

    $primaryColor = $schoolSetting?->primary_color ?? '#8C2826';
    $schoolName = $schoolSetting?->school_name ?? config('app.name', 'Erode Public School');
    
    // Address components
    $addressParts = array_filter([
        $schoolSetting?->address,
        $schoolSetting?->city,
        $schoolSetting?->district && ($schoolSetting?->district !== $schoolSetting?->city) ? $schoolSetting?->district : null,
        $schoolSetting?->state,
        $schoolSetting?->pincode ? 'PIN: ' . $schoolSetting?->pincode : null,
    ]);
    $addressLine = implode(', ', $addressParts);

    // Affiliation & credentials
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

    // Contact line
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

    $docTitle = $title ?? $documentTitle ?? null;
    $docSubtitle = $subtitle ?? $documentSubtitle ?? null;
    $academicYear = $academicYear ?? $schoolSetting?->academic_year ?? null;
@endphp

<div class="school-letterhead" style="margin-bottom: 12px; width: 100%;">
  <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
    <tr>
      {{-- Left Logo Column --}}
      <td style="width: 80px; vertical-align: middle; text-align: left; padding: 0 8px 0 0; border: none;">
        @if($logoBase64)
          <img src="{{ $logoBase64 }}" alt="School Logo" style="max-height: 72px; max-width: 78px; height: auto; width: auto; display: block;">
        @endif
      </td>

      {{-- Center Institutional Details Column --}}
      <td style="vertical-align: middle; text-align: center; padding: 0 4px; border: none;">
        <div style="font-family: Arial, Helvetica, sans-serif; font-size: 17px; font-weight: bold; color: {{ $primaryColor }}; text-transform: uppercase; letter-spacing: 0.6px; line-height: 1.2; margin: 0 0 2px 0;">
          {{ $schoolName }}
        </div>

        @if($affLine)
          <div style="font-family: Arial, Helvetica, sans-serif; font-size: 8.5px; font-weight: bold; color: #334155; margin: 0 0 2px 0; line-height: 1.3;">
            {{ $affLine }}
          </div>
        @endif

        @if($addressLine)
          <div style="font-family: Arial, Helvetica, sans-serif; font-size: 8px; color: #475569; margin: 0 0 2px 0; line-height: 1.3;">
            {{ $addressLine }}
          </div>
        @endif

        @if($contactLine)
          <div style="font-family: Arial, Helvetica, sans-serif; font-size: 8px; color: #64748b; margin: 0; line-height: 1.2;">
            {{ $contactLine }}
          </div>
        @endif
      </td>

      {{-- Right Crest / Balance Column --}}
      <td style="width: 80px; vertical-align: middle; text-align: right; padding: 0 0 0 8px; border: none;">
        @if($crestBase64)
          <img src="{{ $crestBase64 }}" alt="Emblem" style="max-height: 70px; max-width: 75px; height: auto; width: auto; display: inline-block;">
        @endif
      </td>
    </tr>
  </table>

  {{-- Dual Professional Divider Rule --}}
  <div style="margin-top: 6px; border-bottom: 2px solid {{ $primaryColor }}; width: 100%;"></div>
  <div style="margin-top: 1.5px; border-bottom: 0.5px solid #cbd5e1; width: 100%;"></div>

  {{-- Optional Document Title Header Bar --}}
  @if($docTitle)
    <div style="margin-top: 7px; text-align: center;">
      <span style="display: inline-block; font-family: Arial, Helvetica, sans-serif; font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.8px; color: #0f172a; padding: 2px 14px; border: 1px solid #94a3b8; background-color: #f8fafc; border-radius: 3px;">
        {{ $docTitle }}
      </span>
      @if($docSubtitle)
        <div style="font-family: Arial, Helvetica, sans-serif; font-size: 8.5px; color: #64748b; margin-top: 2px; font-weight: normal;">
          {{ $docSubtitle }}
          @if($academicYear)
            &nbsp;|&nbsp; Academic Year: <strong>{{ $academicYear }}</strong>
          @endif
        </div>
      @elseif($academicYear)
        <div style="font-family: Arial, Helvetica, sans-serif; font-size: 8.5px; color: #64748b; margin-top: 2px;">
          Academic Year: <strong>{{ $academicYear }}</strong>
        </div>
      @endif
    </div>
  @endif
</div>

