@extends('layouts.app')
@section('title', 'Periodicals & Newspapers')

@section('content')
<div class="space-y-6">

  {{-- Header --}}
  <div class="flex items-center justify-between flex-wrap gap-4">
    <div>
      <h1 class="page-title text-2xl font-bold text-slate-900">Periodicals, Magazines & Newspapers</h1>
      <p class="page-subtitle text-slate-500 text-sm mt-0.5">Subscriptions Registry, Issue Check-in & Reading Section Inventory</p>
    </div>
  </div>

  {{-- Sub-Navigation --}}
  @include('library.partials.nav')

  @if(session('success'))
    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">
      {{ session('success') }}
    </div>
  @endif

  {{-- Expiring Subscription Warning --}}
  @if(count($expiringSubscriptions))
  <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-sm flex items-start gap-3">
    <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    <div>
      <h4 class="font-bold text-xs uppercase tracking-wide">Upcoming Subscription Renewals</h4>
      <div class="mt-1 flex flex-wrap gap-2">
        @foreach($expiringSubscriptions as $exp)
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-white border border-amber-200 text-xs">
          <strong>{{ $exp->periodical?->title }}</strong> expires {{ $exp->end_date?->format('d M Y') }}
        </span>
        @endforeach
      </div>
    </div>
  </div>
  @endif

  {{-- Quick Actions: Daily Receipt & Add Periodical --}}
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Quick Record Daily Newspaper / Magazine --}}
    <div class="card p-5 border-l-4 border-l-emerald-600">
      <h3 class="font-bold text-slate-900 text-sm mb-1">Check-in Today's Newspaper / Magazine</h3>
      <p class="text-xs text-slate-500 mb-4">Record receipt of morning papers and periodicals for library reading room.</p>

      <form action="{{ route('library.periodicals.daily-receipt.store') }}" method="POST" class="space-y-3">
        @csrf
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Periodical Title *</label>
            <select name="periodical_id" class="input w-full text-xs" required>
              <option value="">Select Newspaper / Magazine</option>
              @foreach($periodicals as $p)
              <option value="{{ $p->id }}">{{ $p->title }} ({{ ucfirst($p->periodical_type) }})</option>
              @endforeach
            </select>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Issue Date *</label>
            <input type="date" name="issue_date" value="{{ date('Y-m-d') }}" class="input w-full text-xs" required>
          </div>
        </div>

        <div class="grid grid-cols-3 gap-3">
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Receipt Date</label>
            <input type="date" name="received_date" value="{{ date('Y-m-d') }}" class="input w-full text-xs" required>
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Volume / Edition</label>
            <input type="text" name="volume_no" placeholder="Vol. 12" class="input w-full text-xs">
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Copies Received</label>
            <input type="number" name="copies_received" value="1" min="1" class="input w-full text-xs" required>
          </div>
        </div>

        <div>
          <button type="submit" class="btn btn-primary btn-sm w-full font-semibold text-xs py-2">
            Log Receipt into Daily Register
          </button>
        </div>
      </form>
    </div>

    {{-- Register New Periodical Master --}}
    <div class="card p-5 border-l-4 border-l-primary-600" x-data="{ openP: false }">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="font-bold text-slate-900 text-sm">Add New Periodical Title</h3>
          <p class="text-xs text-slate-500">Subscribe to new dailies, weeklies, or scientific journals.</p>
        </div>
        <button type="button" @click="openP = !openP" class="btn btn-secondary btn-sm text-xs" x-text="openP ? 'Close' : '+ New Title'"></button>
      </div>

      <form x-show="openP" x-transition action="{{ route('library.periodicals.store') }}" method="POST" class="mt-4 pt-4 border-t border-slate-100 space-y-3">
        @csrf
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1">Publication Name *</label>
          <input type="text" name="title" placeholder="e.g. The Hindu, India Today, National Geographic" class="input w-full text-xs" required>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Type *</label>
            <select name="periodical_type" class="input w-full text-xs" required>
              <option value="newspaper">Newspaper</option>
              <option value="magazine" selected>Magazine</option>
              <option value="journal">Journal</option>
              <option value="newsletter">Newsletter</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Frequency *</label>
            <select name="frequency" class="input w-full text-xs" required>
              <option value="daily">Daily</option>
              <option value="weekly">Weekly</option>
              <option value="fortnightly">Fortnightly</option>
              <option value="monthly" selected>Monthly</option>
              <option value="quarterly">Quarterly</option>
              <option value="annual">Annual</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Language</label>
            <input type="text" name="language" value="English" class="input w-full text-xs">
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Publisher</label>
            <input type="text" name="publisher" placeholder="Publisher" class="input w-full text-xs">
          </div>
        </div>

        <div class="pt-1">
          <button type="submit" class="btn btn-primary btn-sm w-full text-xs font-semibold">Save Periodical</button>
        </div>
      </form>
    </div>
  </div>

  {{-- Periodicals Master List --}}
  <div class="card overflow-hidden">
    <div class="p-4 border-b border-slate-100 flex items-center justify-between">
      <h3 class="text-sm font-bold text-slate-900">Periodicals Master Catalogue</h3>
      <span class="text-xs text-slate-500">{{ $periodicals->total() }} Registered Periodicals</span>
    </div>

    <div class="table-wrap">
      <table class="w-full text-left text-sm">
        <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase text-slate-600">
          <tr>
            <th class="py-3 px-4">Title</th>
            <th class="py-3 px-4">Type</th>
            <th class="py-3 px-4">Frequency</th>
            <th class="py-3 px-4">Language</th>
            <th class="py-3 px-4">Active Subscription</th>
            <th class="py-3 px-4">Shelf Location</th>
            <th class="py-3 px-4 text-center">Receipts Logged</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @forelse($periodicals as $per)
          <tr class="hover:bg-slate-50/80 transition">
            <td class="py-3.5 px-4 font-semibold text-slate-800 text-xs">{{ $per->title }}</td>
            <td class="py-3.5 px-4 text-xs">
              <span class="badge {{ $per->periodical_type === 'newspaper' ? 'badge-blue' : 'badge-green' }} text-[11px]">
                {{ ucfirst($per->periodical_type) }}
              </span>
            </td>
            <td class="py-3.5 px-4 text-xs text-slate-600">{{ ucfirst($per->frequency) }}</td>
            <td class="py-3.5 px-4 text-xs text-slate-600">{{ $per->language }}</td>
            <td class="py-3.5 px-4 text-xs">
              @if($per->activeSubscription)
                <span class="text-emerald-700 font-semibold">Valid until {{ $per->activeSubscription->end_date?->format('d M Y') }}</span>
              @else
                <span class="text-slate-400 text-xs">No active subscription</span>
              @endif
            </td>
            <td class="py-3.5 px-4 text-xs text-slate-600">
              {{ $per->rack ? "Rack {$per->rack->rack_number}" : 'Reading Room' }}
            </td>
            <td class="py-3.5 px-4 text-center text-xs font-bold text-slate-800">
              {{ $per->receipts->count() }} issues
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="text-center py-8 text-slate-400 text-xs">No periodicals or newspapers added yet.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Today's Incoming Periodicals Register --}}
  <div class="card overflow-hidden">
    <div class="p-4 border-b border-slate-100">
      <h3 class="text-sm font-bold text-slate-900">Today's Received Papers & Issues ({{ $todayReceipts->count() }})</h3>
    </div>
    <div class="table-wrap">
      <table class="w-full text-left text-sm">
        <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase text-slate-600">
          <tr>
            <th class="py-3 px-4">Periodical</th>
            <th class="py-3 px-4">Issue Date</th>
            <th class="py-3 px-4">Volume / Issue</th>
            <th class="py-3 px-4">Copies</th>
            <th class="py-3 px-4">Condition</th>
            <th class="py-3 px-4">Logged At</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @forelse($todayReceipts as $rec)
          <tr class="hover:bg-slate-50/80 transition text-xs">
            <td class="py-3 px-4 font-semibold text-slate-800">{{ $rec->periodical?->title }}</td>
            <td class="py-3 px-4 text-slate-600">{{ $rec->issue_date?->format('d M Y') }}</td>
            <td class="py-3 px-4 text-slate-500">{{ $rec->volume_no ?: '—' }}</td>
            <td class="py-3 px-4 font-bold text-slate-800">{{ $rec->copies_received }}</td>
            <td class="py-3 px-4">
              <span class="badge badge-green text-[10px]">Good</span>
            </td>
            <td class="py-3 px-4 text-slate-400">{{ $rec->created_at?->format('H:i') }}</td>
          </tr>
          @empty
          <tr>
            <td colspan="6" class="text-center py-6 text-slate-400 text-xs">No papers or magazines received yet today.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection

