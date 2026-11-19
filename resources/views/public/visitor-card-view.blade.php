<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Parent &amp; Guardian Visitor Pass — {{ $student->full_name }} ({{ $student->admission_no }})</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background: radial-gradient(circle at 50% 0%, #1e293b 0%, #0f172a 100%);
    }
    .font-mono { font-family: 'JetBrains Mono', monospace; }
    @media print {
      body { background: #fff !important; color: #000 !important; }
      .no-print { display: none !important; }
      .print-card-wrapper { box-shadow: none !important; border: 1px solid #cbd5e1 !important; }
    }
  </style>
</head>
<body class="min-h-screen text-slate-100 flex flex-col items-center justify-center p-4 sm:p-6">

  <div class="w-full max-w-md mx-auto space-y-4">

    {{-- Top Action Bar (Mobile-friendly) --}}
    <div class="flex items-center justify-between no-print px-1">
      <div class="flex items-center gap-2">
        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
          <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse mr-1.5"></span>
          Active Campus Escort Pass
        </span>
      </div>
      <div class="flex items-center gap-2">
        <button onclick="window.print()" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
          Print Pass
        </button>
      </div>
    </div>

    {{-- ── Physical/Digital CR80 Badge Card ────────────────────────────── --}}
    <div id="visitorBadge" class="print-card-wrapper bg-white text-slate-900 rounded-2xl overflow-hidden shadow-2xl border border-slate-200 transition relative">

      {{-- Header Ribbon (EPS Maroon Theme) --}}
      <div class="bg-gradient-to-r from-[#892528] via-[#6B1A1D] to-[#892528] text-white p-4 border-b-2 border-amber-500 relative overflow-hidden">
        <div class="relative flex items-center justify-between gap-3">
          <div class="flex items-center gap-2.5">
            <img src="{{ asset('images/school-logo-transparent.png') }}" class="w-9 h-9 object-contain filter drop-shadow-md" alt="Crest" onerror="this.src='{{ asset('images/school-logo.png') }}'">
            <div>
              <div class="flex items-center gap-1.5">
                <span class="text-[8px] font-black uppercase tracking-wider bg-amber-400 text-slate-950 px-1.5 py-0.5 rounded">OFFICIAL ESCORT PASS</span>
              </div>
              <h1 class="text-xs sm:text-sm font-black tracking-wider uppercase text-white mt-0.5">{{ $school->school_name ?? 'ERODE PUBLIC SCHOOL' }}</h1>
              <p class="text-[8px] text-amber-300 font-bold uppercase">CBSE SENIOR SECONDARY &bull; AFF NO: {{ $school->affiliation_no ?? '1931585' }}</p>
            </div>
          </div>
        </div>
      </div>

      {{-- Student Core Profile Strip --}}
      <div class="p-3.5 bg-white border-b border-slate-100 flex items-center gap-3">
        <div class="w-16 h-20 rounded-xl bg-slate-100 border-2 border-[#892528] overflow-hidden shrink-0 shadow-sm relative flex items-center justify-center">
          @if($student->photo)
            <img src="{{ asset('storage/' . $student->photo) }}" class="w-full h-full object-cover" alt="{{ $student->full_name }}">
          @else
            <span class="text-xl font-black text-[#892528]">
              {{ strtoupper(substr($student->first_name ?? 'S', 0, 1)) }}
            </span>
          @endif
        </div>
        <div class="min-w-0 flex-1 space-y-1">
          <div class="flex items-center justify-between gap-2">
            <h2 class="text-sm font-black text-slate-900 uppercase truncate leading-tight">{{ $student->full_name }}</h2>
            @if($student->blood_group)
              <span class="px-1.5 py-0.5 rounded bg-rose-100 text-rose-800 text-[9px] font-mono font-black border border-rose-200 shrink-0">
                {{ $student->blood_group }}
              </span>
            @endif
          </div>
          <div class="text-[10px] space-y-0.5">
            <p class="font-bold text-[#892528]">
              Class {{ $student->currentEnrollment?->class?->name ?? '—' }} ({{ $student->currentEnrollment?->section?->name ?? 'A' }})
            </p>
            <p class="text-slate-500 font-mono text-[9.5px]">
              Adm No: <strong class="text-slate-800">{{ $student->admission_no }}</strong>
            </p>
          </div>
        </div>
      </div>

      {{-- Authorized Parents & Escorts --}}
      <div class="p-3.5 space-y-2.5 bg-slate-50/70 border-b border-slate-200">
        <div class="text-[9px] font-black uppercase tracking-wider text-slate-400">
          Authorized Escorts &bull; Parents &amp; Guardian
        </div>

        <div class="space-y-1.5 text-xs">
          {{-- Father --}}
          <div class="p-2 rounded-xl border border-slate-200 bg-white flex items-center justify-between">
            <div class="flex items-center gap-2">
              <span class="text-sm">👨</span>
              <div>
                <span class="text-[8px] font-black uppercase text-blue-600 block">Father</span>
                <span class="font-bold text-slate-800 text-xs">{{ $student->father_name ?? $student->parent_name ?? '—' }}</span>
              </div>
            </div>
            <span class="font-mono text-slate-500 text-xs">{{ $student->father_mobile ?? $student->parent_mobile ?? '—' }}</span>
          </div>

          {{-- Mother --}}
          <div class="p-2 rounded-xl border border-slate-200 bg-white flex items-center justify-between">
            <div class="flex items-center gap-2">
              <span class="text-sm">👩</span>
              <div>
                <span class="text-[8px] font-black uppercase text-rose-600 block">Mother</span>
                <span class="font-bold text-slate-800 text-xs">{{ $student->mother_name ?? '—' }}</span>
              </div>
            </div>
            <span class="font-mono text-slate-500 text-xs">{{ $student->mother_mobile ?? '—' }}</span>
          </div>

          {{-- Guardian --}}
          @if($student->guardian_name)
          <div class="p-2 rounded-xl border border-slate-200 bg-white flex items-center justify-between">
            <div class="flex items-center gap-2">
              <span class="text-sm">👤</span>
              <div>
                <span class="text-[8px] font-black uppercase text-amber-600 block">Guardian ({{ $student->guardian_relation ?? 'Guardian' }})</span>
                <span class="font-bold text-slate-800 text-xs">{{ $student->guardian_name }}</span>
              </div>
            </div>
            <span class="font-mono text-slate-500 text-xs">{{ $student->guardian_mobile ?? '—' }}</span>
          </div>
          @endif
        </div>
      </div>

      {{-- Gate Security QR Code Box --}}
      <div class="p-3 bg-white flex items-center gap-3">
        <div class="bg-slate-50 p-1 rounded-xl border border-slate-200 shrink-0 w-16 h-16 flex items-center justify-center">
          @if(!empty($qrCodeSvg))
            {!! $qrCodeSvg !!}
          @else
            <div class="text-[7px] font-mono text-slate-400 text-center">QR CODE</div>
          @endif
        </div>
        <div class="space-y-0.5 min-w-0">
          <div class="flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="text-[10px] font-black uppercase text-slate-800">Security Gate Checkpoint</span>
          </div>
          <p class="text-[9px] text-slate-500 leading-tight">
            Scan at school entrance to verify authorized escort clearance.
          </p>
          <p class="text-[8.5px] font-mono text-[#892528] font-bold">Valid Academic Year &bull; Official CR80</p>
        </div>
      </div>

      {{-- Footer --}}
      <div class="bg-slate-900 text-white px-3.5 py-2 flex items-center justify-between text-[8px] font-medium">
        <span class="text-slate-400 font-mono">Ph: {{ $school->phone ?? '93677 54654' }}</span>
        <span class="text-amber-400 font-bold uppercase">CR80 CAMPUS ESCORT BADGE</span>
      </div>

    </div>

    {{-- Bottom Notice --}}
    <p class="text-center text-[10px] text-slate-400 no-print">
      Must be presented at campus security gates for student pickup and campus entry.
    </p>

  </div>

</body>
</html>
