@php
  $schoolSetting = $school ?? \App\Models\SchoolSetting::instance() ?? \App\Models\SchoolSetting::first();
  $schoolName = $schoolSetting?->school_name ?? config('app.name', 'Erode Public School');
  $affLine = $schoolSetting?->affiliation_line ?? ($schoolSetting?->board ? 'Affiliated to ' . $schoolSetting->board : null);
  $addressLine = $schoolSetting?->full_address ?? $schoolSetting?->address;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fee Structure — {{ $selectedClass?->name ?? 'Standard' }} ({{ $academicYear?->name ?? (now()->year . '–' . (now()->year + 1)) }})</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background-color: #f8fafc;
      color: #0f172a;
    }
    @media print {
      @page {
        size: A4 portrait;
        margin: 0 !important;
      }
      body {
        padding: 10mm 12mm 10mm 18mm !important;
        margin: 0 !important;
        box-sizing: border-box !important;
        background-color: #ffffff !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
      }
      .no-print {
        display: none !important;
      }
      .page-card {
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
      }
    }
  </style>
</head>
<body class="py-6 px-4 sm:px-6">

  {{-- Print Controls --}}
  <div class="max-w-4xl mx-auto">
    <x-print.controls title="Official Fee Structure" defaultSize="a4" defaultOrientation="portrait" :backUrl="route('admissions.index')" />
  </div>

  {{-- Watermark --}}
  <x-print.watermark />

  {{-- ── Main Printable Fee Structure Page ─────────────────────── --}}
  <div class="max-w-4xl mx-auto bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden page-card space-y-6 pb-6">

    {{-- Top Curved School Header --}}
    <div class="relative text-white px-8 py-8 flex items-center justify-between overflow-hidden"
         style="background: linear-gradient(135deg, #731E1C 0%, #8C2826 55%, #A83230 100%);">
      <div class="absolute -right-10 -bottom-10 w-48 h-48 rounded-full bg-amber-400/15 blur-2xl"></div>

      <div class="flex items-center gap-5 z-10">
        <div class="w-16 h-16 rounded-2xl bg-white flex items-center justify-center shrink-0 shadow-lg p-1 border border-white/30">
          <img src="{{ $schoolSetting?->getLogoUrl(1) ?: asset('images/school-logo.png') }}" alt="{{ $schoolName }}" class="w-full h-full object-contain" onerror="this.src='{{ asset('images/school-logo.png') }}'">
        </div>

        <div>
          <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white drop-shadow-sm">
            {{ $schoolName }}
          </h1>
          @if($affLine)
          <p class="text-xs sm:text-sm font-semibold text-rose-100 tracking-wider mt-0.5">
            {{ $affLine }}
          </p>
          @endif
        </div>
      </div>

      {{-- Academic Year Pill --}}
      <div class="z-10 bg-white/15 backdrop-blur-md border border-white/25 rounded-2xl px-5 py-2.5 text-center">
        <span class="block text-[10px] font-black tracking-widest text-amber-200 uppercase">ACADEMIC YEAR</span>
        <span class="block text-base font-black text-white font-mono mt-0.5">{{ $academicYear?->name ?? (now()->year . '–' . (now()->year + 1)) }}</span>
      </div>
    </div>

    <div class="px-8 space-y-6">

      {{-- Document Title & Subtitle --}}
      <div class="text-center space-y-1.5 pt-2">
        <div class="inline-flex items-center gap-3 justify-center">
          <span class="h-0.5 w-12 bg-rose-200 rounded-full"></span>
          <h2 class="text-2xl sm:text-3xl font-black text-[#8C2826] uppercase tracking-wider">OFFICIAL FEE SCHEDULE</h2>
          <span class="h-0.5 w-12 bg-rose-200 rounded-full"></span>
        </div>
        <p class="text-base font-extrabold text-slate-800">
          {{ $selectedClass?->name ?? 'Standard' }} ({{ $currentFee['tier'] ?? 'Academic Fee Structure' }})
        </p>
        <p class="text-xs text-slate-500 font-medium max-w-xl mx-auto">
          Official 3-term academic fee schedule for Academic Year {{ $academicYear?->name ?? (now()->year . '–' . (now()->year + 1)) }}.
          All amounts in Indian Rupees (₹).
        </p>
      </div>

      {{-- ── Academic Fee Breakdown Card ── --}}
      <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-5 shadow-xs">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div class="flex items-center gap-3">
            <span class="w-7 h-7 rounded-full bg-[#8C2826] text-white flex items-center justify-center font-bold text-xs">1</span>
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">
              ACADEMIC FEE SCHEDULE — {{ $currentFee['official_name'] ?? ($selectedClass?->name ?? 'STANDARD') }}
            </h3>
          </div>
          <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Per Annum Total: <strong class="text-slate-900">₹{{ number_format($currentFee['annual_total'] ?? 0) }}</strong></span>
        </div>

        <table class="w-full text-sm border-collapse">
          <thead>
            <tr class="bg-slate-50 text-slate-600 font-bold text-xs uppercase border-b border-slate-200">
              <th class="py-2.5 px-3 text-left">Term / Installment</th>
              <th class="py-2.5 px-3 text-center">Due Date</th>
              <th class="py-2.5 px-3 text-right">Tuition Fee (₹)</th>
              <th class="py-2.5 px-3 text-right">Term Amount (₹)</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr>
              <td class="py-3 px-3 font-semibold text-slate-800">Term 1 (At Admission / June)</td>
              <td class="py-3 px-3 text-center text-xs text-slate-500">10 June {{ now()->year }}</td>
              <td class="py-3 px-3 text-right font-mono">₹{{ number_format($currentFee['term1'] ?? 0) }}</td>
              <td class="py-3 px-3 text-right font-bold text-slate-900 font-mono">₹{{ number_format($currentFee['term1'] ?? 0) }}</td>
            </tr>
            <tr>
              <td class="py-3 px-3 font-semibold text-slate-800">Term 2 (Mid-Session / October)</td>
              <td class="py-3 px-3 text-center text-xs text-slate-500">10 October {{ now()->year }}</td>
              <td class="py-3 px-3 text-right font-mono">₹{{ number_format($currentFee['term2'] ?? 0) }}</td>
              <td class="py-3 px-3 text-right font-bold text-slate-900 font-mono">₹{{ number_format($currentFee['term2'] ?? 0) }}</td>
            </tr>
            <tr>
              <td class="py-3 px-3 font-semibold text-slate-800">Term 3 (Final Term / January)</td>
              <td class="py-3 px-3 text-center text-xs text-slate-500">10 January {{ now()->year + 1 }}</td>
              <td class="py-3 px-3 text-right font-mono">₹{{ number_format($currentFee['term3'] ?? 0) }}</td>
              <td class="py-3 px-3 text-right font-bold text-slate-900 font-mono">₹{{ number_format($currentFee['term3'] ?? 0) }}</td>
            </tr>
          </tbody>
          <tfoot>
            <tr class="bg-slate-50 font-bold border-t-2 border-slate-300">
              <td colspan="3" class="py-3 px-3 text-right uppercase text-xs text-slate-600">Total Academic Fee:</td>
              <td class="py-3 px-3 text-right text-base text-[#8C2826] font-mono">₹{{ number_format($currentFee['annual_total'] ?? 0) }}</td>
            </tr>
          </tfoot>
        </table>
      </div>

      {{-- Signatures & Verification --}}
      <x-print.signatures mode="financial" />

      {{-- Thank You Box --}}
      <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/80 flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center shrink-0 shadow-xs">
          <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <div class="space-y-0.5">
          <p class="text-xs font-extrabold text-slate-900">Thank you for choosing {{ $schoolName }}.</p>
          @if($addressLine || $affLine)
          <p class="text-[11px] text-slate-600 font-medium">
            {{ $addressLine }} @if($affLine) &bull; {{ $affLine }} @endif
          </p>
          @endif
        </div>
      </div>

      {{-- ── Footer Contact Bar ───────────────────────────────── --}}
      <div class="pt-4 border-t border-slate-200 text-[10px] text-slate-600 flex flex-wrap items-center justify-between gap-3 font-semibold">
        @if($schoolSetting?->phone)
        <div class="flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
          <span>{{ $schoolSetting->phone }}</span>
        </div>
        @endif
        @if($schoolSetting?->email)
        <div class="flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
          <span>{{ $schoolSetting->email }}</span>
        </div>
        @endif
        @if($schoolSetting?->website)
        <div class="flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
          <span>{{ $schoolSetting->website }}</span>
        </div>
        @endif
        @if($addressLine)
        <div class="flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
          <span>{{ $addressLine }}</span>
        </div>
        @endif
      </div>

    </div>
  </div>

  <x-print.footer />

  <script>
    window.addEventListener('load', () => {
      if (window.location.search.includes('print=true')) {
        setTimeout(() => window.print(), 300);
      }
    });
  </script>
</body>
</html>
