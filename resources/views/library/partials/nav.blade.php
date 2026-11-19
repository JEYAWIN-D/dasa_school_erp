{{-- Enterprise Library Sub-Navigation Bar --}}
<div class="bg-white border border-slate-200 rounded-xl shadow-sm mb-6 p-2 overflow-x-auto">
  <nav class="flex items-center gap-1 min-w-max text-sm font-medium">
    <a href="{{ route('library.index') }}" 
       class="px-3.5 py-2 rounded-lg flex items-center gap-2 transition {{ request()->routeIs('library.index') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
      Dashboard
    </a>

    <a href="{{ route('library.books') }}" 
       class="px-3.5 py-2 rounded-lg flex items-center gap-2 transition {{ request()->routeIs('library.books*') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
      Catalogue & Copies
    </a>

    <a href="{{ route('library.issue') }}" 
       class="px-3.5 py-2 rounded-lg flex items-center gap-2 transition {{ request()->routeIs('library.issue*') || request()->routeIs('library.currently-issued') || request()->routeIs('library.overdue*') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
      Circulation Desk
    </a>

    <a href="{{ route('library.racks.index') }}" 
       class="px-3.5 py-2 rounded-lg flex items-center gap-2 transition {{ request()->routeIs('library.racks*') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
      Racks & Shelves
    </a>

    <a href="{{ route('library.procurement.index') }}" 
       class="px-3.5 py-2 rounded-lg flex items-center gap-2 transition {{ request()->routeIs('library.procurement*') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
      Procurement & POs
    </a>

    <a href="{{ route('library.periodicals.index') }}" 
       class="px-3.5 py-2 rounded-lg flex items-center gap-2 transition {{ request()->routeIs('library.periodicals*') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
      Periodicals & Papers
    </a>

    <a href="{{ route('library.assets.index') }}" 
       class="px-3.5 py-2 rounded-lg flex items-center gap-2 transition {{ request()->routeIs('library.assets*') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
      Assets
    </a>

    <a href="{{ route('library.wastage.index') }}" 
       class="px-3.5 py-2 rounded-lg flex items-center gap-2 transition {{ request()->routeIs('library.wastage*') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
      Wastage & Write-offs
    </a>

    <a href="{{ route('library.stock-verification.index') }}" 
       class="px-3.5 py-2 rounded-lg flex items-center gap-2 transition {{ request()->routeIs('library.stock-verification*') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
      Stock Audit
    </a>

    <a href="{{ route('library.reports') }}" 
       class="px-3.5 py-2 rounded-lg flex items-center gap-2 transition {{ request()->routeIs('library.reports*') || request()->routeIs('library.fine-report') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
      Reports & Analytics
    </a>

    <a href="{{ route('library.settings') }}" 
       class="px-3.5 py-2 rounded-lg flex items-center gap-2 transition {{ request()->routeIs('library.settings*') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
      Settings
    </a>
  </nav>
</div>

