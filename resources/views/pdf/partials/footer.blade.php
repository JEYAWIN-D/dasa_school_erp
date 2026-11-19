{{-- Clean official PDF footer: suppressed computer-generated notices and timestamps --}}
@php
    $schoolSetting = $school ?? \App\Models\SchoolSetting::instance() ?? \App\Models\SchoolSetting::first();
@endphp

@if(!empty($showPageNumbers ?? false))
<div class="school-pdf-footer" style="position: fixed; bottom: -20px; left: 0px; right: 0px; height: 20px; font-family: Arial, Helvetica, sans-serif; font-size: 7.5px; color: #64748b;">
  <table style="width: 100%; border-collapse: collapse; border: none; margin: 0; padding: 0;">
    <tr>
      <td style="text-align: right; vertical-align: middle; border: none; font-weight: bold;">
        <script type="text/php">
            if (isset($pdf)) {
                $text = "Page " . $PAGE_NUM . " of " . $PAGE_COUNT;
                $size = 7.5;
                $font = $fontMetrics->getFont("Arial, Helvetica, sans-serif", "normal");
                $width = $fontMetrics->getTextWidth($text, $font, $size);
                $pdf->page_text($pdf->get_width() - 40 - $width, $pdf->get_height() - 25, $text, $font, $size, [0.39, 0.45, 0.54]);
            }
        </script>
      </td>
    </tr>
  </table>
</div>
@endif
