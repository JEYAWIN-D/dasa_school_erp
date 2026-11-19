@extends('layouts.app')
@section('title', 'Bulk Library Book Labels — Print Sheet')

@push('head')
<style>
.label-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(65mm, 1fr));
  gap: 4mm;
}

.bulk-label-item {
  width: 100%;
  height: 38mm;
  padding: 2.5mm 3mm;
  background: #ffffff;
  color: #0f172a;
  border: 1px solid #94a3b8;
  border-radius: 4px;
  box-sizing: border-box;
  font-family: Arial, Helvetica, sans-serif;
  page-break-inside: avoid;
  break-inside: avoid;
}

@media print {
  header, nav, aside, #sidebar, #toast-container, .no-print, button, a {
    display: none !important;
  }

  html, body {
    margin: 0 !important;
    padding: 0 !important;
    background: #ffffff !important;
  }

  .label-grid {
    grid-template-columns: repeat(3, 1fr) !important;
    gap: 2mm !important;
  }

  .bulk-label-item {
    border: 1px solid #334155 !important;
    box-shadow: none !important;
    height: 35mm !important;
  }

  @page {
    size: A4 portrait;
    margin: 6mm !important;
  }
}
</style>
@endpush

@section('content')
<div class="space-y-6 pb-12">

  {{-- Print Controls Header Component --}}
  <div class="no-print">
    <x-print.controls title="Bulk Book Barcode Labels (Sheet)" defaultSize="a4" defaultOrientation="portrait" :backUrl="route('library.books')" />
  </div>

  <div class="no-print bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between flex-wrap gap-3">
    <div>
      <h2 class="text-sm font-extrabold text-slate-900">Total Labels: {{ count($booksWithQr) }}</h2>
      <p class="text-xs text-slate-500">Formatted for 3-across standard A4 laser label sheets (24 labels per page)</p>
    </div>
    <div class="flex items-center gap-2">
      <a href="{{ route('library.books.qr-bulk') }}?pdf=1" target="_blank" class="btn btn-secondary btn-sm flex items-center gap-1.5 font-bold">
        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Download Sheet (PDF)
      </a>
      <button type="button" onclick="window.print()" class="btn btn-primary btn-sm flex items-center gap-1.5 font-bold shadow-xs">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Print Labels Sheet
      </button>
    </div>
  </div>

  {{-- Labels Grid --}}
  <div class="label-grid">
    @foreach($booksWithQr as $item)
      @php
        $bk = $item['book'];
        $qrSvg = $item['qr'];
      @endphp
      <div class="bulk-label-item">
        <div style="display: flex; gap: 6px; align-items: center; height: 100%;">
          
          <div style="width: 22mm; flex-shrink: 0; text-align: center; border-right: 1px dashed #cbd5e1; padding-right: 4px;">
            <div style="display: inline-block; width: 20mm; height: 20mm;">
              {!! $qrSvg !!}
            </div>
            <div style="font-size: 6px; font-family: monospace; font-weight: bold; margin-top: 1px; color: #475569;">
              SCAN &bull; BORROW
            </div>
          </div>

          <div style="flex: 1; min-width: 0; line-height: 1.2;">
            <div style="font-size: 6px; font-weight: 800; text-transform: uppercase; color: #475569; border-bottom: 0.5px solid #cbd5e1; padding-bottom: 1px; margin-bottom: 1px;">
              {{ \Illuminate\Support\Str::limit($school?->school_name ?? 'ERODE PUBLIC SCHOOL', 26) }}
            </div>
            
            <div style="font-size: 9.5px; font-weight: 900; font-family: monospace; color: #0f172a;">
              {{ $bk->accession_number ?: ('ACC-' . $bk->id) }}
            </div>

            <div style="font-size: 7.5px; font-weight: 800; color: #1e293b; margin-top: 1px; line-height: 1.15; max-height: 18px; overflow: hidden;">
              {{ \Illuminate\Support\Str::limit($bk->title, 32) }}
            </div>

            @if($bk->author)
            <div style="font-size: 6.5px; color: #64748b; margin-top: 0.5px;">
              by {{ \Illuminate\Support\Str::limit($bk->author, 22) }}
            </div>
            @endif

            <div style="margin-top: 2px; padding-top: 1px; border-top: 0.5px dotted #94a3b8; font-size: 6.5px; display: flex; justify-content: space-between;">
              <span style="font-weight: 700; color: #047857;">
                @if($bk->rack || $bk->shelf)
                  R: {{ $bk->rack?->rack_number ?: '—' }} &bull; S: {{ $bk->shelf?->shelf_number ?: '—' }}
                @else
                  {{ $bk->location ?: 'Stacks' }}
                @endif
              </span>
              @if($bk->isbn)
              <span style="font-family: monospace; color: #64748b; font-size: 6px;">
                {{ \Illuminate\Support\Str::limit($bk->isbn, 11) }}
              </span>
              @endif
            </div>
          </div>

        </div>
      </div>
    @endforeach
  </div>

</div>
@endsection
