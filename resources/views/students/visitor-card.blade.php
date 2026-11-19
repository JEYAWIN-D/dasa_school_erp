@extends('layouts.app')

@section('title', 'Parent & Guardian Visitor Card — ' . $student->full_name)

@section('content')
<div class="space-y-6 max-w-5xl mx-auto" x-data="{ orientation: 'landscape', activeSide: 'both' }">

  {{-- Top Navigation & Action Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 print:hidden">
    <div class="flex items-center gap-3">
      <a href="{{ route('students.show', $student->id) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white hover:bg-slate-50 text-slate-700 transition border border-slate-200 shadow-2xs">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      </a>
      <div>
        <div class="flex items-center gap-2">
          <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Parent &amp; Guardian Campus Visitor Card</h1>
          <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#892528]/10 text-[#892528] border border-[#892528]/25">CR80 Badge</span>
        </div>
        <p class="text-xs text-slate-500 font-medium">Student: {{ $student->full_name }} &bull; Adm: {{ $student->admission_no }} &bull; Class: {{ $student->currentEnrollment?->class?->name ?? '—' }} ({{ $student->currentEnrollment?->section?->name ?? 'A' }})</p>
      </div>
    </div>

    <div class="flex items-center gap-2 flex-wrap">
      {{-- Orientation Toggle --}}
      <div class="inline-flex p-1 bg-slate-100 rounded-xl border border-slate-200 text-xs font-bold">
        <button type="button" @click="orientation = 'landscape'"
                :class="orientation === 'landscape' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-800'"
                class="px-2.5 py-1 rounded-lg transition">
          Landscape
        </button>
        <button type="button" @click="orientation = 'portrait'"
                :class="orientation === 'portrait' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-800'"
                class="px-2.5 py-1 rounded-lg transition">
          Portrait
        </button>
      </div>

      {{-- Side Toggle (Landscape only) --}}
      <div x-show="orientation === 'landscape'" class="inline-flex p-1 bg-slate-100 rounded-xl border border-slate-200 text-xs font-bold">
        <button type="button" @click="activeSide = 'both'"
                :class="activeSide === 'both' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-800'"
                class="px-2 py-1 rounded-lg transition">
          Both Sides
        </button>
        <button type="button" @click="activeSide = 'front'"
                :class="activeSide === 'front' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-800'"
                class="px-2 py-1 rounded-lg transition">
          Front
        </button>
        <button type="button" @click="activeSide = 'back'"
                :class="activeSide === 'back' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-800'"
                class="px-2 py-1 rounded-lg transition">
          Back
        </button>
      </div>

      @php
        $whatsappMsg = urlencode("Dear Parent, Here is your official Erode Public School Campus Visitor & Escort Pass for {$student->full_name} (Adm: {$student->admission_no}):\n" . route('public.visitor-card.view', ['token' => $student->parent_visitor_pass_token]));
      @endphp
      <a href="https://api.whatsapp.com/send?text={{ $whatsappMsg }}" target="_blank"
         class="btn btn-sm bg-emerald-600 hover:bg-emerald-700 text-white font-bold flex items-center gap-1.5 shadow-xs border-emerald-600">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
        WhatsApp
      </a>

      <button onclick="window.print()" class="btn btn-sm bg-[#892528] hover:bg-[#6e1e20] text-white font-bold flex items-center gap-1.5 shadow-xs border-[#892528] cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Print Physical Pass
      </button>

      <a href="{{ route('public.visitor-card.view', ['token' => $student->parent_visitor_pass_token]) }}" target="_blank"
         class="btn btn-sm btn-secondary flex items-center gap-1.5 shadow-xs">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
        Open Mobile View
      </a>
    </div>
  </div>

  {{-- Preview Stage Container --}}
  <div class="bg-slate-50/70 p-6 sm:p-10 rounded-3xl border border-slate-200/90 shadow-xs relative">
    
    <div class="flex items-center justify-between border-b border-slate-200 pb-3 mb-6 print:hidden">
      <div class="flex items-center gap-2">
        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
        <h2 class="text-sm font-black text-slate-800 uppercase tracking-wider">
          CR80 Physical Card Specification &bull; 85.60 &times; 53.98 mm
        </h2>
      </div>
      <span class="text-xs font-mono font-bold text-slate-400">EPS THEME &bull; MAROON &amp; GOLD</span>
    </div>

    {{-- ── 1. LANDSCAPE VIEW (CR80 RATIO: 85.6 × 53.98 mm) ────────────────── --}}
    <div x-show="orientation === 'landscape'" class="flex flex-col lg:flex-row items-center justify-center gap-8 py-2">

      {{-- FRONT SIDE --}}
      <div x-show="activeSide === 'both' || activeSide === 'front'" class="flex flex-col items-center">
        <div id="physicalPassFront"
             class="cr80-card relative bg-white rounded-2xl shadow-xl border border-slate-300 overflow-hidden flex flex-col justify-between"
             style="width: 440px; height: 278px; font-family: 'Plus Jakarta Sans', system-ui, sans-serif;">
          
          {{-- Top Header Banner (EPS Maroon Theme) --}}
          <div class="h-13 px-3.5 bg-gradient-to-r from-[#892528] via-[#6B1A1D] to-[#892528] text-white flex items-center justify-between border-b-2 border-amber-500 shrink-0">
            <div class="flex items-center gap-2">
              <img src="{{ asset('images/school-logo-transparent.png') }}" class="w-8 h-8 object-contain filter drop-shadow-md" alt="Crest" onerror="this.src='{{ asset('images/school-logo.png') }}'">
              <div class="leading-tight">
                <h4 class="text-[11px] font-black uppercase tracking-wider text-white">
                  {{ $school->school_name ?? 'ERODE PUBLIC SCHOOL' }}
                </h4>
                <p class="text-[8px] text-amber-300 font-bold uppercase tracking-tight">
                  CBSE SENIOR SECONDARY &bull; AFF NO: {{ $school->affiliation_no ?? '1931585' }}
                </p>
              </div>
            </div>
            <span class="text-[7.5px] font-black uppercase px-2 py-0.5 rounded-full bg-amber-400 text-slate-950 shadow-xs tracking-wider">
              CAMPUS VISITOR &bull; ESCORT PASS
            </span>
          </div>

          {{-- Card Body --}}
          <div class="px-3.5 py-2.5 flex items-center gap-3.5 flex-1 min-h-0 bg-white">
            {{-- Student Photo --}}
            <div class="w-20 h-26 rounded-xl border-2 border-[#892528] overflow-hidden bg-slate-100 flex items-center justify-center shrink-0 shadow-sm relative">
              @if($student->photo)
                <img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->full_name }}" class="w-full h-full object-cover">
              @else
                <span class="text-xl font-black text-[#892528] w-full h-full flex items-center justify-center bg-rose-50">
                  {{ strtoupper(substr($student->first_name ?? 'S', 0, 1) . substr($student->last_name ?? 'T', 0, 1)) }}
                </span>
              @endif
              @if($student->blood_group)
                <span class="absolute bottom-0 inset-x-0 bg-[#892528] text-white font-mono font-bold text-[7.5px] text-center py-0.5">
                  {{ $student->blood_group }}
                </span>
              @endif
            </div>

            {{-- Middle: Student & Escorts Info --}}
            <div class="min-w-0 flex-1 space-y-1">
              <div>
                <h3 class="text-xs font-black text-slate-900 uppercase truncate leading-tight">
                  {{ $student->full_name }}
                </h3>
                <p class="text-[10px] font-bold text-[#892528] leading-tight">
                  Class {{ $student->currentEnrollment?->class?->name ?? '—' }} ({{ $student->currentEnrollment?->section?->name ?? 'A' }}) &bull; Adm: <span class="font-mono text-slate-800">{{ $student->admission_no }}</span>
                </p>
              </div>

              {{-- Authorized Escorts Box --}}
              <div class="bg-slate-50 border border-slate-200 rounded-lg p-1.5 text-[9px] space-y-1">
                <div class="text-[7.5px] font-black uppercase tracking-wider text-slate-400">Authorized Escorts:</div>
                
                <div class="flex items-center justify-between gap-1">
                  <span class="font-bold text-slate-700 truncate">👨 {{ $student->father_name ?? $student->parent_name ?? 'Father' }}</span>
                  <span class="font-mono text-slate-500 text-[8.5px] shrink-0">{{ $student->father_mobile ?? $student->parent_mobile ?? '—' }}</span>
                </div>

                <div class="flex items-center justify-between gap-1">
                  <span class="font-bold text-slate-700 truncate">👩 {{ $student->mother_name ?? 'Mother' }}</span>
                  <span class="font-mono text-slate-500 text-[8.5px] shrink-0">{{ $student->mother_mobile ?? '—' }}</span>
                </div>

                @if($student->guardian_name)
                <div class="flex items-center justify-between gap-1">
                  <span class="font-bold text-slate-700 truncate">👤 {{ $student->guardian_name }} ({{ $student->guardian_relation ?? 'Guardian' }})</span>
                  <span class="font-mono text-slate-500 text-[8.5px] shrink-0">{{ $student->guardian_mobile ?? '—' }}</span>
                </div>
                @endif
              </div>
            </div>

            {{-- Right: Gate Security QR Code --}}
            <div class="shrink-0 text-center flex flex-col items-center justify-center pl-1">
              <div class="w-16 h-16 bg-white p-1 rounded-lg border border-slate-200 shadow-2xs flex items-center justify-center">
                @if(!empty($qrCodeSvg))
                  {!! $qrCodeSvg !!}
                @else
                  <div class="text-[7px] font-mono text-slate-400 text-center">QR CODE</div>
                @endif
              </div>
              <div class="flex items-center gap-1 mt-1">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-[7px] font-bold text-slate-600 uppercase tracking-tight">Gate Check</span>
              </div>
              <span class="text-[6.5px] font-mono text-[#892528] font-bold">{{ $currentYear?->name ?? '2026-2027' }}</span>
            </div>
          </div>

          {{-- Footer --}}
          <div class="h-6 px-3.5 bg-slate-900 text-white flex items-center justify-between text-[7.5px] font-semibold shrink-0">
            <span class="text-slate-400 font-mono truncate">Ph: {{ $school->phone ?? '93677 54654' }}</span>
            <span class="text-amber-400 font-bold uppercase tracking-wider">OFFICIAL ESCORT CREDENTIAL &bull; CR80</span>
          </div>
        </div>
        <span class="text-xs font-black text-slate-500 uppercase mt-2 print:hidden">Front Side Preview</span>
      </div>

      {{-- BACK SIDE --}}
      <div x-show="activeSide === 'both' || activeSide === 'back'" class="flex flex-col items-center">
        <div id="physicalPassBack"
             class="cr80-card relative bg-white rounded-2xl shadow-xl border border-slate-300 overflow-hidden flex flex-col justify-between"
             style="width: 440px; height: 278px; font-family: 'Plus Jakarta Sans', system-ui, sans-serif;">
          
          {{-- Back Header --}}
          <div class="h-8 px-3.5 bg-gradient-to-r from-[#892528] via-[#6B1A1D] to-[#892528] text-white flex items-center justify-between border-b-2 border-amber-500 text-[9px] font-black uppercase tracking-wider shrink-0">
            <span>{{ $school->school_name ?? 'ERODE PUBLIC SCHOOL' }}</span>
            <span class="text-amber-300">CAMPUS VISITOR TERMS</span>
          </div>

          {{-- Back Body --}}
          <div class="p-3 flex-1 flex flex-col justify-between text-[8px] bg-slate-50/50 space-y-1.5">
            <div class="space-y-1 text-slate-600 leading-tight">
              <div class="flex items-start gap-1.5">
                <span class="text-[#892528] font-black">•</span>
                <span>This pass must be presented at the school security checkpoint for campus entry, PTM, or student pickup.</span>
              </div>
              <div class="flex items-start gap-1.5">
                <span class="text-[#892528] font-black">•</span>
                <span>Strictly non-transferable. Only authorized escorts listed on this card are permitted to escort the student.</span>
              </div>
              <div class="flex items-start gap-1.5">
                <span class="text-[#892528] font-black">•</span>
                <span>Loss of this pass must be reported immediately to the school administration office.</span>
              </div>
            </div>

            <div class="pt-1 border-t border-slate-200 flex items-center justify-between">
              <div class="text-[7.5px] text-slate-500">
                <div class="font-bold text-slate-700">Security Helpline:</div>
                <div class="font-mono text-[#892528] font-bold">+91 {{ $school->phone ?? '93677 54654' }}</div>
              </div>
              <div class="text-right">
                <img src="{{ asset('images/principal-signature.svg') }}" class="h-5 inline-block" alt="Sign">
                <div class="text-[6.5px] text-slate-500 font-bold uppercase">Authorized Signatory</div>
              </div>
            </div>
          </div>

          {{-- Back Footer --}}
          <div class="h-5 px-3.5 bg-slate-900 text-white flex items-center justify-between text-[7px] font-mono shrink-0">
            <span class="text-slate-400 truncate">{{ $school->address ?? 'Chennimalai Road, Erode - 638051' }}</span>
            <span class="text-amber-400 font-bold">BACK SIDE</span>
          </div>
        </div>
        <span class="text-xs font-black text-slate-500 uppercase mt-2 print:hidden">Back Side Preview</span>
      </div>

    </div>

    {{-- ── 2. PORTRAIT VIEW (CR80 RATIO: 53.98 × 85.6 mm) ─────────────────── --}}
    <div x-show="orientation === 'portrait'" style="display:none;" class="flex items-center justify-center py-2">
      <div id="physicalPassPortrait"
           class="cr80-card relative bg-white rounded-2xl shadow-xl border border-slate-300 overflow-hidden flex flex-col justify-between text-center"
           style="width: 278px; height: 440px; font-family: 'Plus Jakarta Sans', system-ui, sans-serif;">
        
        {{-- Portrait Header --}}
        <div class="bg-gradient-to-r from-[#892528] via-[#6B1A1D] to-[#892528] text-white p-3 border-b-2 border-amber-500 shrink-0">
          <img src="{{ asset('images/school-logo-transparent.png') }}" class="w-8 h-8 object-contain mx-auto filter drop-shadow-md" alt="Crest" onerror="this.src='{{ asset('images/school-logo.png') }}'">
          <h4 class="text-[11px] font-black uppercase tracking-wider text-white mt-1 leading-tight">
            {{ $school->school_name ?? 'ERODE PUBLIC SCHOOL' }}
          </h4>
          <span class="inline-block mt-1 text-[7px] font-black uppercase px-2 py-0.5 rounded-full bg-amber-400 text-slate-950">
            CAMPUS VISITOR &bull; ESCORT PASS
          </span>
        </div>

        {{-- Portrait Body --}}
        <div class="p-3 space-y-2 flex-1 flex flex-col justify-between">
          {{-- Student Photo --}}
          <div class="w-20 h-24 rounded-xl border-2 border-[#892528] overflow-hidden bg-slate-100 mx-auto shadow-sm relative shrink-0">
            @if($student->photo)
              <img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->full_name }}" class="w-full h-full object-cover">
            @else
              <span class="text-xl font-black text-[#892528] w-full h-full flex items-center justify-center bg-rose-50">
                {{ strtoupper(substr($student->first_name ?? 'S', 0, 1) . substr($student->last_name ?? 'T', 0, 1)) }}
              </span>
            @endif
          </div>

          <div>
            <h3 class="text-xs font-black text-slate-900 uppercase truncate leading-tight">
              {{ $student->full_name }}
            </h3>
            <p class="text-[9.5px] font-bold text-[#892528] leading-tight mt-0.5">
              Class {{ $student->currentEnrollment?->class?->name ?? '—' }} ({{ $student->currentEnrollment?->section?->name ?? 'A' }}) &bull; Adm: <span class="font-mono text-slate-800">{{ $student->admission_no }}</span>
            </p>
          </div>

          {{-- Escorts --}}
          <div class="bg-slate-50 border border-slate-200 rounded-lg p-2 text-[8px] space-y-1 text-left">
            <div class="text-[7px] font-black uppercase tracking-wider text-slate-400">Authorized Escorts:</div>
            <div class="flex items-center justify-between">
              <span class="font-bold text-slate-700 truncate">👨 {{ $student->father_name ?? $student->parent_name ?? 'Father' }}</span>
              <span class="font-mono text-slate-500 text-[8px]">{{ $student->father_mobile ?? $student->parent_mobile ?? '—' }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="font-bold text-slate-700 truncate">👩 {{ $student->mother_name ?? 'Mother' }}</span>
              <span class="font-mono text-slate-500 text-[8px]">{{ $student->mother_mobile ?? '—' }}</span>
            </div>
          </div>

          {{-- QR Code --}}
          <div class="flex items-center justify-center gap-3 bg-slate-50 border border-slate-200 rounded-lg p-1.5">
            <div class="w-12 h-12 bg-white p-0.5 rounded border border-slate-200 shrink-0 flex items-center justify-center">
              @if(!empty($qrCodeSvg))
                {!! $qrCodeSvg !!}
              @else
                <div class="text-[6px] font-mono text-slate-400">QR</div>
              @endif
            </div>
            <div class="text-left text-[7.5px] leading-tight">
              <div class="font-black text-slate-800 uppercase">Gate Clearance</div>
              <div class="text-emerald-600 font-bold flex items-center gap-1 mt-0.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Verified Pass
              </div>
              <div class="text-slate-400 font-mono mt-0.5">{{ $currentYear?->name ?? '2026-2027' }}</div>
            </div>
          </div>
        </div>

        {{-- Portrait Footer --}}
        <div class="h-5 px-3 bg-slate-900 text-white flex items-center justify-between text-[7px] font-mono shrink-0">
          <span class="text-slate-400">Ph: {{ $school->phone ?? '93677 54654' }}</span>
          <span class="text-amber-400 font-bold uppercase">CR80 PORTRAIT</span>
        </div>
      </div>
    </div>

  </div>

</div>

<style>
  @media print {
    body * {
      visibility: hidden;
    }
    .print\\:hidden, header, nav, aside {
      display: none !important;
    }
    .cr80-card, .cr80-card * {
      visibility: visible;
    }
    .cr80-card {
      position: relative !important;
      left: auto !important;
      top: auto !important;
      transform: none !important;
      box-shadow: none !important;
      border: 1px solid #cbd5e1 !important;
      border-radius: 4mm !important;
      overflow: hidden !important;
      margin: 10mm auto !important;
      page-break-inside: avoid !important;
    }
  }
</style>
@endsection
