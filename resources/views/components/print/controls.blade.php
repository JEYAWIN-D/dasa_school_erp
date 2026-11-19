@props([
    'defaultSize' => 'a4',
    'defaultOrientation' => 'portrait',
    'title' => 'Print Document',
    'backUrl' => null,
])

<div x-data="enterprisePrintToolbar('{{ $defaultSize }}', '{{ $defaultOrientation }}')" x-init="init()"
     class="no-print sticky top-3 z-50 mb-6 bg-slate-900/95 backdrop-blur-md text-white rounded-2xl p-3 sm:px-5 sm:py-3.5 shadow-xl border border-slate-700/80 flex flex-wrap items-center justify-between gap-4 select-none">
  
  {{-- Left: Status & Controls --}}
  <div class="flex items-center gap-4 flex-wrap">
    <div class="flex items-center gap-2">
      <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
      <span class="text-xs font-black tracking-wide uppercase text-slate-200">{{ $title }}</span>
    </div>

    <div class="h-4 w-px bg-slate-700 hidden sm:block"></div>

    {{-- Paper Size Selection --}}
    <div class="flex items-center gap-1.5 text-xs">
      <label for="page-size-selector" class="text-slate-400 font-medium text-[11px]">Paper:</label>
      <select id="page-size-selector" x-model="paperSize" @change="applyLayout()"
              class="bg-slate-800 border border-slate-600 rounded-lg px-2.5 py-1 text-xs text-white outline-none cursor-pointer hover:border-slate-500 transition">
        <option value="a4">A4 (210 × 297 mm)</option>
        <option value="a3">A3 (297 × 420 mm)</option>
        <option value="a5">A5 (148 × 210 mm)</option>
        <option value="legal">Legal (8.5 × 14 in)</option>
        <option value="letter">Letter (8.5 × 11 in)</option>
        <option value="thermal">Thermal POS (80mm)</option>
      </select>
    </div>

    {{-- Orientation Toggle --}}
    <div class="flex items-center gap-1 bg-slate-800 p-0.5 rounded-lg border border-slate-700 text-xs">
      <button type="button" @click="orientation = 'portrait'; applyLayout()"
              :class="orientation === 'portrait' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-400 hover:text-white'"
              class="px-2.5 py-1 rounded-md text-[11px] transition cursor-pointer">
        Portrait
      </button>
      <button type="button" @click="orientation = 'landscape'; applyLayout()"
              :class="orientation === 'landscape' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-400 hover:text-white'"
              class="px-2.5 py-1 rounded-md text-[11px] transition cursor-pointer">
        Landscape
      </button>
    </div>

    {{-- Margin & Left-Padding Control --}}
    <div class="flex items-center gap-1.5 text-xs">
      <label for="page-margin-selector" class="text-slate-400 font-medium text-[11px]">Margin (Left):</label>
      <select id="page-margin-selector" x-model="marginPreset" @change="applyLayout()"
              class="bg-slate-800 border border-slate-600 rounded-lg px-2.5 py-1 text-xs text-white outline-none cursor-pointer hover:border-slate-500 transition">
        <option value="spacious">Spacious (Left: 18mm)</option>
        <option value="wide-left">Binding Punch (Left: 24mm)</option>
        <option value="standard">Standard (Left: 14mm)</option>
        <option value="compact">Compact (Left: 10mm)</option>
      </select>
    </div>

    {{-- Watermark Toggle --}}
    <label class="flex items-center gap-1.5 text-xs text-slate-300 cursor-pointer">
      <input type="checkbox" x-model="showWatermark" @change="toggleWatermark()" class="rounded bg-slate-800 border-slate-600 text-indigo-500 focus:ring-0">
      <span class="text-[11px]">Watermark</span>
    </label>
  </div>

  {{-- Right: Actions --}}
  <div class="flex items-center gap-2">
    @if($backUrl)
      <a href="{{ $backUrl }}" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold inline-flex items-center gap-1.5 border border-slate-700 transition cursor-pointer">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        <span>Back</span>
      </a>
    @else
      <button type="button" onclick="window.history.length > 1 ? window.history.back() : window.close()" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold inline-flex items-center gap-1.5 border border-slate-700 transition cursor-pointer">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        <span>Close</span>
      </button>
    @endif

    <button type="button" @click="window.print()" class="px-4 py-1.5 rounded-xl bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white text-xs font-extrabold shadow-sm inline-flex items-center gap-1.5 cursor-pointer transition">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
      <span>Print / PDF</span>
    </button>
  </div>
</div>

<script>
function enterprisePrintToolbar(initialSize, initialOrientation) {
  return {
    paperSize: initialSize || 'a4',
    orientation: initialOrientation || 'portrait',
    marginPreset: 'spacious', // Default to generous left padding
    showWatermark: true,
    init() {
      this.applyLayout();
    },
    toggleWatermark() {
      const watermarks = document.querySelectorAll('.school-print-watermark');
      watermarks.forEach(wm => {
        wm.style.display = this.showWatermark ? 'block' : 'none';
      });
    },
    applyLayout() {
      let styleTag = document.getElementById('enterprise-print-page-style');
      if (!styleTag) {
        styleTag = document.createElement('style');
        styleTag.id = 'enterprise-print-page-style';
        document.head.appendChild(styleTag);
      }

      let pageSizeStr = `${(this.paperSize || 'a4').toUpperCase()} ${this.orientation}`;
      const isLandscape = this.orientation === 'landscape';

      // Base margins with extra left padding for hole punching, binding, and readability
      let top = 10, right = 12, bottom = 10, left = 18;

      if (this.marginPreset === 'wide-left') {
        top = 10; right = 12; bottom = 10; left = isLandscape ? 20 : 24;
      } else if (this.marginPreset === 'standard') {
        top = 8; right = 10; bottom = 8; left = isLandscape ? 12 : 14;
      } else if (this.marginPreset === 'compact') {
        top = 6; right = 8; bottom = 6; left = isLandscape ? 8 : 10;
      } else {
        // 'spacious' (default)
        top = 10; right = 12; bottom = 10; left = isLandscape ? 16 : 18;
      }

      if (this.paperSize === 'a3') {
        pageSizeStr = `A3 ${this.orientation}`;
        top = Math.round(top * 1.2);
        right = Math.round(right * 1.2);
        bottom = Math.round(bottom * 1.2);
        left = Math.round(left * 1.2);
      } else if (this.paperSize === 'a5') {
        pageSizeStr = `A5 ${this.orientation}`;
        top = Math.max(6, top - 2);
        right = Math.max(8, right - 2);
        bottom = Math.max(6, bottom - 2);
        left = Math.max(12, left - 4);
      } else if (this.paperSize === 'legal') {
        pageSizeStr = `legal ${this.orientation}`;
      } else if (this.paperSize === 'letter') {
        pageSizeStr = `letter ${this.orientation}`;
      } else if (this.paperSize === 'thermal') {
        pageSizeStr = '80mm auto';
        top = 3; right = 3; bottom = 3; left = 4;
      }

      const marginStr = `${top}mm ${right}mm ${bottom}mm ${left}mm`;

      styleTag.textContent = `
        @media print {
          @page {
            size: ${pageSizeStr};
            margin: 0 !important;
          }
          body {
            padding: ${marginStr} !important;
            margin: 0 !important;
            box-sizing: border-box !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            background: #ffffff !important;
          }
          .no-print {
            display: none !important;
          }
        }
      `;
    }
  };
}
</script>
