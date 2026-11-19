@extends('layouts.app')
@section('title', 'Book Label — ' . ($book->accession_number ?? $book->title))

@push('head')
<style>
/* Label Specific Print Styling */
.book-label-stage {
  max-width: 480px;
  margin: 0 auto;
}

.book-label-card {
  width: 65mm;
  min-height: 38mm;
  padding: 3mm 3.5mm;
  background: #ffffff;
  color: #0f172a;
  border: 1.5px solid #0f172a;
  border-radius: 4px;
  box-sizing: border-box;
  font-family: Arial, Helvetica, sans-serif;
  margin: 0 auto;
}

@media print {
  /* Suppress standard ERP layout elements */
  header, nav, aside, #sidebar, #toast-container, .no-print, button, a {
    display: none !important;
  }

  html, body {
    margin: 0 !important;
    padding: 0 !important;
    background: #ffffff !important;
    color: #000000 !important;
    width: 100% !important;
    height: auto !important;
    overflow: visible !important;
  }

  .book-label-stage {
    max-width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
  }

  .book-label-card {
    border: 1px solid #000000 !important;
    box-shadow: none !important;
    margin: 0 auto !important;
    page-break-inside: avoid !important;
    break-inside: avoid !important;
  }

  @page {
    size: 70mm 45mm landscape;
    margin: 2mm !important;
  }
}
</style>
@endpush

@section('content')
<div class="space-y-6 pb-12 book-label-stage">

  {{-- Print Controls Header Component --}}
  <div class="no-print">
    <x-print.controls title="Book Spine & QR Label" defaultSize="label" defaultOrientation="landscape" :backUrl="route('library.books')" />
  </div>

  {{-- Secondary Toolbar --}}
  <div class="no-print bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between flex-wrap gap-3">
    <div>
      <h2 class="text-sm font-extrabold text-slate-900">{{ $book->title }}</h2>
      <p class="text-xs text-slate-500 font-mono">{{ $book->accession_number }} &bull; ISBN: {{ $book->isbn ?: 'N/A' }}</p>
    </div>
    <div class="flex items-center gap-2">
      <a href="{{ route('library.books.qr-label', $book->id) }}?pdf=1" target="_blank" class="btn btn-secondary btn-sm flex items-center gap-1.5 font-bold">
        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Download PDF
      </a>
      <button type="button" onclick="window.print()" class="btn btn-primary btn-sm flex items-center gap-1.5 font-bold shadow-xs">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Print Label
      </button>
    </div>
  </div>

  {{-- On-Screen Sticker Label Preview Card --}}
  <div class="bg-slate-100/80 p-6 sm:p-10 rounded-3xl border border-slate-200 flex flex-col items-center justify-center">
    
    <div class="no-print text-center text-xs text-slate-500 font-medium mb-3">
      Print Preview • Thermal Barcode Label (~65mm × 38mm)
    </div>

    <div class="book-label-card shadow-lg">
      <div style="display: flex; gap: 8px; align-items: center; height: 100%;">
        
        {{-- Left: QR Code Box --}}
        <div style="width: 25mm; flex-shrink: 0; text-align: center; border-right: 1px dashed #64748b; padding-right: 6px;">
          <div style="display: inline-block; width: 22mm; height: 22mm;">
            {!! $qr !!}
          </div>
          <div style="font-size: 7px; font-family: monospace; font-weight: bold; margin-top: 2px; color: #334155;">
            SCAN &bull; BORROW
          </div>
        </div>

        {{-- Right: Book Metadata & Location --}}
        <div style="flex: 1; min-width: 0; line-height: 1.25;">
          <div style="font-size: 6.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.3px; color: #475569; border-bottom: 0.5px solid #cbd5e1; padding-bottom: 1px; margin-bottom: 2px;">
            {{ \Illuminate\Support\Str::limit($school?->school_name ?? 'ERODE PUBLIC SCHOOL', 28) }}
          </div>
          
          <div style="font-size: 11px; font-weight: 900; font-family: monospace; color: #0f172a; letter-spacing: 0.5px;">
            {{ $book->accession_number ?: ('ACC-' . $book->id) }}
          </div>

          <div style="font-size: 8px; font-weight: 800; color: #1e293b; margin-top: 1px; line-height: 1.2;">
            {{ \Illuminate\Support\Str::limit($book->title, 34) }}
          </div>

          @if($book->author)
          <div style="font-size: 7px; color: #475569; margin-top: 0.5px;">
            by {{ \Illuminate\Support\Str::limit($book->author, 24) }}
          </div>
          @endif

          <div style="margin-top: 3px; padding-top: 2px; border-top: 0.5px dotted #94a3b8; font-size: 7px; display: flex; justify-content: space-between;">
            <span style="font-weight: 700; color: #047857;">
              @if($book->rack || $book->shelf)
                R: {{ $book->rack?->rack_number ?: '—' }} &bull; S: {{ $book->shelf?->shelf_number ?: '—' }}
              @else
                {{ $book->location ?: 'Main Stacks' }}
              @endif
            </span>
            @if($book->isbn)
            <span style="font-family: monospace; color: #64748b; font-size: 6.5px;">
              {{ \Illuminate\Support\Str::limit($book->isbn, 13) }}
            </span>
            @endif
          </div>
        </div>

      </div>
    </div>

  </div>

</div>
@endsection
