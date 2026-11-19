@extends('layouts.app')
@section('title', 'Shelf Copies — Rack ' . $shelf->rack?->rack_number . ' / ' . $shelf->shelf_number)

@section('content')
<div class="space-y-6">

  <div class="flex items-center justify-between flex-wrap gap-4">
    <div class="flex items-center gap-3">
      <a href="{{ route('library.racks.index') }}" class="btn btn-secondary btn-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Racks
      </a>
      <div>
        <h1 class="page-title text-xl font-bold text-slate-900">Rack {{ $shelf->rack?->rack_number }} · Shelf {{ $shelf->shelf_number }}</h1>
        <p class="text-xs text-slate-500 mt-0.5">
          {{ $shelf->rack?->building }} · {{ $shelf->rack?->room }} · {{ $shelf->copies->count() }} of {{ $shelf->capacity }} books capacity
        </p>
      </div>
    </div>
  </div>

  <div class="card overflow-hidden">
    <div class="table-wrap">
      <table class="w-full text-left text-sm">
        <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase text-slate-600">
          <tr>
            <th class="py-3 px-4">Acc. #</th>
            <th class="py-3 px-4">Barcode</th>
            <th class="py-3 px-4">Book Title</th>
            <th class="py-3 px-4">Author</th>
            <th class="py-3 px-4">Condition</th>
            <th class="py-3 px-4">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @forelse($shelf->copies as $copy)
          <tr class="hover:bg-slate-50/80 transition">
            <td class="py-3.5 px-4 font-mono text-xs font-bold text-primary-600">{{ $copy->accession_number }}</td>
            <td class="py-3.5 px-4 font-mono text-xs text-slate-600">{{ $copy->barcode }}</td>
            <td class="py-3.5 px-4 font-semibold text-slate-800 text-xs">{{ $copy->book?->title }}</td>
            <td class="py-3.5 px-4 text-xs text-slate-600">{{ $copy->book?->author ?: '—' }}</td>
            <td class="py-3.5 px-4 text-xs">
              <span class="badge bg-slate-100 text-slate-700 text-xs">{{ ucfirst(str_replace('_', ' ', $copy->condition)) }}</span>
            </td>
            <td class="py-3.5 px-4 whitespace-nowrap">
              <span class="badge {{ $copy->status === 'available' ? 'badge-green' : 'badge-blue' }} text-xs">{{ ucfirst($copy->status) }}</span>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6" class="text-center py-8 text-slate-400 text-xs">This shelf currently has no books stored on it.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection

