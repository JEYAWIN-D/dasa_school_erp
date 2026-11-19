<!DOCTYPE html>
<html lang="en" x-data="appShell()" x-init="init()">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Dashboard') — {{ \App\Models\SchoolSetting::instance()?->school_name ?? config('app.name', 'Erode Public School') }}</title>
  <link rel="icon" type="image/svg+xml" href="/favicon.svg">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  {{-- Fonts: non-render-blocking async swap (avoids layout shifts on mobile) --}}

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  {{-- Chart.js — deferred, crossorigin for CORS caching benefit --}}
  <link rel="preconnect" href="https://cdn.jsdelivr.net">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js" defer crossorigin="anonymous"></script>
  {{-- Dynamic Theme Styling --}}
  {!! \App\Models\SchoolSetting::getThemeCssVariables() !!}

  <style>
    /* Critical layout & mobile sidebar containment rules */
    #sidebar {
      max-width: 280px !important;
    }
    #sidebar img, .sidebar-brand img {
      max-width: 36px !important;
      max-height: 36px !important;
      width: 36px !important;
      height: 36px !important;
      object-fit: contain !important;
      display: block !important;
    }
    .nav-item svg, #sidebar svg {
      width: 20px !important;
      height: 20px !important;
      min-width: 20px !important;
      min-height: 20px !important;
      max-width: 20px !important;
      max-height: 20px !important;
      flex-shrink: 0 !important;
      display: inline-block !important;
    }

    /* Print Visibility Utilities */
    .print-only {
      display: none !important;
    }

    @media print {
      /* Force print color accuracy & backgrounds */
      * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
      }

      .print-only {
        display: block !important;
      }

      /* Zero page margin suppresses browser default headers/footers (URL, page title, timestamps) */
      @page {
        size: A4 portrait;
        margin: 0mm !important;
      }

      /* Hide application chrome when printing */
      #sidebar,
      header,
      #toast-container,
      #dasa-nav-loader,
      .print\:hidden,
      .no-print {
        display: none !important;
      }

      /* Reset document root & layout wrappers with increased institutional print padding with generous left margin */
      html, body {
        height: auto !important;
        min-height: auto !important;
        overflow: visible !important;
        background: #ffffff !important;
        color: #0f172a !important;
        margin: 0 !important;
        padding: 10mm 12mm 10mm 18mm !important;
        box-sizing: border-box !important;
      }

      .flex.h-screen.overflow-hidden,
      div[class*="h-screen"],
      .flex-1.flex.flex-col.min-w-0.overflow-hidden {
        display: block !important;
        height: auto !important;
        min-height: auto !important;
        overflow: visible !important;
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
      }

      main {
        display: block !important;
        height: auto !important;
        min-height: auto !important;
        overflow: visible !important;
        padding: 0 !important;
        margin: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        background: #ffffff !important;
      }

      /* Text wrapping rules for print */
      p, h1, h2, h3, h4 {
        word-break: break-word !important;
        overflow-wrap: break-word !important;
      }

      /* Printable table optimizations */
      .table-wrap, .overflow-x-auto {
        overflow: visible !important;
        width: 100% !important;
        max-width: 100% !important;
      }

      table {
        width: 100% !important;
        border-collapse: collapse !important;
        table-layout: auto !important;
      }

      th, td {
        word-break: normal !important;
        overflow-wrap: normal !important;
        padding: 4px 6px !important;
      }

      /* Expand all containers to full 100% printable width in print */
      .max-w-xs, .max-w-sm, .max-w-md, .max-w-lg, .max-w-xl, .max-w-2xl, 
      .max-w-3xl, .max-w-4xl, .max-w-5xl, .max-w-6xl, .max-w-7xl,
      div[class*="max-w-"],
      div[class*="pb-20"] {
        max-width: 100% !important;
        width: 100% !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
        padding-bottom: 0 !important;
      }

      /* Flatten cards and remove heavy web card borders, shadows, and huge outer paddings */
      #statement-print-area,
      #receipt-print,
      .card,
      div[class*="rounded-2xl"],
      div[class*="rounded-3xl"] {
        border: none !important;
        box-shadow: none !important;
        border-radius: 0 !important;
        padding: 0 !important;
        margin: 0 !important;
        width: 100% !important;
      }

      /* Compact vertical rhythm in print */
      .space-y-8 > :not([hidden]) ~ :not([hidden]) {
        margin-top: 8px !important;
      }
      .space-y-6 > :not([hidden]) ~ :not([hidden]) {
        margin-top: 6px !important;
      }
      .space-y-4 > :not([hidden]) ~ :not([hidden]) {
        margin-top: 4px !important;
      }
      .mb-5, .my-6 {
        margin-bottom: 6px !important;
        margin-top: 6px !important;
      }
      .mt-8 {
        margin-top: 10px !important;
      }

      .whitespace-nowrap,
      td.whitespace-nowrap,
      th.whitespace-nowrap,
      td.font-mono,
      .nowrap,
      .badge,
      .badge-slate {
        white-space: nowrap !important;
      }

      /* Standard printable tables */
      table thead {
        display: table-header-group !important;
      }
      table tr {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
      }
    }
  </style>

  <script>
    window.setPrintPageSetup = function(size, orientation) {
      size = (size || 'a4').toLowerCase();
      orientation = (orientation || 'portrait').toLowerCase();

      let styleEl = document.getElementById('global-print-page-style');
      if (!styleEl) {
        styleEl = document.createElement('style');
        styleEl.id = 'global-print-page-style';
        document.head.appendChild(styleEl);
      }

      let sizeDecl = 'A4 portrait';
      let marginDecl = '10mm 12mm 10mm 18mm';

      if (size === 'id-card') {
        sizeDecl = orientation === 'landscape' ? '86mm 54mm' : '54mm 86mm';
        marginDecl = '3mm';
      } else {
        const isLandscape = orientation === 'landscape';
        const margins = {
          a5: isLandscape ? '8mm 10mm 8mm 14mm' : '8mm 10mm 8mm 16mm',
          a4: isLandscape ? '10mm 12mm 10mm 16mm' : '10mm 12mm 10mm 18mm',
          a3: isLandscape ? '12mm 14mm 12mm 18mm' : '12mm 14mm 12mm 20mm',
          a2: '14mm 16mm 14mm 22mm',
          a1: '16mm 18mm 16mm 24mm',
        };
        marginDecl = margins[size] || (isLandscape ? '10mm 12mm 10mm 16mm' : '10mm 12mm 10mm 18mm');
        sizeDecl = `${size.toUpperCase()} ${orientation}`;
      }

      // Injects zero-margin @page rule to suppress browser-injected URL, headers, and footers
      styleEl.textContent = `@media print { 
        @page { size: ${sizeDecl}; margin: 0 !important; } 
        body { padding: ${marginDecl} !important; margin: 0 !important; box-sizing: border-box !important; } 
      }`;

      const allSizes = ['paper-a5', 'paper-a4', 'paper-a3', 'paper-a2', 'paper-a1', 'paper-id-card', 'print-id-card', 'portrait', 'landscape'];
      if (document.body) {
        document.body.classList.remove(...allSizes);
        document.body.classList.add(`paper-${size}`, orientation);
      }
    };

    if (typeof window !== 'undefined') {
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
          window.setPrintPageSetup('a4', 'portrait');
        });
      } else {
        window.setPrintPageSetup('a4', 'portrait');
      }
    }
  </script>

  @stack('head')
  @stack('styles')
