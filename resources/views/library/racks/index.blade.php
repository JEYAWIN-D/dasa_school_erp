@extends('layouts.app')
@section('title', 'Library Spatial Racks & Shelves')

@section('content')
<div class="space-y-6">

  {{-- Header --}}
  <div class="flex items-center justify-between flex-wrap gap-4">
    <div>
      <h1 class="page-title text-2xl font-bold text-slate-900">Physical Location Master</h1>
      <p class="page-subtitle text-slate-500 text-sm mt-0.5">Hierarchy: Building → Floor → Room → Section → Rack → Shelf</p>
    </div>
  </div>

  {{-- Sub-Navigation --}}
  @include('library.partials.nav')

  @if(session('success'))
    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">
      {{ session('success') }}
    </div>
  @endif

  {{-- Add New Rack Form --}}
  <div class="card p-5 border-l-4 border-l-primary-600" x-data="{ open: false }">
    <div class="flex items-center justify-between">
      <div>
        <h3 class="font-bold text-slate-900 text-sm">Add Library Rack</h3>
        <p class="text-xs text-slate-500">Create a physical book rack with capacity limits.</p>
      </div>
      <button type="button" @click="open = !open" class="btn btn-secondary btn-sm" x-text="open ? 'Close' : '+ New Rack'"></button>
    </div>

    <form x-show="open" x-transition action="{{ route('library.racks.store') }}" method="POST" class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
      @csrf
      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Rack Number / Identifier *</label>
        <input type="text" name="rack_number" placeholder="e.g. RCK-SCI-01" class="input w-full text-sm" required>
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Rack Name / Title *</label>
        <input type="text" name="name" placeholder="e.g. Science & Physics" class="input w-full text-sm" required>
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Building & Floor</label>
        <div class="flex gap-2">
          <input type="text" name="building" value="Main Campus" class="input w-1/2 text-sm" placeholder="Building">
          <input type="text" name="floor" value="1st Floor" class="input w-1/2 text-sm" placeholder="Floor">
        </div>
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Room / Section</label>
        <div class="flex gap-2">
          <input type="text" name="room" value="Central Library" class="input w-1/2 text-sm" placeholder="Room">
          <input type="text" name="section" placeholder="Section" class="input w-1/2 text-sm">
        </div>
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Dedicated Category</label>
        <select name="category_id" class="input w-full text-sm">
          <option value="">General (Any Category)</option>
          @foreach($categories as $cat)
          <option value="{{ $cat->id }}">{{ $cat->name }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Total Capacity (Books)</label>
        <input type="number" name="capacity" value="100" min="1" class="input w-full text-sm" required>
      </div>

      <div class="sm:col-span-2 flex items-end gap-2">
        <input type="text" name="remarks" placeholder="Optional location notes or directions" class="input flex-1 text-sm">
        <button type="submit" class="btn btn-primary btn-sm flex-shrink-0">Create Rack</button>
      </div>
    </form>
  </div>

  {{-- Racks Grid --}}
  <div class="space-y-4">
    @forelse($racks as $rack)
    <div class="card p-5 space-y-4">
      <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 font-bold flex items-center justify-center text-sm border border-primary-100">
            {{ $rack->rack_number }}
          </div>
          <div>
            <h3 class="font-bold text-slate-900 text-sm">{{ $rack->name }}</h3>
            <p class="text-xs text-slate-500">
              {{ $rack->building }} · {{ $rack->floor }} · {{ $rack->room }} {{ $rack->section ? "· {$rack->section}" : '' }}
              @if($rack->category)
              <span class="ml-1 badge bg-indigo-50 text-indigo-700 text-[10px] font-semibold">{{ $rack->category->name }}</span>
              @endif
            </p>
          </div>
        </div>

        <div class="flex items-center gap-4 text-xs font-medium text-slate-600">
          <div>
            <span>Occupancy: </span>
            <strong class="text-slate-800">{{ $rack->currentOccupancy() }} / {{ $rack->capacity }}</strong> books
          </div>
          <div class="w-24 bg-slate-100 rounded-full h-2 overflow-hidden">
            @php
              $occPct = $rack->capacity > 0 ? min(100, round(($rack->currentOccupancy() / $rack->capacity) * 100)) : 0;
            @endphp
            <div class="bg-primary-600 h-2 rounded-full" style="width: {{ $occPct }}%;"></div>
          </div>
        </div>
      </div>

      {{-- Shelves in this Rack --}}
      <div class="pt-3 border-t border-slate-100">
        <div class="flex items-center justify-between mb-2.5">
          <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500">Shelves ({{ $rack->shelves->count() }})</h4>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
          @foreach($rack->shelves as $shelf)
          <div class="p-3 rounded-lg border border-slate-200 bg-slate-50/50 flex flex-col justify-between gap-2">
            <div>
              <div class="flex items-center justify-between">
                <span class="font-mono text-xs font-bold text-primary-600">Shelf {{ $shelf->shelf_number }}</span>
                <span class="text-[10px] text-slate-400 font-medium">{{ $shelf->copies->count() }} / {{ $shelf->capacity }}</span>
              </div>
              @if($shelf->name || $shelf->subject)
              <p class="text-xs text-slate-700 font-medium truncate mt-0.5">{{ $shelf->name ?: $shelf->subject }}</p>
              @endif
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-[11px]">
              <a href="{{ route('library.shelves.copies', $shelf->id) }}" class="text-primary-600 font-medium hover:underline">
                View Copies &rarr;
              </a>
            </div>
          </div>
          @endforeach

          {{-- Add Shelf Card --}}
          <div class="p-3 rounded-lg border border-dashed border-slate-300 bg-white" x-data="{ openShelf: false }">
            <button type="button" @click="openShelf = !openShelf" class="w-full text-center text-xs font-medium text-slate-600 hover:text-primary-600 py-2" x-show="!openShelf">
              + Add Shelf
            </button>
            <form x-show="openShelf" style="display: none;" action="{{ route('library.racks.shelves.store', $rack->id) }}" method="POST" class="space-y-2">
              @csrf
              <input type="text" name="shelf_number" placeholder="Shelf # (e.g. SH-01)" class="input w-full text-xs" required>
              <input type="text" name="name" placeholder="Name or Subject" class="input w-full text-xs">
              <input type="number" name="capacity" value="25" min="1" class="input w-full text-xs" required>
              <div class="flex gap-1.5 pt-1">
                <button type="submit" class="btn btn-primary btn-xs flex-1 text-xs">Save</button>
                <button type="button" @click="openShelf = false" class="btn btn-secondary btn-xs text-xs">Cancel</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    @empty
    <div class="card p-12 text-center text-slate-400 text-sm">
      No library racks created yet. Use the "+ New Rack" button above to map your physical library floors and shelves.
    </div>
    @endforelse
  </div>

  @if($racks->hasPages())
  <div class="p-4">
    {{ $racks->links() }}
  </div>
  @endif

</div>
@endsection

