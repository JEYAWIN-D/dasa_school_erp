@php
    $schoolSetting = $school ?? \App\Models\SchoolSetting::instance() ?? \App\Models\SchoolSetting::first();
    $type = $type ?? 'standard'; // 'standard', 'financial', 'academic', 'certificate'
    
    // Check for principal signature
    $sigBase64 = null;
    if (!empty($schoolSetting?->principal_signature) && \Illuminate\Support\Facades\Storage::disk('public')->exists($schoolSetting->principal_signature)) {
        $sigBase64 = 'data:image/png;base64,' . base64_encode(\Illuminate\Support\Facades\Storage::disk('public')->get($schoolSetting->principal_signature));
    }
    
    // Check for stamp/seal
    $stampBase64 = null;
    if ($schoolSetting && method_exists($schoolSetting, 'getSealBase64')) {
        $stampBase64 = $schoolSetting->getSealBase64(1);
    }
@endphp

<div class="school-pdf-signatures" style="margin-top: 35px; width: 100%; page-break-inside: avoid;">
  <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
    <tr>
      @if($type === 'financial')
        {{-- Financial: Cashier/Accountant, Verified By, Principal --}}
        <td style="width: 33%; text-align: left; vertical-align: bottom; border: none; padding: 0 10px;">
          <div style="border-top: 1px dashed #64748b; width: 130px; margin-bottom: 4px;"></div>
          <span style="font-family: Arial, Helvetica, sans-serif; font-size: 9px; font-weight: bold; color: #334155;">Accountant / Cashier</span>
        </td>
        <td style="width: 34%; text-align: center; vertical-align: bottom; border: none; padding: 0 10px;">
          <div style="border-top: 1px dashed #64748b; width: 130px; margin: 0 auto 4px auto;"></div>
          <span style="font-family: Arial, Helvetica, sans-serif; font-size: 9px; font-weight: bold; color: #334155;">Audited / Verified By</span>
        </td>
        <td style="width: 33%; text-align: right; vertical-align: bottom; border: none; padding: 0 10px;">
          @if($sigBase64)
            <img src="{{ $sigBase64 }}" alt="Signature" style="height: 32px; display: inline-block; margin-bottom: 2px;">
          @endif
          <div style="border-top: 1px dashed #64748b; width: 130px; margin-left: auto; margin-bottom: 4px;"></div>
          <span style="font-family: Arial, Helvetica, sans-serif; font-size: 9px; font-weight: bold; color: #334155;">Principal / Director</span>
        </td>

      @elseif($type === 'academic')
        {{-- Academic: Class Teacher, Exam Incharge, Principal --}}
        <td style="width: 33%; text-align: left; vertical-align: bottom; border: none; padding: 0 10px;">
          <div style="border-top: 1px dashed #64748b; width: 130px; margin-bottom: 4px;"></div>
          <span style="font-family: Arial, Helvetica, sans-serif; font-size: 9px; font-weight: bold; color: #334155;">Class Teacher</span>
        </td>
        <td style="width: 34%; text-align: center; vertical-align: bottom; border: none; padding: 0 10px;">
          <div style="border-top: 1px dashed #64748b; width: 130px; margin: 0 auto 4px auto;"></div>
          <span style="font-family: Arial, Helvetica, sans-serif; font-size: 9px; font-weight: bold; color: #334155;">Examination In-Charge</span>
        </td>
        <td style="width: 33%; text-align: right; vertical-align: bottom; border: none; padding: 0 10px;">
          @if($sigBase64)
            <img src="{{ $sigBase64 }}" alt="Signature" style="height: 32px; display: inline-block; margin-bottom: 2px;">
          @endif
          <div style="border-top: 1px dashed #64748b; width: 130px; margin-left: auto; margin-bottom: 4px;"></div>
          <span style="font-family: Arial, Helvetica, sans-serif; font-size: 9px; font-weight: bold; color: #334155;">Principal</span>
        </td>

      @elseif($type === 'certificate')
        {{-- Certificate: Prepared By, Verified By, Principal with Stamp --}}
        <td style="width: 30%; text-align: left; vertical-align: bottom; border: none; padding: 0 5px;">
          <div style="border-top: 1px dashed #64748b; width: 120px; margin-bottom: 4px;"></div>
          <span style="font-family: Arial, Helvetica, sans-serif; font-size: 9px; font-weight: bold; color: #334155;">Prepared By</span>
        </td>
        <td style="width: 40%; text-align: center; vertical-align: bottom; border: none; padding: 0 5px;">
          @if($stampBase64)
            <img src="{{ $stampBase64 }}" alt="Seal" style="max-height: 48px; display: inline-block; margin-bottom: 4px; opacity: 0.85;">
          @else
            <div style="height: 36px; border: 1px dashed #cbd5e1; width: 100px; margin: 0 auto 4px auto; line-height: 36px; font-size: 7.5px; color: #94a3b8; text-transform: uppercase;">Institutional Seal</div>
          @endif
          <span style="font-family: Arial, Helvetica, sans-serif; font-size: 9px; font-weight: bold; color: #334155;">Office Seal</span>
        </td>
        <td style="width: 30%; text-align: right; vertical-align: bottom; border: none; padding: 0 5px;">
          @if($sigBase64)
            <img src="{{ $sigBase64 }}" alt="Signature" style="height: 32px; display: inline-block; margin-bottom: 2px;">
          @endif
          <div style="border-top: 1px dashed #64748b; width: 120px; margin-left: auto; margin-bottom: 4px;"></div>
          <span style="font-family: Arial, Helvetica, sans-serif; font-size: 9px; font-weight: bold; color: #334155;">Principal</span>
        </td>

      @else
        {{-- Standard: Prepared By, Verified By, Principal --}}
        <td style="width: 33%; text-align: left; vertical-align: bottom; border: none; padding: 0 10px;">
          <div style="border-top: 1px dashed #64748b; width: 130px; margin-bottom: 4px;"></div>
          <span style="font-family: Arial, Helvetica, sans-serif; font-size: 9px; font-weight: bold; color: #334155;">Prepared By</span>
        </td>
        <td style="width: 34%; text-align: center; vertical-align: bottom; border: none; padding: 0 10px;">
          <div style="border-top: 1px dashed #64748b; width: 130px; margin: 0 auto 4px auto;"></div>
          <span style="font-family: Arial, Helvetica, sans-serif; font-size: 9px; font-weight: bold; color: #334155;">Verified By</span>
        </td>
        <td style="width: 33%; text-align: right; vertical-align: bottom; border: none; padding: 0 10px;">
          @if($sigBase64)
            <img src="{{ $sigBase64 }}" alt="Signature" style="height: 32px; display: inline-block; margin-bottom: 2px;">
          @endif
          <div style="border-top: 1px dashed #64748b; width: 130px; margin-left: auto; margin-bottom: 4px;"></div>
          <span style="font-family: Arial, Helvetica, sans-serif; font-size: 9px; font-weight: bold; color: #334155;">Principal</span>
        </td>
      @endif
    </tr>
  </table>
</div>

