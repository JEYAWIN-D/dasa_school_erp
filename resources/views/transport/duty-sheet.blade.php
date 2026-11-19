@extends('layouts.app')
@section('title', 'Daily Transport Duty Sheet')
@section('content')
<div class="space-y-6">

  {{-- Page Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="page-title text-2xl font-black text-slate-900 tracking-tight">Daily Transport Duty Sheet</h1>
      <p class="text-xs text-slate-500 mt-0.5">Generate, audit, and print operational daily run sheets, driver checklists, and passenger manifests</p>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
      <a href="{{ route('transport.duty-sheet.print', ['date' => $date, 'vehicle_id' => $vehicleId, 'trip' => $trip]) }}" 
         target="_blank"
         class="btn btn-primary btn-sm inline-flex items-center gap-1.5 shadow-sm font-bold">
        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Print Duty Sheet
      </a>
      <a href="{{ route('transport.duty-sheet.pdf', ['date' => $date, 'vehicle_id' => $vehicleId, 'trip' => $trip]) }}" 
         class="btn btn-secondary btn-sm inline-flex items-center gap-1.5 shadow-2xs font-bold text-rose-700">
        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Download PDF
      </a>
    </div>
  </div>

  {{-- Sub Navigation Tabs --}}
  @include('transport.partials.nav')

  {{-- Filters Card --}}
  <div class="card p-5 bg-white border border-slate-200 shadow-xs">
    <form method="GET" action="{{ route('transport.duty-sheet') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
      <div>
        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Duty Date</label>
        <input type="date" name="date" value="{{ $date }}" class="input text-xs w-full">
      </div>
      <div>
        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Select Van / Fleet</label>
        <select name="vehicle_id" class="input text-xs w-full">
          <option value="">All Active Vans (5 Vehicles)</option>
          @foreach($allVehicles as $v)
            <option value="{{ $v->id }}" {{ $vehicleId == $v->id ? 'selected' : '' }}>
              {{ $v->vehicle_number }} &bull; {{ $v->driver_name ?? 'Driver' }} ({{ $v->route?->route_name ?? 'Route' }})
            </option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Shift / Run</label>
        <select name="trip" class="input text-xs w-full">
          <option value="both" {{ $trip === 'both' ? 'selected' : '' }}>Full Day (Morning Pickup + Evening Drop)</option>
          <option value="morning" {{ $trip === 'morning' ? 'selected' : '' }}>Morning Pickup Only</option>
          <option value="evening" {{ $trip === 'evening' ? 'selected' : '' }}>Evening Drop Only</option>
        </select>
      </div>
      <div class="flex items-center gap-2">
        <button type="submit" class="btn btn-primary btn-sm flex-1 font-bold">Filter</button>
        <a href="{{ route('transport.duty-sheet') }}" class="btn btn-secondary btn-sm">Reset</a>
      </div>
    </form>
  </div>

  {{-- Vehicles Operational Roster Cards --}}
  <div class="space-y-4">
    @forelse($vehicles as $v)
      @php
        $attendant = $v->attendants->first();
        $stops = $v->route?->stops ?? collect();
      @endphp
      <div class="card p-5 bg-white border border-slate-200/90 shadow-xs hover:border-slate-300 transition">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-3">
          <div class="flex items-center gap-3">
            <span class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-900 font-black text-sm">
              🚍
            </span>
            <div>
              <div class="flex items-center gap-2">
                <h3 class="text-base font-extrabold text-slate-900">{{ $v->vehicle_number }}</h3>
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                  {{ $v->route?->route_name ?? 'Unassigned Corridor' }}
                </span>
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                  Capacity: {{ $v->effective_capacity }} Seats
                </span>
              </div>
              <p class="text-xs text-slate-500 mt-0.5">
                Driver: <strong class="text-slate-800">{{ $v->driver_name ?? '—' }}</strong> ({{ $v->driver_phone ?? $v->driver_mobile ?? '—' }}) &bull;
                Attendant: <strong class="text-slate-800">{{ $attendant?->name ?? $v->attendant_name ?? '—' }}</strong> ({{ $attendant?->phone ?? $v->attendant_mobile ?? '—' }})
              </p>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <a href="{{ route('transport.vehicle.print', $v->id) }}" target="_blank" class="btn btn-secondary btn-sm text-xs font-semibold">
              Van Dossier
            </a>
            <a href="{{ route('transport.duty-sheet.print', ['date' => $date, 'vehicle_id' => $v->id, 'trip' => $trip]) }}" target="_blank" class="btn btn-primary btn-sm text-xs font-bold">
              Print Sheet
            </a>
          </div>
        </div>

        {{-- Stops Summary Pill Row --}}
        <div class="mt-4">
          <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">
            Sequential Route Stops ({{ $stops->count() }} stops)
          </div>
          <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
            @forelse($stops as $idx => $s)
              <div class="shrink-0 px-2.5 py-1.5 rounded-lg bg-slate-50 border border-slate-200 flex items-center gap-2">
                <span class="w-4 h-4 rounded-full bg-slate-200 text-slate-700 text-[10px] font-bold flex items-center justify-center">
                  {{ $s->stop_order ?? ($idx + 1) }}
                </span>
                <span class="font-bold text-slate-800">{{ $s->stop_name ?? $s->name }}</span>
                <span class="text-[10px] text-emerald-700 font-mono font-semibold">
                  {{ $s->pickup_time ? \Carbon\Carbon::parse($s->pickup_time)->format('h:i A') : ($s->morning_time ?? '') }}
                </span>
              </div>
              @if(!$loop->last)
                <span class="text-slate-300">&rarr;</span>
              @endif
            @empty
              <span class="text-slate-400 text-xs italic">No stops configured for this corridor.</span>
            @endforelse
          </div>
        </div>
      </div>
    @empty
      <div class="card p-12 text-center text-slate-400">
        <p class="font-bold text-base text-slate-700">No active vehicles found matching filter criteria.</p>
        <p class="text-xs mt-1">Select "All Active Vans" to review complete school fleet duty operations.</p>
      </div>
    @endforelse
  </div>

</div>
@endsection

