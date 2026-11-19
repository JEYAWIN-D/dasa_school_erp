@extends('layouts.app')
@section('title', 'Book Catalogue & Master')

@section('content')
<div class="space-y-6">

  {{-- Header --}}
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title text-2xl font-bold text-slate-900">Book Catalogue</h1>
      <p class="page-subtitle text-slate-500 text-sm mt-0.5">Master Book Titles & Physical Inventory Registry</p>
    </div>
    <div class="flex gap-2 flex-wrap">
      <a href="{{ route('library.books.bulk-import') }}" class="btn btn-secondary btn-sm">Bulk Import</a>
      <a href="{{ route('library.books.qr-bulk') }}" target="_blank" class="btn btn-secondary btn-sm">Print QR Labels</a>
      <a href="{{ route('library.acquisition-report') }}" class="btn btn-secondary btn-sm">Acquisition Register</a>
      <a href="{{ route('library.books.create') }}" class="btn btn-primary btn-sm flex items-center gap-1.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Book Title
      </a>
    </div>
  </div>

  {{-- Sub-Navigation --}}
  @include('library.partials.nav')

  {{-- Filters --}}
  <form method="GET" class="card p-4">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
      <div class="lg:col-span-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title, author, ISBN, or accession..." class="input w-full text-sm">
      </div>

      <div>
        <select name="category_id" class="input w-full text-sm">
          <option value="">All Categories</option>
          @foreach($categories ?? [] as $cat)
          <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->name }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <select name="rack_id" class="input w-full text-sm">
          <option value="">All Racks</option>
          @foreach($racks ?? [] as $r)
          <option value="{{ $r->id }}" @selected(request('rack_id') == $r->id)>Rack {{ $r->rack_number }} ({{ $r->name }})</option>
          @endforeach
        </select>
      </div>

      <div class="flex items-center gap-2">
        <label class="flex items-center gap-2 text-xs font-medium text-slate-600">
          <input type="checkbox" name="available" value="1" @checked(request('available')) class="w-4 h-4 rounded text-primary-600">
          Available Only
        </label>
        <button type="submit" class="btn btn-secondary btn-sm flex-1">Filter</button>
      </div>
    </div>
  </form>

  {{-- Book List Table --}}
  <div class="card overflow-hidden">
    <div class="table-wrap">
      <table class="w-full text-left text-sm">
        <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase text-slate-600">
          <tr>
            <th class="py-3 px-4">Acc. #</th>
            <th class="py-3 px-4">Cover</th>
            <th class="py-3 px-4">Title & Details</th>
            <th class="py-3 px-4">Author / Publisher</th>
            <th class="py-3 px-4">Category</th>
            <th class="py-3 px-4">Location</th>
            <th class="py-3 px-4 text-center">Copies</th>
            <th class="py-3 px-4 text-center">Available</th>
            <th class="py-3 px-4 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @forelse($books as $book)
          <tr class="hover:bg-slate-50/80 transition {{ $book->is_deaccessioned ? 'opacity-50 bg-slate-50' : '' }}">
            <td class="py-3.5 px-4 font-mono text-xs font-semibold text-primary-600 whitespace-nowrap">
              {{ $book->accession_number }}
              @if($book->isbn)
              <div class="text-[10px] text-slate-400 font-normal">ISBN: {{ $book->isbn }}</div>
              @endif
            </td>

            <td class="py-3.5 px-4 whitespace-nowrap">
              @if($book->cover_image)
                <img src="{{ asset('storage/' . $book->cover_image) }}" alt="Cover" class="w-8 h-11 object-cover rounded shadow-sm border border-slate-200">
              @else
                <div class="w-8 h-11 bg-slate-100 border border-slate-200 rounded flex items-center justify-center text-slate-300 text-[10px]">📖</div>
              @endif
            </td>

            <td class="py-3.5 px-4 max-w-xs">
              <p class="font-semibold text-slate-800 leading-snug">{{ $book->title }}</p>
              @if($book->subtitle)
              <p class="text-xs text-slate-400 truncate">{{ $book->subtitle }}</p>
              @endif
              @if($book->subject)
              <span class="inline-block mt-0.5 text-[10px] px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 font-medium">{{ $book->subject }}</span>
              @endif
            </td>

            <td class="py-3.5 px-4 text-xs text-slate-600 whitespace-nowrap">
              <p class="font-medium text-slate-800">{{ $book->author ?? '—' }}</p>
              <p class="text-slate-400 text-[11px]">{{ $book->publisher ?? '' }} {{ $book->publish_year ? "({$book->publish_year})" : '' }}</p>
            </td>

            <td class="py-3.5 px-4 whitespace-nowrap">
              <span class="badge bg-slate-100 text-slate-700 text-xs font-medium">
                {{ $book->category?->name ?? ($book->category ?: 'General') }}
              </span>
            </td>

            <td class="py-3.5 px-4 whitespace-nowrap text-xs text-slate-600">
              @if($book->rack || $book->shelf)
                <div>
                  <span class="font-bold text-slate-900">Rack {{ $book->rack?->rack_number ?: ($book->shelf?->rack?->rack_number ?? '—') }}</span>
                  @if($book->rack?->name)
                    <span class="text-[11px] text-slate-500 block truncate max-w-[140px]">{{ $book->rack->name }}</span>
                  @endif
                </div>
                <div class="text-[10.5px] text-slate-400 mt-0.5">
                  Shelf {{ $book->shelf?->shelf_number ?? '—' }}
                  @if($book->rack?->floor)
                    &bull; Fl {{ $book->rack->floor }}
                  @endif
                </div>
              @else
                <span class="text-slate-400 text-xs">{{ $book->location ?: 'Not allocated' }}</span>
              @endif
            </td>

            <td class="py-3.5 px-4 text-center whitespace-nowrap font-semibold text-slate-800 text-sm">
              {{ $book->copies_count ?? $book->total_copies }}
            </td>

            <td class="py-3.5 px-4 text-center whitespace-nowrap">
              @php
                $avail = $book->available_copies;
              @endphp
              <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $avail > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600' }}">
                {{ $avail }}
              </span>
            </td>

            <td class="py-3.5 px-4 text-right whitespace-nowrap">
              <div class="flex items-center justify-end gap-1.5">
                {{-- Manage Copies Button --}}
                <a href="{{ route('library.copies.index', $book->id) }}" 
                   class="btn btn-secondary btn-xs inline-flex items-center gap-1 text-xs" 
                   title="View & manage physical barcode copies">
                  <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                  Copies ({{ $book->copies_count ?? $book->total_copies }})
                </a>

                <a href="{{ route('library.books.edit', $book->id) }}" class="btn btn-secondary btn-xs text-xs">
                  Edit
                </a>

                <a href="{{ route('library.books.qr-label', $book->id) }}" target="_blank" class="btn btn-secondary btn-xs text-xs" title="Print Barcode Label">
                  Print
                </a>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="9" class="py-12 text-center text-slate-400 text-sm">
              No matching books found. <a href="{{ route('library.books.create') }}" class="text-primary-600 font-semibold hover:underline">Add a new book</a>.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($books->hasPages())
    <div class="p-4 border-t border-slate-100">
      {{ $books->links() }}
    </div>
    @endif
  </div>

</div>
@endsection
