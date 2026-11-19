@extends('layouts.app')
@section('title', 'Wastage, Damage & Disposal')

@section('content')
<div class="space-y-6">

  {{-- Header --}}
  <div class="flex items-center justify-between flex-wrap gap-4">
    <div>
      <h1 class="page-title text-2xl font-bold text-slate-900">Damage, Loss & Write-off Register</h1>
      <p class="page-subtitle text-slate-500 text-sm mt-0.5">Formal Incident Logging, Condition Tracking & Disposal Approvals</p>
    </div>
  </div>

  {{-- Sub-Navigation --}}
  @include('library.partials.nav')

  @if(session('success'))
    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">
      {{ session('success') }}
    </div>
  @endif

  {{-- Incident Report Form --}}
  <div class="card p-5 border-l-4 border-l-rose-600" x-data="{ openD: false }">
    <div class="flex items-center justify-between">
      <div>
        <h3 class="font-bold text-slate-900 text-sm">Report Damaged or Lost Item</h3>
        <p class="text-xs text-slate-500">File an incident report for books with torn pages, water damage, or lost copies.</p>
      </div>
      <button type="button" @click="openD = !openD" class="btn btn-secondary btn-sm text-xs" x-text="openD ? 'Close' : '+ File Damage Incident'"></button>
    </div>

    <form x-show="openD" x-transition action="{{ route('library.wastage.damage.store') }}" method="POST" class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
      @csrf
      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Select Damaged Book Copy</label>
        <select name="book_copy_id" class="input w-full text-xs">
          <option value="">Select Copy (if applicable)</option>
          @foreach($damagedCopies as $dc)
          <option value="{{ $dc->id }}">{{ $dc->book?->title }} ({{ $dc->accession_number }})</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Incident Type *</label>
        <select name="incident_type" class="input w-full text-xs" required>
          <option value="torn_pages">Torn Pages</option>
          <option value="water_damaged">Water Damaged</option>
          <option value="binding_broken">Binding Broken</option>
          <option value="missing_pages">Missing Pages</option>
          <option value="vandalized">Vandalized / Scribbled</option>
          <option value="lost_by_borrower">Lost by Borrower</option>
          <option value="natural_wear">Natural Wear & Tear</option>
          <option value="other">Other Incident</option>
        </select>
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Incident Date *</label>
        <input type="date" name="reported_date" value="{{ date('Y-m-d') }}" class="input w-full text-xs" required>
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Proposed Action *</label>
        <select name="action_proposed" class="input w-full text-xs" required>
          <option value="repair">Send for Rebinding / Repair</option>
          <option value="fine_and_replace">Charge Fine & Replace</option>
          <option value="write_off">Recommend for Write-off</option>
          <option value="dispose">Scrap Disposal</option>
        </select>
      </div>

      <div class="sm:col-span-3">
        <label class="block text-xs font-semibold text-slate-700 mb-1">Damage Description *</label>
        <input type="text" name="damage_description" placeholder="Describe the physical condition and extent of damage" class="input w-full text-xs" required>
      </div>

      <div class="flex items-end">
        <button type="submit" class="btn btn-primary btn-sm w-full text-xs font-semibold">Submit Report</button>
      </div>
    </form>
  </div>

  {{-- Incident Reports & Disposals Tabs --}}
  <div x-data="{ tab: 'reports' }" class="space-y-4">
    <div class="flex border-b border-slate-200 gap-6 text-sm font-medium">
      <button type="button" @click="tab = 'reports'" :class="tab === 'reports' ? 'border-primary-600 text-primary-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-800'" class="pb-3 border-b-2 transition">
        Incident Reports ({{ $damageReports->total() }})
      </button>
      <button type="button" @click="tab = 'disposals'" :class="tab === 'disposals' ? 'border-primary-600 text-primary-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-800'" class="pb-3 border-b-2 transition">
        Approved Write-offs & Disposals ({{ $disposals->total() }})
      </button>
    </div>

    {{-- TAB 1: INCIDENTS --}}
    <div x-show="tab === 'reports'" class="card overflow-hidden">
      <div class="table-wrap">
        <table class="w-full text-left text-sm">
          <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase text-slate-600">
            <tr>
              <th class="py-3 px-4">Report #</th>
              <th class="py-3 px-4">Item Details</th>
              <th class="py-3 px-4">Incident Type</th>
              <th class="py-3 px-4">Proposed Action</th>
              <th class="py-3 px-4">Reported Date</th>
              <th class="py-3 px-4">Status</th>
              <th class="py-3 px-4 text-right">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @forelse($damageReports as $rep)
            <tr class="hover:bg-slate-50/80 transition text-xs">
              <td class="py-3 px-4 font-mono font-bold text-primary-600">{{ $rep->report_number }}</td>
              <td class="py-3 px-4 max-w-xs">
                @if($rep->bookCopy)
                  <p class="font-semibold text-slate-800 truncate">{{ $rep->bookCopy->book?->title }}</p>
                  <p class="text-slate-400 font-mono text-[10px]">{{ $rep->bookCopy->accession_number }}</p>
                @elseif($rep->asset)
                  <p class="font-semibold text-slate-800">{{ $rep->asset->name }}</p>
                @endif
              </td>
              <td class="py-3 px-4">{{ ucfirst(str_replace('_', ' ', $rep->incident_type)) }}</td>
              <td class="py-3 px-4 text-slate-600">{{ ucfirst(str_replace('_', ' ', $rep->action_proposed)) }}</td>
              <td class="py-3 px-4 text-slate-500">{{ $rep->reported_date?->format('d M Y') }}</td>
              <td class="py-3 px-4">
                <span class="badge {{ $rep->status === 'disposed' ? 'badge-slate' : 'badge-amber' }}">
                  {{ ucfirst(str_replace('_', ' ', $rep->status)) }}
                </span>
              </td>
              <td class="py-3 px-4 text-right whitespace-nowrap" x-data="{ openDisp: false }">
                @if($rep->status !== 'disposed')
                <button type="button" @click="openDisp = !openDisp" class="btn btn-secondary btn-xs text-xs">
                  Approve Write-off
                </button>

                <div x-show="openDisp" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4">
                  <div class="bg-white rounded-xl max-w-md w-full p-5 text-left shadow-xl" @click.away="openDisp = false">
                    <h4 class="font-bold text-slate-900 text-sm mb-1">Approve Disposal & Write-Off</h4>
                    <p class="text-xs text-slate-500 mb-3">Record formal disposal method and scrap value.</p>
                    <form action="{{ route('library.wastage.write-off', $rep->id) }}" method="POST" class="space-y-3">
                      @csrf
                      <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Disposal Method *</label>
                        <select name="disposal_method" class="input w-full text-xs" required>
                          <option value="recycle_paper">Recycle as Scrap Paper</option>
                          <option value="scrap_sale">Scrap Sale</option>
                          <option value="donation">Donation to Public Library</option>
                          <option value="destruction">Destruction / Incineration</option>
                        </select>
                      </div>
                      <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Disposal Date</label>
                        <input type="date" name="disposal_date" value="{{ date('Y-m-d') }}" class="input w-full text-xs" required>
                      </div>
                      <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Scrap Value Recovered (₹)</label>
                        <input type="number" step="0.01" name="scrap_value_recovered" value="0" class="input w-full text-xs">
                      </div>
                      <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Justification Reason *</label>
                        <input type="text" name="justification" placeholder="e.g. Beyond economical repair, approved by principal" class="input w-full text-xs" required>
                      </div>
                      <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="openDisp = false" class="btn btn-secondary btn-xs">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-xs">Approve & Write-off</button>
                      </div>
                    </form>
                  </div>
                </div>
                @else
                <span class="text-slate-400 text-xs">Disposed #{{ $rep->disposalRecord?->disposal_number }}</span>
                @endif
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="7" class="text-center py-8 text-slate-400 text-xs">No damage incidents recorded.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- TAB 2: DISPOSALS --}}
    <div x-show="tab === 'disposals'" style="display: none;" class="card overflow-hidden">
      <div class="table-wrap">
        <table class="w-full text-left text-sm">
          <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase text-slate-600">
            <tr>
              <th class="py-3 px-4">Disposal #</th>
              <th class="py-3 px-4">Item</th>
              <th class="py-3 px-4">Method</th>
              <th class="py-3 px-4">Disposal Date</th>
              <th class="py-3 px-4">Original Value</th>
              <th class="py-3 px-4">Scrap Recovered</th>
              <th class="py-3 px-4">Authorized By</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @forelse($disposals as $disp)
            <tr class="hover:bg-slate-50/80 transition text-xs">
              <td class="py-3 px-4 font-mono font-bold text-slate-700">{{ $disp->disposal_number }}</td>
              <td class="py-3 px-4 font-semibold text-slate-800">
                {{ $disp->bookCopy?->book?->title ?: ($disp->asset?->name ?: 'Item') }}
              </td>
              <td class="py-3 px-4">{{ ucfirst(str_replace('_', ' ', $disp->disposal_method)) }}</td>
              <td class="py-3 px-4 text-slate-500">{{ $disp->disposal_date?->format('d M Y') }}</td>
              <td class="py-3 px-4 font-mono text-slate-700">₹{{ number_format($disp->original_value, 2) }}</td>
              <td class="py-3 px-4 font-mono font-bold text-emerald-600">₹{{ number_format($disp->scrap_value_recovered, 2) }}</td>
              <td class="py-3 px-4 text-slate-500">{{ $disp->approver?->name ?: 'Librarian' }}</td>
            </tr>
            @empty
            <tr>
              <td colspan="7" class="text-center py-8 text-slate-400 text-xs">No write-offs or disposals recorded.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>
@endsection

