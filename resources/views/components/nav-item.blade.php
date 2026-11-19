@props(['route', 'icon', 'label', 'active' => false, 'open' => true])

@php
  try { $url = route($route); } catch (\Exception $e) { $url = '#'; }
@endphp

<a href="{{ $url }}"
   class="nav-item {{ $active ? 'active' : '' }}"
   title="{{ $label }}">

  {{-- Icon --}}
  <span class="w-5 h-5 min-w-[20px] min-h-[20px] max-w-[20px] max-h-[20px] flex items-center justify-center flex-shrink-0" style="width: 20px !important; height: 20px !important; min-width: 20px !important; min-height: 20px !important;">
    @includeFirst(['components.icons.' . $icon, 'components.icons.academic-cap'], ['class' => 'w-5 h-5 flex-shrink-0'])
  </span>

  {{-- Label --}}
  <span class="truncate transition-all duration-200" :class="sidebarOpen ? 'opacity-100 w-auto' : 'opacity-0 w-0 overflow-hidden'">
    {{ $label }}
  </span>
</a>
