@extends('layouts.app')
@section('title', 'Add New Book Title')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

  {{-- Header --}}
  <div class="flex items-center justify-between flex-wrap gap-4">
    <div class="flex items-center gap-3">
      <a href="{{ route('library.books') }}" class="btn btn-secondary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Catalogue
      </a>
      <div>
        <h1 class="page-title text-xl font-bold text-slate-900">Add New Book Title</h1>
        <p class="page-subtitle text-slate-500 text-xs">Register master book title & generate physical copies</p>
      </div>
    </div>
  </div>

  {{-- Sub-Navigation --}}
  @include('library.partials.nav')

  {{-- ISBN Lookup --}}
  <div class="card p-5 border-l-4 border-l-indigo-600" x-data="{
    isbnInput: '',
    loading: false,
    error: '',
    coverUrl: '',
    async lookup() {
      if (!this.isbnInput.trim()) return;
      this.loading = true; this.error = ''; this.coverUrl = '';
      try {
        const res = await fetch('{{ route('library.isbn-lookup') }}?isbn=' + encodeURIComponent(this.isbnInput));
        const data = await res.json();
        if (!res.ok) { this.error = data.error || 'Not found'; this.loading = false; return; }
        document.getElementById('f_title').value      = data.title || '';
        document.getElementById('f_author').value     = data.author || '';
        document.getElementById('f_publisher').value  = data.publisher || '';
        document.getElementById('f_year').value       = data.year || '';
        document.getElementById('f_isbn').value       = this.isbnInput;
        if (data.pages) document.getElementById('f_pages').value = data.pages;
        if (data.subjects) document.getElementById('f_subject').value = data.subjects;
        if (data.cover_url) this.coverUrl = data.cover_url;
      } catch(e) { this.error = 'Request failed. Check internet connection.'; }
      this.loading = false;
    }
  }">
    <h3 class="font-bold text-slate-800 text-sm mb-2 flex items-center gap-2">
      <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
      Fast ISBN Auto-Fill <span class="text-xs text-slate-400 font-normal">via Open Library API</span>
    </h3>
    <div class="flex gap-2">
      <input type="text" x-model="isbnInput" class="input flex-1 text-sm font-mono"
        placeholder="Enter 10 or 13-digit ISBN (e.g. 9780134685991)"
        @keydown.enter.prevent="lookup()">
      <button type="button" @click="lookup()" class="btn btn-secondary btn-sm" :disabled="loading">
        <span x-text="loading ? 'Looking up…' : 'Fetch Details'"></span>
      </button>
    </div>
    <p x-show="error" class="text-xs text-red-500 mt-2 font-medium" x-text="error"></p>
    <div x-show="coverUrl" class="mt-3 flex items-center gap-3">
      <img :src="coverUrl" alt="Cover" class="h-14 rounded shadow border border-slate-200">
      <p class="text-xs text-emerald-600 font-medium">✓ Book metadata retrieved successfully</p>
    </div>
  </div>

  <form method="POST" action="{{ route('library.books.store') }}" enctype="multipart/form-data" class="card p-6 space-y-5">
    @csrf

    {{-- Title and Subtitle --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="sm:col-span-2">
        <label class="block text-xs font-semibold text-slate-700 mb-1">Book Title *</label>
        <input type="text" id="f_title" name="title" value="{{ old('title') }}" placeholder="Primary Title" class="input w-full text-sm" required>
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Subtitle</label>
        <input type="text" name="subtitle" value="{{ old('subtitle') }}" placeholder="Subtitle or Edition detail" class="input w-full text-sm">
      </div>
    </div>

    {{-- Author and Publisher --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Author *</label>
        <input type="text" id="f_author" name="author" value="{{ old('author') }}" placeholder="Primary Author" class="input w-full text-sm">
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Co-Author</label>
        <input type="text" name="co_author" value="{{ old('co_author') }}" placeholder="Secondary Author" class="input w-full text-sm">
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Publisher</label>
        <input type="text" id="f_publisher" name="publisher" value="{{ old('publisher') }}" placeholder="Publisher Name" class="input w-full text-sm">
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">ISBN</label>
        <input type="text" id="f_isbn" name="isbn" value="{{ old('isbn') }}" placeholder="ISBN-10 or 13" class="input w-full text-sm font-mono">
      </div>
    </div>

    {{-- Category, Subject, Suitability, Language --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Category *</label>
        <select name="category_id" class="input w-full text-sm">
          <option value="">Select Category</option>
          @foreach($categories as $cat)
          <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>{{ $cat->name }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Subject</label>
        <input type="text" id="f_subject" name="subject" value="{{ old('subject') }}" placeholder="e.g. Mathematics, Organic Chemistry" class="input w-full text-sm">
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Class Suitability</label>
        <input type="text" name="class_suitability" value="{{ old('class_suitability') }}" placeholder="e.g. Class 8 - 10" class="input w-full text-sm">
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Language</label>
        <input type="text" name="language" value="{{ old('language', 'English') }}" class="input w-full text-sm">
      </div>
    </div>

    {{-- Physical Copies & Placement --}}
    <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-4"
         x-data="{
           selectedRackId: '{{ old('rack_id', '') }}',
           selectedShelfId: '{{ old('shelf_id', '') }}',
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
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Physical Inventory, Rack Allocation & Financials</h3>
        <span class="text-[11px] text-slate-500 font-medium">Mapped to Library Stacks Master</span>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">Total Physical Copies *</label>
          <input type="number" name="total_copies" value="{{ old('total_copies', 1) }}" min="1" max="100" class="input w-full text-sm font-bold text-primary-600" required>
          <span class="text-[10px] text-slate-400">Sequential accessions auto-generated</span>
        </div>

        {{-- 1. Rack Selection Dropdown --}}
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">1. Select Library Rack</label>
          <select name="rack_id" x-model="selectedRackId" @change="onRackChange()" class="input w-full text-sm font-medium">
            <option value="">Choose Rack / Stacks...</option>
            @foreach($racks as $rk)
            <option value="{{ $rk->id }}">
              Rack {{ $rk->rack_number }} — {{ $rk->name }} (Floor {{ $rk->floor ?? '1' }} · Rm {{ $rk->room ?? 'Main' }})
            </option>
            @endforeach
          </select>
          <span class="text-[10px] text-slate-400">Filters shelves automatically</span>
        </div>

        {{-- 2. Shelf Selection Dropdown --}}
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">2. Select Shelf</label>
          <select name="shelf_id" x-model="selectedShelfId" @change="onShelfChange()" class="input w-full text-sm">
            <option value="">Unassigned Shelf</option>
            <template x-for="sh in filteredShelves" :key="sh.id">
              <option :value="sh.id" x-text="`Shelf ${sh.shelf_num} — ${sh.name} (Cap: ${sh.capacity})`"></option>
            </template>
          </select>
          <span class="text-[10px] text-slate-400" x-show="selectedRackId" x-text="`Showing ${filteredShelves.length} shelves on this rack`"></span>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">Purchase Price (₹)</label>
          <input type="number" step="0.01" name="purchase_price" value="{{ old('purchase_price') }}" class="input w-full text-sm" placeholder="₹">
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
          <label class="block text-xs font-semibold text-slate-700 mb-1">Current Book Value (₹)</label>
          <input type="number" step="0.01" name="current_value" value="{{ old('current_value') }}" class="input w-full text-sm" placeholder="₹">
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">Book Vendor / Supplier</label>
          <select name="supplier_id" class="input w-full text-sm">
            <option value="">Select Vendor</option>
            @foreach($suppliers as $sup)
            <option value="{{ $sup->id }}">{{ $sup->name }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">Invoice / Bill Number</label>
          <input type="text" name="bill_number" placeholder="Bill / Invoice ref" class="input w-full text-sm">
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">Publication Year & Pages</label>
          <div class="flex gap-2">
            <input type="number" id="f_year" name="publication_year" placeholder="Year" class="input w-1/2 text-sm" min="1800" max="{{ date('Y') + 1 }}">
            <input type="number" id="f_pages" name="pages" placeholder="Pages" class="input w-1/2 text-sm" min="1">
          </div>
        </div>
      </div>
    </div>

    {{-- Description & Cover --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="sm:col-span-2">
        <label class="block text-xs font-semibold text-slate-700 mb-1">Book Summary / Description</label>
        <textarea name="description" rows="3" class="input w-full text-sm" placeholder="Brief synopsis or syllabus notes"></textarea>
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Book Cover Image</label>
        <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp"
               class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
        <p class="text-[10px] text-slate-400 mt-1">JPG, PNG, WebP up to 2MB</p>
      </div>
    </div>

    <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
      <a href="{{ route('library.books') }}" class="btn btn-secondary">Cancel</a>
      <button type="submit" class="btn btn-primary px-6">
        Save Title & Create Physical Copies
      </button>
    </div>
  </form>

</div>
@endsection
