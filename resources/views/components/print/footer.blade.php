{{-- Clean official printout: footer disclaimers, printed dates and page text suppressed --}}
@props([
    'note' => null,
    'school' => null,
])

@if(!empty($note))
  <div class="school-print-footer mt-6 pt-2 border-t border-slate-200 text-[9.5px] text-slate-500 text-center select-none">
    {{ $note }}
  </div>
@endif
