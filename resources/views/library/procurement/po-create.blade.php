@extends('layouts.app')
@section('title', 'Create Purchase Order — Library')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

  <div class="flex items-center gap-4">
    <a href="{{ route('library.procurement.index') }}" class="btn btn-secondary btn-sm">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      Back to Procurement
    </a>
    <div>
      <h1 class="page-title text-xl font-bold text-slate-900">New Book Purchase Order</h1>
      <p class="page-subtitle text-slate-500 text-xs">Official School Procurement Order</p>
    </div>
  </div>

  <form action="{{ route('library.procurement.po.store') }}" method="POST" class="card p-6 space-y-6"
        x-data="{
          items: [
            { book_title: '', isbn: '', author: '', quantity_ordered: 5, unit_price: 250, discount_percent: 10 }
          ],
          addItem() {
            this.items.push({ book_title: '', isbn: '', author: '', quantity_ordered: 5, unit_price: 250, discount_percent: 0 });
          },
          removeItem(index) {
            if (this.items.length > 1) {
              this.items.splice(index, 1);
            }
          },
          calcSubtotal(item) {
            const qty = parseFloat(item.quantity_ordered) || 0;
            const price = parseFloat(item.unit_price) || 0;
            const disc = parseFloat(item.discount_percent) || 0;
            return (qty * price) * (1 - (disc / 100));
          },
          get grandTotal() {
            return this.items.reduce((sum, item) => sum + this.calcSubtotal(item), 0);
          }
        }">
    @csrf

    {{-- Header Info --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Book Vendor / Supplier *</label>
        <select name="supplier_id" class="input w-full text-sm" required>
          <option value="">Select Supplier</option>
          @foreach($suppliers as $sup)
          <option value="{{ $sup->id }}">{{ $sup->name }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Order Date *</label>
        <input type="date" name="order_date" value="{{ date('Y-m-d') }}" class="input w-full text-sm" required>
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Expected Delivery Date</label>
        <input type="date" name="expected_delivery_date" class="input w-full text-sm">
      </div>
    </div>

    {{-- Line Items Table --}}
    <div class="space-y-3">
      <div class="flex items-center justify-between">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-600">Order Line Items</h3>
        <button type="button" @click="addItem()" class="btn btn-secondary btn-xs text-xs font-semibold">+ Add Book Title</button>
      </div>

      <div class="table-wrap border border-slate-200 rounded-lg overflow-hidden">
        <table class="w-full text-left text-xs">
          <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase">
            <tr>
              <th class="p-3 w-1/3">Book Title *</th>
              <th class="p-3">ISBN</th>
              <th class="p-3">Author</th>
              <th class="p-3 w-20">Qty *</th>
              <th class="p-3 w-24">Price (₹) *</th>
              <th class="p-3 w-20">Disc %</th>
              <th class="p-3 w-28 text-right">Subtotal</th>
              <th class="p-3 w-10"></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <template x-for="(item, idx) in items" :key="idx">
              <tr>
                <td class="p-2">
                  <input type="text" :name="'items[' + idx + '][book_title]'" x-model="item.book_title" placeholder="Book Title" class="input w-full text-xs" required>
                </td>
                <td class="p-2">
                  <input type="text" :name="'items[' + idx + '][isbn]'" x-model="item.isbn" placeholder="ISBN" class="input w-full text-xs">
                </td>
                <td class="p-2">
                  <input type="text" :name="'items[' + idx + '][author]'" x-model="item.author" placeholder="Author" class="input w-full text-xs">
                </td>
                <td class="p-2">
                  <input type="number" :name="'items[' + idx + '][quantity_ordered]'" x-model="item.quantity_ordered" min="1" class="input w-full text-xs" required>
                </td>
                <td class="p-2">
                  <input type="number" step="0.01" :name="'items[' + idx + '][unit_price]'" x-model="item.unit_price" min="0" class="input w-full text-xs" required>
                </td>
                <td class="p-2">
                  <input type="number" step="0.01" :name="'items[' + idx + '][discount_percent]'" x-model="item.discount_percent" min="0" max="100" class="input w-full text-xs">
                </td>
                <td class="p-2 text-right font-bold text-slate-800">
                  ₹<span x-text="calcSubtotal(item).toFixed(2)"></span>
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

    {{-- Order Summary --}}
    <div class="flex justify-end pt-3">
      <div class="w-64 space-y-2 text-sm">
        <div class="flex justify-between text-slate-600">
          <span>Estimated Total:</span>
          <strong class="font-bold text-slate-900 text-lg">₹<span x-text="grandTotal.toFixed(2)"></span></strong>
        </div>
      </div>
    </div>

    <div>
      <label class="block text-xs font-semibold text-slate-700 mb-1">Procurement Notes / Terms</label>
      <input type="text" name="notes" placeholder="Optional delivery instructions or payment terms" class="input w-full text-sm">
    </div>

    <div class="pt-2">
      <button type="submit" class="btn btn-primary w-full py-2.5 font-semibold text-sm">
        Submit Purchase Order for Approval
      </button>
    </div>
  </form>

</div>
@endsection

