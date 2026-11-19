@extends('layouts.app')
@section('title', 'Manage Copies — ' . $book->title)

@section('content')
<div class="space-y-6">

  {{-- Header --}}
  <div class="flex items-center justify-between flex-wrap gap-4">
    <div class="flex items-center gap-3">
      <a href="{{ route('library.books') }}" class="btn btn-secondary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Catalogue
      </a>
      <div>
        <h1 class="page-title text-xl font-bold text-slate-900">Physical Copies: {{ $book->title }}</h1>
        <p class="text-xs text-slate-500 mt-0.5">
          Accession: {{ $book->accession_number }} · Author: {{ $book->author ?: 'N/A' }} · {{ $book->copies->count() }} Total Copies ({{ $book->availableCopiesList->count() }} Available)
        </p>
        @if($book->rack || $book->shelf)
        <div class="mt-1 flex items-center gap-2 flex-wrap text-xs">
          <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold text-[10.5px]">
            📍 Assigned Rack: {{ $book->rack?->rack_number ?: ($book->shelf?->rack?->rack_number ?? '—') }} ({{ $book->rack?->name ?: 'Stacks' }})
          </span>
          <span class="text-slate-600 font-medium">
            Floor {{ $book->rack?->floor ?? '1' }}, Room {{ $book->rack?->room ?? 'Main' }} &bull; Shelf {{ $book->shelf?->shelf_number ?? '—' }} ({{ $book->shelf?->name ?? 'General' }})
          </span>
          @if($book->shelf?->capacity)
          <span class="text-slate-400 font-mono text-[10px]">Cap: {{ $book->shelf->capacity }} books</span>
          @endif
        </div>
        @endif
      </div>
    </div>

    <div>
      <a href="{{ route('library.books.qr-label', $book->id) }}" target="_blank" class="btn btn-secondary btn-sm flex items-center gap-1.5">
        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Print Barcode Labels
      </a>
    </div>
  </div>

  {{-- Add New Copies Card --}}
  <div class="card p-5 border-l-4 border-l-primary-600">
    <h3 class="text-sm font-bold text-slate-900 mb-1">Add Physical Copies to Title</h3>
    <p class="text-xs text-slate-500 mb-4">Each copy will be assigned a unique accession number and scannable barcode automatically.</p>

    <form action="{{ route('library.copies.store', $book->id) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5 items-end">
      @csrf

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Number of Copies *</label>
        <input type="number" name="count" value="1" min="1" max="100" class="input w-full text-sm" required>
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Rack / Shelf Location</label>
        <select name="shelf_id" class="input w-full text-sm">
          <option value="">Unallocated</option>
          @foreach($shelves as $sh)
          <option value="{{ $sh->id }}" @selected($book->shelf_id == $sh->id)>
            Rack {{ $sh->rack?->rack_number }} · Shelf {{ $sh->shelf_number }} ({{ $sh->name ?: $sh->subject }})
          </option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Initial Condition</label>
        <select name="condition" class="input w-full text-sm">
          <option value="new">Brand New</option>
          <option value="good" selected>Good Condition</option>
          <option value="minor_damage">Minor Damage</option>
        </select>
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Unit Cost Price (₹)</label>
        <input type="number" step="0.01" name="cost_price" value="{{ $book->purchase_price }}" placeholder="₹" class="input w-full text-sm">
      </div>

      <div class="flex items-center gap-3">
        <label class="flex items-center gap-1.5 text-xs text-slate-700 font-medium">
          <input type="checkbox" name="is_reference_only" value="1" class="w-4 h-4 rounded text-primary-600">
          Reference Only
        </label>
        <button type="submit" class="btn btn-primary btn-sm flex-1">Add Copies</button>
      </div>
    </form>
  </div>

  {{-- Physical Copies List --}}
  <div class="card overflow-hidden">
    <div class="p-4 border-b border-slate-100 flex items-center justify-between">
      <h3 class="text-sm font-bold text-slate-900">Physical Copies Inventory ({{ $book->copies->count() }})</h3>
      <span class="text-xs text-slate-500">Every physical copy must have a unique accession number</span>
    </div>

    <div class="table-wrap">
      <table class="w-full text-left text-sm">
        <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase text-slate-600">
          <tr>
            <th class="py-3 px-4">Copy #</th>
            <th class="py-3 px-4">Accession #</th>
            <th class="py-3 px-4">Barcode</th>
            <th class="py-3 px-4">Location</th>
            <th class="py-3 px-4">Condition</th>
            <th class="py-3 px-4">Status</th>
            <th class="py-3 px-4">Loan Policy</th>
            <th class="py-3 px-4 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @forelse($book->copies as $copy)
          <tr class="hover:bg-slate-50/80 transition">
            <td class="py-3.5 px-4 font-semibold text-slate-700 text-xs">
              #{{ $copy->copy_number }}
            </td>

            <td class="py-3.5 px-4 font-mono text-xs font-bold text-primary-600">
              {{ $copy->accession_number }}
            </td>

            <td class="py-3.5 px-4 font-mono text-xs text-slate-600">
              <span class="px-2 py-0.5 rounded bg-slate-100 border border-slate-200 text-slate-700 font-medium">
                {{ $copy->barcode }}
              </span>
            </td>

            <td class="py-3.5 px-4 text-xs text-slate-600">
              @if($copy->shelf)
                <span class="font-bold text-slate-900">Rack {{ $copy->shelf->rack?->rack_number }} ({{ $copy->shelf->rack?->name ?: 'Stacks' }})</span>
                <span class="text-slate-500 block text-[11px]">Shelf {{ $copy->shelf->shelf_number }} &bull; Floor {{ $copy->shelf->rack?->floor ?? '1' }}</span>
              @else
                <span class="text-slate-400">Not assigned</span>
              @endif
            </td>

            <td class="py-3.5 px-4 text-xs">
              <form action="{{ route('library.copies.condition', $copy->id) }}" method="POST" class="inline-flex items-center gap-1.5">
                @csrf
                <select name="condition" onchange="this.form.submit()" class="text-xs py-1 px-2 rounded border-slate-200 bg-white">
                  <option value="new" @selected($copy->condition === 'new')>New</option>
                  <option value="good" @selected($copy->condition === 'good')>Good</option>
                  <option value="minor_damage" @selected($copy->condition === 'minor_damage')>Minor Damage</option>
                  <option value="major_damage" @selected($copy->condition === 'major_damage')>Major Damage</option>
                  <option value="torn_pages" @selected($copy->condition === 'torn_pages')>Torn Pages</option>
                  <option value="water_damage" @selected($copy->condition === 'water_damage')>Water Damage</option>
                  <option value="lost" @selected($copy->condition === 'lost')>Lost</option>
                  <option value="written_off" @selected($copy->condition === 'written_off')>Written Off</option>
                </select>
              </form>
            </td>

            <td class="py-3.5 px-4 whitespace-nowrap">
              @if($copy->status === 'available')
                <span class="badge bg-emerald-50 text-emerald-700 font-semibold text-xs">Available</span>
              @elseif($copy->status === 'issued')
                <span class="badge bg-blue-50 text-blue-700 font-semibold text-xs">Issued</span>
              @elseif($copy->status === 'reserved')
                <span class="badge bg-amber-50 text-amber-700 font-semibold text-xs">Reserved</span>
              @elseif($copy->status === 'damaged')
                <span class="badge bg-orange-50 text-orange-700 font-semibold text-xs">Damaged</span>
              @elseif($copy->status === 'lost')
                <span class="badge bg-red-50 text-red-700 font-semibold text-xs">Lost</span>
              @elseif($copy->status === 'disposed')
                <span class="badge bg-slate-100 text-slate-500 font-semibold text-xs">Disposed</span>
              @else
                <span class="badge bg-slate-100 text-slate-700 text-xs">{{ ucfirst($copy->status) }}</span>
              @endif
            </td>

            <td class="py-3.5 px-4 text-xs whitespace-nowrap">
              @if($copy->is_reference_only)
                <span class="badge bg-purple-50 text-purple-700 text-[11px] font-semibold">Reference Only</span>
              @else
                <span class="text-slate-500 text-[11px]">Circulating</span>
              @endif
            </td>

            <td class="py-3.5 px-4 text-right whitespace-nowrap">
              @if($copy->status === 'available')
              <a href="{{ route('library.issue', ['barcode' => $copy->barcode]) }}" class="btn btn-primary btn-xs text-xs">
                Issue Copy
              </a>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="text-center py-8 text-slate-400 text-sm">No physical copies added yet. Use form above to generate copies.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection

