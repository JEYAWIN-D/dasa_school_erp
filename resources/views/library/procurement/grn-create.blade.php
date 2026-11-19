@extends('layouts.app')
@section('title', 'Receive GRN — Goods Receipt')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

  <div class="flex items-center gap-4">
    <a href="{{ route('library.procurement.index') }}" class="btn btn-secondary btn-sm">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      Back to Procurement
    </a>
    <div>
      <h1 class="page-title text-xl font-bold text-slate-900">Receive Goods Receipt Note (GRN)</h1>
      <p class="page-subtitle text-slate-500 text-xs">Verify Vendor Delivery & Inject Physical Copies into Library Inventory</p>
    </div>
  </div>

  <form action="{{ route('library.procurement.grn.store') }}" method="POST" class="card p-6 space-y-6"
        x-data="{
          items: [
            @if(isset($selectedPo) && $selectedPo->items->count())
              @foreach($selectedPo->items as $pi)
              {
                purchase_order_item_id: {{ $pi->id }},
                book_title: '{{ addslashes($pi->book_title) }}',
                isbn: '{{ $pi->isbn }}',
                author: '{{ addslashes($pi->author) }}',
                quantity_received: {{ max(1, $pi->quantity_ordered - $pi->quantity_received) }},
                unit_cost: {{ $pi->unit_price }},
                shelf_id: ''
              },
              @endforeach
            @else
              {
                purchase_order_item_id: null,
                book_title: '',
                isbn: '',
                author: '',
                quantity_received: 5,
                unit_cost: 200,
                shelf_id: ''
              }
            @endif
          ],
          addItem() {
            this.items.push({ purchase_order_item_id: null, book_title: '', isbn: '', author: '', quantity_received: 1, unit_cost: 150, shelf_id: '' });
          },
          removeItem(idx) {
            if (this.items.length > 1) {
              this.items.splice(idx, 1);
            }
          }
        }">
    @csrf

    @if(isset($selectedPo))
    <input type="hidden" name="purchase_order_id" value="{{ $selectedPo->id }}">
    <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg text-xs text-blue-800 flex items-center justify-between">
      <span>Receiving delivery against <strong>PO #{{ $selectedPo->po_number }}</strong></span>
      <span>Vendor: <strong>{{ $selectedPo->supplier?->name }}</strong></span>
    </div>
    @endif

    {{-- Invoice Details --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Supplier / Vendor *</label>
        <select name="supplier_id" class="input w-full text-sm" required>
          <option value="">Select Supplier</option>
          @foreach($suppliers as $sup)
          <option value="{{ $sup->id }}" @selected(isset($selectedPo) && $selectedPo->supplier_id == $sup->id)>
            {{ $sup->name }}
          </option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Vendor Invoice # *</label>
        <input type="text" name="invoice_number" placeholder="e.g. INV-2026-99" class="input w-full text-sm" required>
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Invoice Date *</label>
        <input type="date" name="invoice_date" value="{{ date('Y-m-d') }}" class="input w-full text-sm" required>
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Physical Receipt Date *</label>
        <input type="date" name="receipt_date" value="{{ date('Y-m-d') }}" class="input w-full text-sm" required>
      </div>
    </div>

    {{-- Received Items --}}
    <div class="space-y-3">
      <div class="flex items-center justify-between">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-600">Delivered Books & Location Assignment</h3>
        <button type="button" @click="addItem()" class="btn btn-secondary btn-xs text-xs font-semibold">+ Add Book Title</button>
      </div>

      <div class="table-wrap border border-slate-200 rounded-lg overflow-hidden">
        <table class="w-full text-left text-xs">
          <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase">
            <tr>
              <th class="p-3 w-1/3">Book Title *</th>
              <th class="p-3">ISBN</th>
              <th class="p-3">Author</th>
              <th class="p-3 w-20">Qty Recv *</th>
              <th class="p-3 w-24">Unit Cost (₹) *</th>
              <th class="p-3 w-40">Shelf Location</th>
              <th class="p-3 w-10"></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <template x-for="(item, idx) in items" :key="idx">
              <tr>
                <td class="p-2">
                  <input type="hidden" :name="'items[' + idx + '][purchase_order_item_id]'" :value="item.purchase_order_item_id">
                  <input type="text" :name="'items[' + idx + '][book_title]'" x-model="item.book_title" placeholder="Book Title" class="input w-full text-xs" required>
                </td>
                <td class="p-2">
                  <input type="text" :name="'items[' + idx + '][isbn]'" x-model="item.isbn" placeholder="ISBN" class="input w-full text-xs">
                </td>
                <td class="p-2">
                  <input type="text" :name="'items[' + idx + '][author]'" x-model="item.author" placeholder="Author" class="input w-full text-xs">
                </td>
                <td class="p-2">
                  <input type="number" :name="'items[' + idx + '][quantity_received]'" x-model="item.quantity_received" min="1" class="input w-full text-xs" required>
                </td>
                <td class="p-2">
                  <input type="number" step="0.01" :name="'items[' + idx + '][unit_cost]'" x-model="item.unit_cost" min="0" class="input w-full text-xs" required>
                </td>
                <td class="p-2">
                  <select :name="'items[' + idx + '][shelf_id]'" x-model="item.shelf_id" class="input w-full text-xs">
                    <option value="">Unassigned</option>
                    @foreach($shelves as $sh)
                    <option value="{{ $sh->id }}">Rack {{ $sh->rack?->rack_number }} / Shelf {{ $sh->shelf_number }}</option>
                    @endforeach
                  </select>
                </td>
                <td class="p-2 text-center">
                  <button type="button" @click="removeItem(idx)" class="text-red-500 hover:text-red-700 font-bold">&times;</button>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
    </div>

    <div>
      <label class="block text-xs font-semibold text-slate-700 mb-1">Receipt Remarks / Package Condition</label>
      <input type="text" name="remarks" placeholder="e.g. Delivered in 2 cardboard boxes, condition verified intact" class="input w-full text-sm">
    </div>

    <div class="pt-2">
      <button type="submit" class="btn btn-primary w-full py-2.5 font-semibold text-sm">
        Confirm Delivery & Generate Physical Accession Barcodes
      </button>
    </div>
  </form>

</div>
@endsection