</head>
<body class="bg-slate-50 font-sans">

@include('partials.mobile-nav-speed')

{{-- ── Toast Notifications ───────────────────────────────── --}}
<div id="toast-container" class="fixed top-3 sm:top-4 right-3 sm:right-4 z-[9999] flex flex-col gap-2 w-[calc(100vw-1.5rem)] sm:w-80 max-w-sm pointer-events-none" x-data="toasts()">
  <template x-for="toast in list" :key="toast.id">
    <div class="bg-white rounded-xl shadow-lg border-l-4 px-4 py-3 flex items-start gap-3 animate-slide-right pointer-events-auto"
         :class="{
           'border-green-500': toast.type==='success',
           'border-red-500':   toast.type==='error',
           'border-amber-500': toast.type==='warning',
           'border-blue-500':  toast.type==='info'
         }">
      <div class="flex-1 min-w-0">
        <p class="text-sm font-semibold text-slate-800 break-words" x-text="toast.title"></p>
        <p class="text-xs text-slate-500 mt-0.5 break-words" x-text="toast.message" x-show="toast.message"></p>
      </div>
      <button @click="remove(toast.id)" class="text-slate-400 hover:text-slate-600 mt-0.5 p-1 flex-shrink-0">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
  </template>
</div>

<div class="flex h-screen overflow-hidden">

  {{-- ── Mobile backdrop ──────────────────────────────────────── --}}
  <div x-show="isMobile && sidebarOpen"
       @click="sidebarOpen = false"
       class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-40 lg:hidden"
       x-transition:enter="transition-opacity duration-300"
       x-transition:enter-start="opacity-0"
       x-transition:enter-end="opacity-100"
       x-transition:leave="transition-opacity duration-300"
       x-transition:leave-start="opacity-100"
       x-transition:leave-end="opacity-0"
       style="display:none">
  </div>

  {{-- ── Sidebar ────────────────────────────────────────────── --}}
  <aside id="sidebar"
         class="fixed lg:relative inset-y-0 left-0 flex-shrink-0 flex flex-col h-full overflow-hidden transition-all duration-300 ease-in-out z-50 lg:z-30 max-w-[85vw] sm:max-w-none -translate-x-full lg:translate-x-0"
         :class="{
           'w-72 translate-x-0 shadow-2xl':      isMobile &&  sidebarOpen,
           'w-72 -translate-x-full':              isMobile && !sidebarOpen,
           'w-64':                               !isMobile &&  sidebarOpen,
           'w-16':                               !isMobile && !sidebarOpen
         }"
         style="background: var(--gradient-sidebar)">

    {{-- Brand Header: Erode Public School & DasaTech --}}
    <div class="flex items-center gap-3 px-3.5 py-3 border-b border-white/10 flex-shrink-0" style="max-height: 64px; overflow: hidden;">
      <div class="w-10 h-10 rounded-xl bg-white/15 p-1 flex items-center justify-center flex-shrink-0 border border-white/20 shadow-xs" style="width: 40px !important; height: 40px !important; min-width: 40px !important; min-height: 40px !important; max-width: 40px !important; max-height: 40px !important; overflow: hidden !important;">
        <img src="{{ asset('images/school-seal-badge.png') }}" class="w-full h-full object-contain" width="32" height="32" style="width: 32px !important; height: 32px !important; max-width: 32px !important; max-height: 32px !important; object-fit: contain !important; display: block !important;" alt="Erode Public School Crest">
      </div>
      <div class="flex-1 min-w-0" x-show="sidebarOpen || isMobile">
        <p class="text-white font-extrabold text-[14px] leading-tight truncate" style="font-family:'Plus Jakarta Sans',sans-serif;">Erode Public School</p>
        <p class="text-amber-300 text-[10.5px] font-bold tracking-wide truncate mt-0.5" style="font-family:'Plus Jakarta Sans',sans-serif;">Senior Secondary &bull; CBSE</p>
        <p class="text-slate-400 text-[10px] font-medium tracking-tight truncate mt-0.5" style="font-family:'Plus Jakarta Sans',sans-serif;">Powered by <span class="text-white/90 font-semibold">DasaTech</span></p>
      </div>
      {{-- Mobile close button --}}
      <button x-show="isMobile" @click="sidebarOpen = false"
              class="lg:hidden flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition"
              title="Close menu">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>


    {{-- Nav — only this scrolls --}}
    <nav class="flex-1 overflow-y-auto overflow-x-hidden py-3 space-y-0.5 min-h-0" style="-ms-overflow-style:none;scrollbar-width:none">
    <style>
      #sidebar nav::-webkit-scrollbar{display:none}
      #sidebar img, .sidebar-brand img {
        max-width: 100% !important;
        max-height: 100% !important;
        object-fit: contain !important;
      }
      .nav-item svg {
        width: 20px !important;
        height: 20px !important;
        min-width: 20px !important;
        min-height: 20px !important;
        max-width: 20px !important;
        max-height: 20px !important;
        flex-shrink: 0 !important;
        display: inline-block !important;
      }
      .nav-group-label {
        display: block;
        padding: 0.25rem 0.75rem;
        margin-top: 1.15rem;
        margin-bottom: 0.25rem;
        font-size: 10.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.09em;
        color: #94A3B8 !important;
      }
    </style>

      @php $currentRoute = request()->route()?->getName() ?? ''; @endphp

      {{-- MAIN --}}
      <p class="nav-group-label">MAIN</p>
      <x-nav-item route="dashboard" icon="home" label="Dashboard" :active="str_starts_with($currentRoute, 'dashboard')" :open="$sidebarOpen ?? true" />

      {{-- ACADEMICS --}}
      @canany(['view admissions','view students','view academics','view attendance','view examinations'])
      <p class="nav-group-label">ACADEMICS</p>
      @can('view admissions')
      <x-nav-item route="admissions.index"     icon="clipboard-document-list" label="Admissions"     :active="str_starts_with($currentRoute, 'admissions.')" :open="$sidebarOpen ?? true" />
      @endcan
      <x-nav-item route="hr.leave-approvals"   icon="shield-check"             label="Approvals Desk" :active="str_starts_with($currentRoute, 'hr.leave-approvals')" :open="$sidebarOpen ?? true" />
      @can('view students')
      @if(!auth()->user()?->hasRole('teacher'))
      <x-nav-item route="students.index"    icon="users"                   label="Students"    :active="str_starts_with($currentRoute, 'students.')"    :open="$sidebarOpen ?? true" />
      @endif
      @endcan
      @can('view academics')
      @if(auth()->user()?->hasRole('class_teacher') || \Illuminate\Support\Facades\DB::table('sections')->where('class_teacher_id', auth()->id())->exists())
      <x-nav-item route="academics.my-class" icon="users"                  label="My Class"    :active="str_starts_with($currentRoute, 'academics.my-class')" :open="$sidebarOpen ?? true" />
      @endif
      {{-- Classes disabled from sidebar: accessible directly inside Academics hub --}}
      {{-- <x-nav-item route="classes.index"     icon="academic-class"          label="Classes"     :active="str_starts_with($currentRoute, 'classes')" :open="$sidebarOpen ?? true" /> --}}
      <x-nav-item route="academics.index"   icon="academic-cap"            label="Academics"   :active="str_starts_with($currentRoute, 'academics') && !str_starts_with($currentRoute, 'academics.my-class')"   :open="$sidebarOpen ?? true" />
      {{-- Staff Timetable moved under HR & Payroll --}}
      @endcan
      {{-- Attendance disabled from sidebar: accessible inside Academics / Dashboard --}}
      {{-- @can('view attendance')
      <x-nav-item route="attendance.index"  icon="calendar"                label="Attendance"  :active="str_starts_with($currentRoute, 'attendance')" :open="$sidebarOpen ?? true" />
      @endcan --}}
      {{-- Examinations disabled from sidebar --}}
      {{-- @can('view examinations')
      <x-nav-item route="examinations.index" icon="document-text"          label="Examinations" :active="str_starts_with($currentRoute, 'examinations')" :open="$sidebarOpen ?? true" />
      @endcan --}}
      @endcanany

      {{-- FINANCE --}}
      @canany(['view fees', 'view employees'])
      <p class="nav-group-label">FINANCE</p>
      @can('view fees')
      <x-nav-item route="fees.index"     icon="banknotes"      label="Fee Management"     :active="str_starts_with($currentRoute, 'fees')"     :open="$sidebarOpen ?? true" />
      <x-nav-item route="accounts.index" icon="calculator"     label="Account Management" :active="str_starts_with($currentRoute, 'accounts')" :open="$sidebarOpen ?? true" />
      @endcan
      @can('view employees')
      <x-nav-item route="hr.index"      icon="identification" label="HR & Payroll"   :active="str_starts_with($currentRoute, 'hr') || str_starts_with($currentRoute, 'staff-timetable') || str_starts_with($currentRoute, 'my-timetable')" :open="$sidebarOpen ?? true" />
      @endcan
      @endcanany

      {{-- ADMINISTRATION --}}
      @canany(['view library','view transport','view inventory','manage settings'])
      <p class="nav-group-label">ADMINISTRATION</p>
      @can('view library')
      <x-nav-item route="library.index"   icon="book-open"   label="Library"    :active="str_starts_with($currentRoute, 'library')"   :open="$sidebarOpen ?? true" />
      @endcan
      @can('view transport')
      <x-nav-item route="transport.index" icon="truck"        label="Transport"  :active="str_starts_with($currentRoute, 'transport')" :open="$sidebarOpen ?? true" />
      @endcan
      @can('view inventory')
      <x-nav-item route="warehouse.index" icon="archive-box"  label="Warehouse"  :active="str_starts_with($currentRoute, 'warehouse') || str_starts_with($currentRoute, 'inventory')" :open="$sidebarOpen ?? true" />
      @endcan
      <x-nav-item route="settings.books-notes.index" icon="book-open" label="Books, Notes & Uniform" :active="str_starts_with($currentRoute, 'settings.books-notes') || str_starts_with($currentRoute, 'books-notes')" :open="$sidebarOpen ?? true" />
      @endcanany

      {{-- ENGAGEMENT --}}
      @canany(['view events','view gate'])
      <p class="nav-group-label">ENGAGEMENT</p>
      @can('view events')
      <x-nav-item route="events.index"  icon="calendar"       label="Events"    :active="str_starts_with($currentRoute, 'events')"  :open="$sidebarOpen ?? true" />
      @endcan
      @can('view gate')
      <x-nav-item route="gate.index"     icon="user-plus"     label="Gate/Visitors" :active="str_starts_with($currentRoute, 'gate.')" :open="$sidebarOpen ?? true" />
      @endcan
      @endcanany

      {{-- ACCESS CONTROL (RBAC) --}}
      @if(auth()->user()?->isSuperAdmin() || auth()->user()?->hasAnyRole(['super_admin', 'admin']) || auth()->user()?->can('manage rbac'))
      <p class="nav-group-label">ACCESS CONTROL (RBAC)</p>
      <x-nav-item route="rbac.users.index" icon="users" label="User Accounts" :active="str_starts_with($currentRoute, 'rbac.users')" :open="$sidebarOpen ?? true" />
      <x-nav-item route="rbac.roles.index" icon="identification" label="Role Management" :active="str_starts_with($currentRoute, 'rbac.roles')" :open="$sidebarOpen ?? true" />
      <x-nav-item route="rbac.matrix.index" icon="shield-check" label="Permission Matrix" :active="str_starts_with($currentRoute, 'rbac.matrix')" :open="$sidebarOpen ?? true" />
      <x-nav-item route="rbac.config.targets" icon="cog-6-tooth" label="Account Targets" :active="str_starts_with($currentRoute, 'rbac.config')" :open="$sidebarOpen ?? true" />
      <x-nav-item route="rbac.audit.index" icon="document-text" label="RBAC Audit Log" :active="str_starts_with($currentRoute, 'rbac.audit')" :open="$sidebarOpen ?? true" />
      @endif

      {{-- SYSTEM --}}
      @canany(['view reports','view audit logs','manage settings'])
      <p class="nav-group-label">SYSTEM</p>
      @can('view reports')
      <x-nav-item route="reports.index"   icon="chart-bar"     label="Reports"    :active="str_starts_with($currentRoute, 'reports')"  :open="$sidebarOpen ?? true" />
      @endcan
      @canany(['view audit logs','manage settings'])
      <x-nav-item route="system.audit-log" icon="shield-check" label="Audit/Security" :active="str_starts_with($currentRoute, 'system')" :open="$sidebarOpen ?? true" />
      @endcanany
      @can('manage settings')
      <x-nav-item route="id-cards.settings" icon="cog-6-tooth" label="ID Card Settings" :active="str_starts_with($currentRoute, 'id-cards.settings')" :open="$sidebarOpen ?? true" />
      <x-nav-item route="id-cards.audit-logs" icon="document-text" label="ID Card Audit Logs" :active="str_starts_with($currentRoute, 'id-cards.audit-logs')" :open="$sidebarOpen ?? true" />
      <x-nav-item route="settings.index"  icon="cog-6-tooth"   label="Settings"   :active="$currentRoute === 'settings.index' || str_starts_with($currentRoute, 'settings.save')" :open="$sidebarOpen ?? true" />
      @endcan
      @endcanany

    </nav>

    {{-- Help widget --}}
    <div class="flex-shrink-0 px-3 pb-2"
         x-show="sidebarOpen || isMobile"
         x-transition:enter="transition-opacity duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         style="display:none">
      <div class="px-3 py-2.5 rounded-xl" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08)">
        <a href="mailto:support@dcinnovision.com"
           class="flex items-center gap-2 text-slate-400 hover:text-white transition-colors text-xs font-medium mb-1.5">
          <svg class="w-3.5 h-3.5 text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          Help & Support
        </a>
        <p class="text-slate-300 text-[10.5px] font-medium flex items-center justify-between pt-1 border-t border-white/10" style="font-family:'Plus Jakarta Sans',sans-serif;">
          <span>DasaTech EduERP</span>
          <span class="text-amber-300 font-semibold text-[10px]">v2.6 Enterprise</span>
        </p>
      </div>
    </div>

    {{-- Chevron toggle tab — desktop only --}}
    <button x-show="!isMobile" @click="sidebarOpen = !sidebarOpen"
            class="hidden lg:flex absolute -right-4 top-1/2 -translate-y-1/2 w-8 h-8 bg-white border border-slate-200 rounded-full items-center justify-center z-40 group transition-all duration-200 hover:border-indigo-300 hover:bg-indigo-50"
            style="box-shadow:0 2px 8px rgba(0,0,0,0.15),0 0 0 1px rgba(255,255,255,0.8)"
            :title="sidebarOpen ? 'Collapse sidebar' : 'Expand sidebar'">
      <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-indigo-600 transition-all duration-300"
           :class="sidebarOpen ? '' : 'rotate-180'"
           fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
      </svg>
    </button>

  </aside>

  {{-- ── Main area ──────────────────────────────────────────── --}}
  <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

    {{-- Topbar --}}
    <header class="h-14 bg-white border-b border-slate-200 flex items-center px-2.5 sm:px-4 md:px-6 gap-2 sm:gap-3 flex-shrink-0 z-20">

      {{-- Mobile hamburger --}}
      <button @click="sidebarOpen = true" class="lg:hidden btn-icon flex-shrink-0 w-9 h-9" title="Open navigation menu">
        <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>

      {{-- Desktop sidebar toggle --}}
      <button @click="sidebarOpen = !sidebarOpen" class="hidden lg:flex btn-icon flex-shrink-0" :title="sidebarOpen ? 'Collapse' : 'Expand'">
        <svg class="w-5 h-5 transition-transform duration-300" :class="sidebarOpen ? '' : 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7M21 19l-7-7 7-7"/>
        </svg>
      </button>

      {{-- Back Button & Breadcrumb --}}
      <div class="flex-1 min-w-0 flex items-center gap-2 text-xs text-slate-400 overflow-hidden">
        <button onclick="window.history.back()" type="button" class="flex-shrink-0 inline-flex items-center gap-1.5 px-2 sm:px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition text-xs border border-slate-200 shadow-xs cursor-pointer" title="Go to previous page">
          <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
          </svg>
          <span class="hidden xs:inline">Back</span>
        </button>
        @hasSection('breadcrumb')
          <span class="hidden sm:inline flex-shrink-0">/</span>
          <div class="hidden sm:flex items-center gap-1.5 min-w-0 truncate">@yield('breadcrumb')</div>
        @endif
      </div>

      {{-- Right actions --}}
      <div class="flex items-center gap-1.5 sm:gap-2 flex-shrink-0" x-data="{ notifOpen: false, topUserOpen: false }">

        {{-- Live Clock --}}
        <div x-data="{
                t: '',
                d: '',
                tick() {
                    const now = new Date();
                    let h = now.getHours(), m = now.getMinutes(), s = now.getSeconds();
                    const ampm = h >= 12 ? 'PM' : 'AM';
                    h = h % 12 || 12;
                    this.t = (h < 10 ? '0'+h : h) + ':' + (m < 10 ? '0'+m : m) + ':' + (s < 10 ? '0'+s : s) + ' ' + ampm;
                    const days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
                    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                    this.d = days[now.getDay()] + ', ' + months[now.getMonth()] + ' ' + now.getDate();
                }
             }"
             x-init="tick(); setInterval(() => tick(), 1000)"
             class="hidden md:flex items-center gap-2 px-2.5 py-1 rounded-xl bg-slate-50 border border-slate-200 shadow-2xs">
          <svg class="w-3.5 h-3.5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <div class="flex flex-col leading-none">
            <span class="text-[11px] font-bold text-slate-800 tracking-wide tabular-nums" x-text="t">--:--:-- --</span>
            <span class="text-[9px] text-slate-400 font-medium mt-0.5" x-text="d"></span>
          </div>
        </div>

        {{-- Academic Year badge --}}
        <span class="badge-blue text-[11px] sm:text-xs cursor-default hidden xs:inline-flex" title="Academic Year">2025-2026</span>

        {{-- Role badge --}}
        @php $roleName = auth()->user()?->getRoleNames()?->first(); @endphp
        @if($roleName)
          <span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-indigo-50 text-indigo-700 border border-indigo-200 capitalize">
            {{ str_replace('_', ' ', $roleName) }}
          </span>
        @endif

        {{-- Notifications Dropdown --}}
        <div class="relative" @click.outside="notifOpen = false">
          <button @click="notifOpen = !notifOpen; if(notifOpen) topUserOpen = false" class="btn-icon relative w-9 h-9" title="Notifications">
            <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
          </button>

          {{-- Notification Box --}}
          <div x-show="notifOpen"
               x-transition:enter="transition ease-out duration-150"
               x-transition:enter-start="opacity-0 scale-95"
               x-transition:enter-end="opacity-100 scale-100"
               x-transition:leave="transition ease-in duration-100"
               x-transition:leave-start="opacity-100 scale-100"
               x-transition:leave-end="opacity-0 scale-95"
               class="absolute right-0 top-full mt-2 z-50 w-[calc(100vw-1.5rem)] sm:w-80 max-w-sm bg-white rounded-xl shadow-2xl border border-slate-200 overflow-hidden max-h-[80vh] flex flex-col"
               style="display:none">
            <div class="px-4 py-3 bg-slate-50 border-b border-slate-100 flex items-center justify-between flex-shrink-0">
              <span class="text-xs font-bold text-slate-800 uppercase tracking-wider">Notifications</span>
              <span class="badge-blue text-[10px]">3 New</span>
            </div>
            <div class="divide-y divide-slate-100 overflow-y-auto max-h-72">
              <a href="{{ route('communication.index') }}" class="block p-3 hover:bg-slate-50 transition">
                <p class="text-xs font-semibold text-slate-800">Fee Collection Summary</p>
                <p class="text-[11px] text-slate-500 mt-0.5">Today's collection report is ready.</p>
                <span class="text-[10px] text-slate-400 mt-1 block">5 minutes ago</span>
              </a>
              <a href="{{ route('attendance.index') }}" class="block p-3 hover:bg-slate-50 transition">
                <p class="text-xs font-semibold text-slate-800">Attendance Alert</p>
                <p class="text-[11px] text-slate-500 mt-0.5">Class 10-A attendance marked successfully.</p>
                <span class="text-[10px] text-slate-400 mt-1 block">1 hour ago</span>
              </a>
              <a href="{{ route('admissions.index') }}" class="block p-3 hover:bg-slate-50 transition">
                <p class="text-xs font-semibold text-slate-800">New Admission Enquiry</p>
                <p class="text-[11px] text-slate-500 mt-0.5">Parent submitted a new enquiry for Class 1.</p>
                <span class="text-[10px] text-slate-400 mt-1 block">2 hours ago</span>
              </a>
            </div>
            <div class="p-2.5 bg-slate-50 border-t border-slate-100 text-center flex-shrink-0">
              <a href="{{ route('communication.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">View All Communications &rarr;</a>
            </div>
          </div>
        </div>

        {{-- Topbar User Avatar Dropdown (With Log Out) --}}
        @auth
        <div class="relative" @click.outside="topUserOpen = false">
          <button type="button"
                  @click="topUserOpen = !topUserOpen; if(topUserOpen) notifOpen = false"
                  class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center cursor-pointer ring-2 ring-indigo-100 hover:ring-indigo-300 transition-all select-none flex-shrink-0 focus:outline-hidden"
                  title="{{ auth()->user()?->name }} — Click for menu">
            <span class="text-white text-xs font-bold">{{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 2)) }}</span>
          </button>

          {{-- User Menu Box --}}
          <div x-show="topUserOpen"
               x-transition:enter="transition ease-out duration-150"
               x-transition:enter-start="opacity-0 scale-95"
               x-transition:enter-end="opacity-100 scale-100"
               x-transition:leave="transition ease-in duration-100"
               x-transition:leave-start="opacity-100 scale-100"
               x-transition:leave-end="opacity-0 scale-95"
               class="absolute right-0 top-full mt-2 z-50 w-[calc(100vw-1.5rem)] xs:w-64 max-w-xs bg-white rounded-xl shadow-2xl border border-slate-200 overflow-hidden"
               style="display:none">
            <div class="px-4 py-3 bg-slate-50 border-b border-slate-100">
              <p class="text-xs font-bold text-slate-900 truncate">{{ auth()->user()?->name }}</p>
              <p class="text-xs text-slate-500 truncate mt-0.5">{{ auth()->user()?->email }}</p>
              <span class="inline-block mt-1.5 px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded text-[10px] font-bold capitalize">
                {{ str_replace('_', ' ', $roleName ?? 'User') }}
              </span>
            </div>

            <div class="py-1">
              <a href="{{ route('settings.index') }}"
                 class="flex items-center gap-2.5 px-3.5 py-2.5 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                My Profile
              </a>
              <a href="{{ route('settings.index') }}"
                 class="flex items-center gap-2.5 px-3.5 py-2.5 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Settings
              </a>
            </div>

            {{-- Log Out Option --}}
            <div class="border-t border-slate-100 py-1">
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-2.5 px-3.5 py-2.5 text-xs font-semibold text-red-600 hover:bg-red-50 transition cursor-pointer">
                  <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                  </svg>
                  Sign Out / Log Out
                </button>
              </form>
            </div>
          </div>
        </div>
        @else
        <a href="{{ route('login') }}" class="btn-primary text-xs px-3 py-1.5 font-semibold">Sign In</a>
        @endauth

      </div>
    </header>

    {{-- Page content --}}
    <main class="flex-1 overflow-y-auto overflow-x-hidden p-3 sm:p-4 md:p-6 3xl:p-8 pb-safe page-enter">
      <div class="w-full max-w-[2400px] mx-auto min-w-0">

      {{-- Flash messages --}}
      @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition class="alert-success mb-4 sm:mb-5 flex items-center justify-between gap-3 shadow-2xs">
          <div class="flex items-center gap-2.5 min-w-0">
            <svg class="w-5 h-5 flex-shrink-0 text-emerald-600 cursor-pointer pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span class="break-words text-sm font-medium">{{ session('success') }}</span>
          </div>
          <button type="button" @click="show = false" onclick="this.closest('.alert-success').style.display='none'; this.closest('.alert-success').remove();" class="text-emerald-700 hover:text-emerald-900 text-lg font-bold leading-none p-1 rounded-lg hover:bg-emerald-100/60 transition cursor-pointer" title="Dismiss" aria-label="Close">&times;</button>
        </div>
      @endif
      @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-transition class="alert-error mb-4 sm:mb-5 flex items-center justify-between gap-3 shadow-2xs">
          <div class="flex items-center gap-2.5 min-w-0">
            <button type="button" @click="show = false" onclick="this.closest('.alert-error').style.display='none'; this.closest('.alert-error').remove();" class="text-rose-600 hover:text-rose-900 flex-shrink-0 cursor-pointer p-0.5 rounded hover:bg-rose-100 transition" title="Dismiss" aria-label="Close">
              <svg class="w-5 h-5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <span class="break-words text-sm font-semibold text-rose-900">{{ session('error') }}</span>
          </div>
          <button type="button" @click="show = false" onclick="this.closest('.alert-error').style.display='none'; this.closest('.alert-error').remove();" class="text-rose-600 hover:text-rose-900 text-xl font-bold leading-none p-1 rounded-lg hover:bg-rose-100/60 transition cursor-pointer shrink-0" title="Dismiss" aria-label="Close">&times;</button>
        </div>
      @endif

      @yield('content')
      </div>
    </main>

  </div>
