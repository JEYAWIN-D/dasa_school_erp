@extends('layouts.app')
@section('title', 'Library Assets & Facilities')

@section('content')
<div class="space-y-6">

  {{-- Header --}}
  <div class="flex items-center justify-between flex-wrap gap-4">
    <div>
      <h1 class="page-title text-2xl font-bold text-slate-900">Library Assets & Facilities Management</h1>
      <p class="page-subtitle text-slate-500 text-sm mt-0.5">Hardware, Scanners, Furniture, Maintenance Ledger & Depreciation</p>
    </div>
  </div>

  {{-- Sub-Navigation --}}
  @include('library.partials.nav')

  @if(session('success'))
    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">
      {{ session('success') }}
    </div>
  @endif

  {{-- Warranty Alerts --}}
  @if(count($expiringWarranties))
  <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-sm flex items-start gap-3">
    <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    <div>
      <h4 class="font-bold text-xs uppercase tracking-wide">Upcoming Warranty Expirations</h4>
      <div class="mt-1 flex flex-wrap gap-2">
        @foreach($expiringWarranties as $w)
        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-white border border-amber-200 text-xs">
          <strong>{{ $w->name }}</strong> [{{ $w->asset_code }}] warranty ends {{ $w->warranty_until?->format('d M Y') }}
        </span>
        @endforeach
      </div>
    </div>
  </div>
  @endif

  {{-- Register Asset Form --}}
  <div class="card p-5 border-l-4 border-l-primary-600" x-data="{ openAsset: false }">
    <div class="flex items-center justify-between">
      <div>
        <h3 class="font-bold text-slate-900 text-sm">Register New Library Asset</h3>
        <p class="text-xs text-slate-500">Record computers, scanners, RFID hardware, or reading furniture.</p>
      </div>
      <button type="button" @click="openAsset = !openAsset" class="btn btn-secondary btn-sm text-xs" x-text="openAsset ? 'Close' : '+ Register Asset'"></button>
    </div>

    <form x-show="openAsset" x-transition action="{{ route('library.assets.store') }}" method="POST" class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
      @csrf
      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Asset Tag / Code</label>
        <input type="text" name="asset_code" placeholder="Auto-generated if blank" class="input w-full text-xs font-mono">
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Asset Name *</label>
        <input type="text" name="name" placeholder="e.g. Zebra Barcode Scanner, Dell OPAC PC" class="input w-full text-xs" required>
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Category *</label>
        <select name="category" class="input w-full text-xs" required>
          <option value="scanner">Barcode / QR Scanner</option>
          <option value="computer">Computer / Terminal</option>
          <option value="printer">Printer / Label Maker</option>
          <option value="furniture">Furniture / Shelving</option>
          <option value="projector">Projector / Screen</option>
          <option value="appliance">Appliance</option>
          <option value="other">Other</option>
        </select>
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Brand & Model</label>
        <input type="text" name="brand_model" placeholder="e.g. Honeywell Voyager 1400g" class="input w-full text-xs">
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Purchase Date</label>
        <input type="date" name="purchase_date" value="{{ date('Y-m-d') }}" class="input w-full text-xs">
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Purchase Cost (₹)</label>
        <input type="number" step="0.01" name="purchase_cost" placeholder="₹ Amount" class="input w-full text-xs">
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Warranty Until</label>
        <input type="date" name="warranty_until" class="input w-full text-xs">
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Location Room</label>
        <input type="text" name="location_room" value="Central Library Desk" class="input w-full text-xs">
      </div>

      <div class="sm:col-span-4 flex justify-end pt-1">
        <button type="submit" class="btn btn-primary btn-sm text-xs font-semibold">Save Asset</button>
      </div>
    </form>
  </div>

  {{-- Asset Master Table --}}
  <div class="card overflow-hidden">
    <div class="table-wrap">
      <table class="w-full text-left text-sm">
        <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase text-slate-600">
          <tr>
            <th class="py-3 px-4">Asset Code</th>
            <th class="py-3 px-4">Name & Brand</th>
            <th class="py-3 px-4">Category</th>
            <th class="py-3 px-4">Location</th>
            <th class="py-3 px-4">Cost / Value</th>
            <th class="py-3 px-4">Warranty</th>
            <th class="py-3 px-4">Status</th>
            <th class="py-3 px-4 text-right">Maintenance</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @forelse($assets as $asset)
          <tr class="hover:bg-slate-50/80 transition text-xs">
            <td class="py-3.5 px-4 font-mono font-bold text-primary-600">{{ $asset->asset_code }}</td>
            <td class="py-3.5 px-4">
              <p class="font-semibold text-slate-800">{{ $asset->name }}</p>
              @if($asset->brand_model)<p class="text-slate-400 text-[11px]">{{ $asset->brand_model }}</p>@endif
            </td>
            <td class="py-3.5 px-4">
              <span class="badge bg-slate-100 text-slate-700">{{ ucfirst($asset->category) }}</span>
            </td>
            <td class="py-3.5 px-4 text-slate-600">{{ $asset->location_room ?: 'Library' }}</td>
            <td class="py-3.5 px-4 font-semibold text-slate-800">
              ₹{{ number_format($asset->purchase_cost, 2) }}
            </td>
            <td class="py-3.5 px-4 text-slate-500">
              {{ $asset->warranty_until ? $asset->warranty_until->format('d M Y') : '—' }}
            </td>
            <td class="py-3.5 px-4">
              <span class="badge {{ $asset->status === 'working' ? 'badge-green' : 'badge-amber' }}">
                {{ ucfirst(str_replace('_', ' ', $asset->status)) }}
              </span>
            </td>
            <td class="py-3.5 px-4 text-right whitespace-nowrap" x-data="{ maintOpen: false }">
              <button type="button" @click="maintOpen = !maintOpen" class="btn btn-secondary btn-xs text-xs">
                Log Service
              </button>

              <div x-show="maintOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4">
                <div class="bg-white rounded-xl max-w-md w-full p-5 text-left shadow-xl" @click.away="maintOpen = false">
                  <h4 class="font-bold text-slate-900 text-sm mb-1">Service & Maintenance: {{ $asset->name }}</h4>
                  <p class="text-xs text-slate-500 mb-3">Record maintenance cost and actions taken.</p>
                  <form action="{{ route('library.assets.maintenance.store', $asset->id) }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                      <label class="block text-xs font-semibold text-slate-700 mb-1">Maintenance Date</label>
                      <input type="date" name="maintenance_date" value="{{ date('Y-m-d') }}" class="input w-full text-xs" required>
                    </div>
                    <div>
                      <label class="block text-xs font-semibold text-slate-700 mb-1">Cost Incurred (₹)</label>
                      <input type="number" step="0.01" name="cost" value="0" class="input w-full text-xs" required>
                    </div>
                    <div>
                      <label class="block text-xs font-semibold text-slate-700 mb-1">Issue / Service Done</label>
                      <input type="text" name="issue_description" placeholder="e.g. Laser lens cleaned, cable replaced" class="input w-full text-xs" required>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                      <button type="button" @click="maintOpen = false" class="btn btn-secondary btn-xs">Cancel</button>
                      <button type="submit" class="btn btn-primary btn-xs">Save Record</button>
                    </div>
                  </form>
                </div>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="text-center py-8 text-slate-400 text-xs">No library assets recorded yet.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection

