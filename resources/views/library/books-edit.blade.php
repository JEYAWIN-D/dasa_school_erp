@extends('layouts.app')
@section('title', 'Edit Book Master — ' . $book->title)

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">

  <div class="flex items-center justify-between flex-wrap gap-4">
    <div class="flex items-center gap-3">
      <a href="{{ route('library.books') }}" class="btn btn-secondary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Catalogue
      </a>
      <div>
        <h1 class="page-title text-xl font-bold text-slate-900">Edit Title Master: {{ $book->title }}</h1>
        <p class="text-xs text-slate-500 font-mono mt-0.5">Acc. #{{ $book->accession_number }} · {{ $book->copies->count() }} Physical Copies</p>
      </div>
    </div>
    <div>
      <a href="{{ route('library.copies.index', $book->id) }}" class="btn btn-secondary btn-sm flex items-center gap-1.5">
        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
        Manage Physical Copies ({{ $book->copies->count() }})
      </a>
    </div>
  </div>

  {{-- Sub-Navigation --}}
  @include('library.partials.nav')

  @if(session('success'))
    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">
      {{ session('success') }}
    </div>
  @endif

  <form method="POST" action="{{ route('library.books.update', $book->id) }}" enctype="multipart/form-data" class="card p-6 space-y-5">
    @csrf
    @method('PUT')

    {{-- Title and Subtitle --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="sm:col-span-2">
        <label class="block text-xs font-semibold text-slate-700 mb-1">Book Title *</label>
        <input type="text" name="title" value="{{ old('title', $book->title) }}" class="input w-full text-sm font-semibold" required>
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Subtitle</label>
        <input type="text" name="subtitle" value="{{ old('subtitle', $book->subtitle) }}" class="input w-full text-sm">
      </div>
    </div>

    {{-- Authors & Publisher --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Author</label>
        <input type="text" name="author" value="{{ old('author', $book->author) }}" class="input w-full text-sm">
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Co-Author</label>
        <input type="text" name="co_author" value="{{ old('co_author', $book->co_author) }}" class="input w-full text-sm">
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Publisher</label>
        <input type="text" name="publisher" value="{{ old('publisher', $book->publisher) }}" class="input w-full text-sm">
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">ISBN</label>
        <input type="text" name="isbn" value="{{ old('isbn', $book->isbn) }}" class="input w-full text-sm font-mono">
      </div>
    </div>

    {{-- Category, Subject, Suitability --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Category</label>
        <select name="category_id" class="input w-full text-sm">
          <option value="">Select Category</option>
          @foreach($categories as $cat)
          <option value="{{ $cat->id }}" @selected(old('category_id', $book->category_id) == $cat->id)>{{ $cat->name }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Subject</label>
        <input type="text" name="subject" value="{{ old('subject', $book->subject) }}" class="input w-full text-sm">
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Class Suitability</label>
        <input type="text" name="class_suitability" value="{{ old('class_suitability', $book->class_suitability) }}" class="input w-full text-sm">
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Pages & Year</label>
        <div class="flex gap-2">
          <input type="number" name="pages" value="{{ old('pages', $book->pages) }}" placeholder="Pages" class="input w-1/2 text-sm">
          <input type="number" name="publication_year" value="{{ old('publication_year', $book->publish_year) }}" placeholder="Year" class="input w-1/2 text-sm">
        </div>
      </div>
    </div>

    {{-- Physical Location & Financials --}}
    <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-4"
         x-data="{
           selectedRackId: '{{ old('rack_id', $book->rack_id ?? ($book->shelf?->rack_id ?? '')) }}',
           selectedShelfId: '{{ old('shelf_id', $book->shelf_id ?? '') }}',
           shelves: {{ json_encode($shelves->map(fn($s) => [
             'id' => $s->id,
             'rack_id' => $s->rack_id,
             'rack_num' => $s->rack?->rack_number ?? '—',
             'shelf_num' => $s->shelf_number,
             'name' => $s->name ?: ($s->subject ?: 'General'),
             'capacity' => $s->capacity ?? 50,
             'floor' => $s->rack?->floor ?? '1',
             'room' => $s->rack?->room ?? 'Main Stacks'
           ])) }},
           racks: {{ json_encode($racks->map(fn($r) => [
             'id' => $r->id,
             'number' => $r->rack_number,
             'name' => $r->name ?: 'Standard Rack',
             'floor' => $r->floor ?? '1',
             'room' => $r->room ?? 'Main Stacks',
             'capacity' => $r->capacity ?? 200,
             'shelves_count' => $r->shelves->count()
           ])) }},
           get filteredShelves() {
             if (!this.selectedRackId) return this.shelves;
             return this.shelves.filter(s => s.rack_id == this.selectedRackId);
           },
           get activeRack() {
             return this.racks.find(r => r.id == this.selectedRackId) || null;
           },
           get activeShelf() {
             return this.shelves.find(s => s.id == this.selectedShelfId) || null;
           },
           onRackChange() {
             if (this.selectedShelfId) {
               const match = this.shelves.find(s => s.id == this.selectedShelfId && s.rack_id == this.selectedRackId);
               if (!match) this.selectedShelfId = '';
             }
           },
           onShelfChange() {
             if (this.selectedShelfId && !this.selectedRackId) {
               const sh = this.shelves.find(s => s.id == this.selectedShelfId);
               if (sh && sh.rack_id) this.selectedRackId = sh.rack_id;
             }
           }
         }">
      <div class="flex items-center justify-between">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Location, Value &amp; Inventory Status</h3>
        <span class="text-[11px] text-slate-500 font-medium">Mapped to Physical Stacks</span>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- 1. Rack Selection Dropdown --}}
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">1. Library Rack</label>
          <select name="rack_id" x-model="selectedRackId" @change="onRackChange()" class="input w-full text-sm font-medium">
            <option value="">Choose Rack / Stacks...</option>
            @foreach($racks as $rk)
            <option value="{{ $rk->id }}" @selected(old('rack_id', $book->rack_id ?? ($book->shelf?->rack_id ?? '')) == $rk->id)>
              Rack {{ $rk->rack_number }} — {{ $rk->name }} (Floor {{ $rk->floor ?? '1' }} · Rm {{ $rk->room ?? 'Main' }})
            </option>
            @endforeach
          </select>
          <span class="text-[10px] text-slate-400">Filters available shelves</span>
        </div>

        {{-- 2. Shelf Selection Dropdown --}}
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">2. Shelf Location</label>
          <select name="shelf_id" x-model="selectedShelfId" @change="onShelfChange()" class="input w-full text-sm">
            <option value="">Unassigned Shelf</option>
            <template x-for="sh in filteredShelves" :key="sh.id">
              <option :value="sh.id" :selected="sh.id == selectedShelfId" x-text="`Shelf ${sh.shelf_num} — ${sh.name} (Cap: ${sh.capacity})`"></option>
            </template>
          </select>
          <span class="text-[10px] text-slate-400" x-show="selectedRackId" x-text="`Showing ${filteredShelves.length} shelves on this rack`"></span>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">Purchase Price (₹)</label>
          <input type="number" step="0.01" name="purchase_price" value="{{ old('purchase_price', $book->purchase_price) }}" class="input w-full text-sm">
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">Current Book Value (₹)</label>
          <input type="number" step="0.01" name="current_value" value="{{ old('current_value', $book->current_value) }}" class="input w-full text-sm">
        </div>
      </div>

      {{-- Assigned Rack Details Live Preview Panel --}}
      <div x-show="activeRack || activeShelf" x-transition class="p-3 bg-emerald-50/80 border border-emerald-200 rounded-lg text-xs text-emerald-950 flex flex-wrap items-center justify-between gap-3 shadow-2xs">
        <div class="flex items-center gap-3 flex-wrap">
          <span class="px-2 py-0.5 rounded bg-emerald-700 text-white font-bold text-[10px] uppercase tracking-wider">
            Assigned Location
          </span>
          <div class="space-x-2">
            <span class="font-bold">
              Rack <span x-text="activeRack ? activeRack.number : (activeShelf ? activeShelf.rack_num : '—')"></span>:
              <span x-text="activeRack ? activeRack.name : 'Allocated'"></span>
            </span>
            <span class="text-emerald-700 font-medium">
              &bull; Floor <span x-text="activeRack ? activeRack.floor : (activeShelf ? activeShelf.floor : '1')"></span>,
              Room <span x-text="activeRack ? activeRack.room : (activeShelf ? activeShelf.room : 'Main')"></span>
            </span>
            <template x-if="activeShelf">
              <span class="font-semibold text-emerald-900 bg-emerald-100/80 px-2 py-0.5 rounded">
                Shelf <span x-text="activeShelf.shelf_num"></span> (<span x-text="activeShelf.name"></span>) &bull; Capacity: <span x-text="activeShelf.capacity"></span>
              </span>
            </template>
          </div>
        </div>
        <div class="text-[11px] font-mono text-emerald-800" x-show="activeRack">
          Total Rack Cap: <strong x-text="activeRack ? activeRack.capacity : '—'"></strong>
        </div>
      </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">Status</label>
          <select name="status" class="input w-full text-sm">
            <option value="active" @selected($book->status === 'active')>Active Title</option>
            <option value="archived" @selected($book->status === 'archived')>Archived Title</option>
          </select>
        </div>
      </div>
    </div>

    {{-- Cover and Description --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="sm:col-span-2">
        <label class="block text-xs font-semibold text-slate-700 mb-1">Description / Summary</label>
        <textarea name="description" rows="3" class="input w-full text-sm">{{ old('description', $book->description) }}</textarea>
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Book Cover Image</label>
        @if($book->cover_image)
        <div class="flex items-center gap-3 mb-2">
          <img src="{{ asset('storage/' . $book->cover_image) }}" alt="Cover" class="w-12 h-16 object-cover rounded border border-slate-200 shadow-sm">
          <span class="text-xs text-slate-400">Current cover</span>
        </div>
        @endif
        <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp"
               class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
      </div>
    </div>

    <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
      <a href="{{ route('library.books') }}" class="btn btn-secondary">Cancel</a>
      <button type="submit" class="btn btn-primary px-6">Save Title Changes</button>
    </div>
  </form>

</div>
@endsection