</div>

<script>
function appShell() {
  const isMob = typeof window !== 'undefined' ? (window.innerWidth < 1024) : false;
  let initialOpen = !isMob;
  if (!isMob) {
    try {
      const stored = localStorage.getItem('sidebar_open');
      if (stored !== null) initialOpen = JSON.parse(stored);
    } catch(e) {}
  }
  return {
    isMobile: isMob,
    sidebarOpen: initialOpen,
    init() {
      if (this.isMobile) {
        this.sidebarOpen = false;
      }

      const onResize = () => {
        const mobile = window.innerWidth < 1024;
        if (mobile !== this.isMobile) {
          this.isMobile = mobile;
          this.sidebarOpen = !mobile;
        }
      };
      window.addEventListener('resize', onResize);

      this.$watch('sidebarOpen', open => {
        if (!this.isMobile) {
          try {
            localStorage.setItem('sidebar_open', JSON.stringify(open));
          } catch(e) {}
        } else {
          document.body.style.overflow = open ? 'hidden' : '';
        }
      });
    }
  }
}
function toasts() {
  return {
    list: [],
    add(type, title, message = '') {
      const id = Date.now()
      this.list.push({ id, type, title, message })
      setTimeout(() => this.remove(id), 4000)
    },
    remove(id) { this.list = this.list.filter(t => t.id !== id) }
  }
}
</script>
@stack('scripts')
</body>
</html>
