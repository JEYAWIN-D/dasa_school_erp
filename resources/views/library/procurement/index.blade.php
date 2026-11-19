@extends('layouts.app')
@section('title', 'Library Procurement & Suppliers')

@section('content')
<div class="space-y-6">

  {{-- Header --}}
  <div class="flex items-center justify-between flex-wrap gap-4">
    <div>
      <h1 class="page-title text-2xl font-bold text-slate-900">Procurement & Inventory Acquisition</h1>
      <p class="page-subtitle text-slate-500 text-sm mt-0.5">Purchase Orders (PO), Goods Receipt Notes (GRN) & Vendor Master</p>
    </div>
    <div class="flex gap-2">
      <a href="{{ route('library.procurement.po.create') }}" class="btn btn-secondary btn-sm flex items-center gap-1.5">
        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Purchase Order
      </a>
      <a href="{{ route('library.procurement.grn.create') }}" class="btn btn-primary btn-sm flex items-center gap-1.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Receive GRN (Stock Injection)
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

  {{-- Tabs: POs, GRNs, Vendors --}}
  <div x-data="{ tab: 'po' }" class="space-y-4">
    <div class="flex border-b border-slate-200 gap-6 text-sm font-medium">
      <button type="button" @click="tab = 'po'" :class="tab === 'po' ? 'border-primary-600 text-primary-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-800'" class="pb-3 border-b-2 transition">
        Purchase Orders ({{ $purchaseOrders->total() }})
      </button>
      <button type="button" @click="tab = 'grn'" :class="tab === 'grn' ? 'border-primary-600 text-primary-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-800'" class="pb-3 border-b-2 transition">
        Goods Receipts / GRN ({{ $goodsReceipts->total() }})
      </button>
      <button type="button" @click="tab = 'vendors'" :class="tab === 'vendors' ? 'border-primary-600 text-primary-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-800'" class="pb-3 border-b-2 transition">
        Vendor / Supplier Master ({{ $suppliers->count() }})
      </button>
    </div>

    {{-- TAB 1: PURCHASE ORDERS --}}
    <div x-show="tab === 'po'" class="card overflow-hidden">
      <div class="table-wrap">
        <table class="w-full text-left text-sm">
          <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase text-slate-600">
            <tr>
              <th class="py-3 px-4">PO Number</th>
              <th class="py-3 px-4">Supplier</th>
              <th class="py-3 px-4">Order Date</th>
              <th class="py-3 px-4">Items / Qty</th>
              <th class="py-3 px-4">Grand Total</th>
              <th class="py-3 px-4">Status</th>
              <th class="py-3 px-4 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @forelse($purchaseOrders as $po)
            <tr class="hover:bg-slate-50/80 transition">
              <td class="py-3.5 px-4 font-mono text-xs font-bold text-primary-600">{{ $po->po_number }}</td>
              <td class="py-3.5 px-4 text-xs font-medium text-slate-800">{{ $po->supplier?->name }}</td>
              <td class="py-3.5 px-4 text-xs text-slate-500">{{ $po->order_date?->format('d M Y') }}</td>
              <td class="py-3.5 px-4 text-xs text-slate-600 font-medium">
                {{ $po->items->count() }} titles ({{ $po->items->sum('quantity_ordered') }} copies)
              </td>
              <td class="py-3.5 px-4 text-xs font-bold text-slate-900">₹{{ number_format($po->grand_total, 2) }}</td>
              <td class="py-3.5 px-4 whitespace-nowrap">
                @if($po->status === 'draft')
                  <span class="badge bg-slate-100 text-slate-600 text-xs">Draft</span>
                @elseif($po->status === 'approved')
                  <span class="badge bg-blue-50 text-blue-700 text-xs font-semibold">Approved</span>
                @elseif($po->status === 'partially_received')
                  <span class="badge bg-amber-50 text-amber-700 text-xs font-semibold">Partially Received</span>
                @elseif($po->status === 'completed')
                  <span class="badge bg-emerald-50 text-emerald-700 text-xs font-semibold">Completed</span>
                @else
                  <span class="badge bg-slate-100 text-slate-600 text-xs">{{ ucfirst($po->status) }}</span>
                @endif
              </td>
              <td class="py-3.5 px-4 text-right whitespace-nowrap">
                <div class="flex items-center justify-end gap-1.5">
                  @if($po->status === 'draft' || $po->status === 'submitted')
                  <form action="{{ route('library.procurement.po.approve', $po->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-xs text-xs">Approve PO</button>
                  </form>
                  @endif

                  @if($po->status === 'approved' || $po->status === 'partially_received')
                  <a href="{{ route('library.procurement.grn.create', ['po_id' => $po->id]) }}" class="btn btn-secondary btn-xs text-xs">
                    Receive GRN
                  </a>
                  @endif
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="7" class="text-center py-8 text-slate-400 text-xs">No purchase orders created yet.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      @if($purchaseOrders->hasPages())
      <div class="p-4 border-t border-slate-100">{{ $purchaseOrders->links() }}</div>
      @endif
    </div>

    {{-- TAB 2: GOODS RECEIPTS (GRN) --}}
    <div x-show="tab === 'grn'" style="display: none;" class="card overflow-hidden">
      <div class="table-wrap">
        <table class="w-full text-left text-sm">
          <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase text-slate-600">
            <tr>
              <th class="py-3 px-4">GRN #</th>
              <th class="py-3 px-4">Invoice #</th>
              <th class="py-3 px-4">Supplier</th>
              <th class="py-3 px-4">Receipt Date</th>
              <th class="py-3 px-4">Copies Received</th>
              <th class="py-3 px-4">Invoice Amount</th>
              <th class="py-3 px-4">Verified By</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @forelse($goodsReceipts as $grn)
            <tr class="hover:bg-slate-50/80 transition">
              <td class="py-3.5 px-4 font-mono text-xs font-bold text-primary-600">{{ $grn->grn_number }}</td>
              <td class="py-3.5 px-4 font-mono text-xs text-slate-700">{{ $grn->invoice_number }}</td>
              <td class="py-3.5 px-4 text-xs font-medium text-slate-800">{{ $grn->supplier?->name }}</td>
              <td class="py-3.5 px-4 text-xs text-slate-500">{{ $grn->receipt_date?->format('d M Y') }}</td>
              <td class="py-3.5 px-4 text-xs font-bold text-emerald-600">{{ $grn->total_items_received }} copies</td>
              <td class="py-3.5 px-4 text-xs font-bold text-slate-900">₹{{ number_format($grn->total_amount, 2) }}</td>
              <td class="py-3.5 px-4 text-xs text-slate-500">{{ $grn->verifier?->name ?: 'Admin' }}</td>
            </tr>
            @empty
            <tr>
              <td colspan="7" class="text-center py-8 text-slate-400 text-xs">No GRN records logged yet.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      @if($goodsReceipts->hasPages())
      <div class="p-4 border-t border-slate-100">{{ $goodsReceipts->links() }}</div>
      @endif
    </div>

    {{-- TAB 3: SUPPLIERS DIRECTORY --}}
    <div x-show="tab === 'vendors'" style="display: none;" class="space-y-4">
      <div class="card p-5 border-l-4 border-l-primary-600" x-data="{ openVendor: false }">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="font-bold text-slate-900 text-sm">Add Book Vendor / Publisher</h3>
            <p class="text-xs text-slate-500">Register book suppliers and distributors for purchase orders.</p>
          </div>
          <button type="button" @click="openVendor = !openVendor" class="btn btn-secondary btn-sm" x-text="openVendor ? 'Close' : '+ Add Supplier'"></button>
        </div>

        <form x-show="openVendor" x-transition action="{{ route('library.procurement.suppliers.store') }}" method="POST" class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
          @csrf
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Company / Vendor Name *</label>
            <input type="text" name="name" placeholder="e.g. Oxford Book Depot" class="input w-full text-sm" required>
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Contact Person</label>
            <input type="text" name="contact_person" placeholder="Manager Name" class="input w-full text-sm">
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Phone Number</label>
            <input type="text" name="phone" placeholder="+91..." class="input w-full text-sm">
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Email Address</label>
            <input type="email" name="email" placeholder="sales@vendor.com" class="input w-full text-sm">
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">GST Number</label>
            <input type="text" name="gst_number" placeholder="GSTIN..." class="input w-full text-sm">
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">City & State</label>
            <div class="flex gap-2">
              <input type="text" name="city" placeholder="City" class="input w-1/2 text-sm">
              <input type="text" name="state" placeholder="State" class="input w-1/2 text-sm">
            </div>
          </div>
          <div class="sm:col-span-2 flex items-end gap-2">
            <input type="text" name="address" placeholder="Postal Address" class="input flex-1 text-sm">
            <button type="submit" class="btn btn-primary btn-sm flex-shrink-0">Save Supplier</button>
          </div>
        </form>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($suppliers as $sup)
        <div class="card p-4 space-y-2">
          <div class="flex items-center justify-between">
            <h4 class="font-bold text-slate-900 text-sm truncate">{{ $sup->name }}</h4>
            <span class="badge bg-emerald-50 text-emerald-700 text-[10px]">Active</span>
          </div>
          <p class="text-xs text-slate-500">{{ $sup->contact_person ? "Contact: {$sup->contact_person}" : 'No contact specified' }}</p>
          <div class="pt-2 border-t border-slate-100 text-xs text-slate-600 space-y-0.5">
            <p>📞 {{ $sup->phone ?: '—' }}</p>
            <p>✉️ {{ $sup->email ?: '—' }}</p>
            @if($sup->gst_number)<p class="font-mono text-[11px]">GST: {{ $sup->gst_number }}</p>@endif
          </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-8 text-slate-400 text-sm">No suppliers registered yet.</div>
        @endforelse
      </div>
    </div>
  </div>

</div>
@endsection

