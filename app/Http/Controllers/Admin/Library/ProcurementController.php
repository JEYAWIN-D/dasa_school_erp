<?php

namespace App\Http\Controllers\Admin\Library;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\Library\LibraryGoodsReceipt;
use App\Models\Library\LibraryPurchaseOrder;
use App\Models\Library\LibraryShelf;
use App\Models\Library\LibrarySupplier;
use App\Services\Library\ProcurementService;
use Illuminate\Http\Request;

class ProcurementController extends Controller
{
    public function __construct(protected ProcurementService $procurementService) {}

    public function index(Request $request)
    {
        $purchaseOrders = LibraryPurchaseOrder::with(['supplier', 'creator', 'approver', 'items'])
            ->latest('id')
            ->paginate(15);

        $goodsReceipts = LibraryGoodsReceipt::with(['supplier', 'receiver', 'items.book'])
            ->latest('id')
            ->paginate(15);

        $suppliers = LibrarySupplier::where('is_active', true)->orderBy('name')->get();

        return view('library.procurement.index', compact('purchaseOrders', 'goodsReceipts', 'suppliers'));
    }

    public function createPO()
    {
        $suppliers = LibrarySupplier::where('is_active', true)->orderBy('name')->get();
        $categories = BookCategory::orderBy('name')->get();

        return view('library.procurement.po-create', compact('suppliers', 'categories'));
    }

    public function storePO(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:library_suppliers,id',
            'order_date'  => 'required|date',
            'items'       => 'required|array|min:1',
            'items.*.book_title' => 'required|string',
            'items.*.quantity_ordered' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $po = $this->procurementService->createPurchaseOrder($request->all(), $request->input('items'));

        return redirect()->route('library.procurement.index')->with('success', "Purchase Order {$po->po_number} created successfully.");
    }

    public function approvePO(int $id)
    {
        $this->procurementService->approvePurchaseOrder($id, auth()->id());
        return redirect()->route('library.procurement.index')->with('success', "PO approved.");
    }

    public function grnCreate(Request $request)
    {
        $suppliers = LibrarySupplier::where('is_active', true)->orderBy('name')->get();
        $shelves = LibraryShelf::with('rack')->where('is_active', true)->get();
        $books = Book::where('status', 'active')->orderBy('title')->get();
        $selectedPo = $request->po_id ? LibraryPurchaseOrder::with('items')->find($request->po_id) : null;

        return view('library.procurement.grn-create', compact('suppliers', 'shelves', 'books', 'selectedPo'));
    }

    public function storeGRN(Request $request)
    {
        $request->validate([
            'supplier_id'    => 'required|exists:library_suppliers,id',
            'invoice_number' => 'required|string',
            'invoice_date'   => 'required|date',
            'receipt_date'   => 'required|date',
            'items'          => 'required|array|min:1',
            'items.*.book_title' => 'required|string',
            'items.*.quantity_received' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        $grn = $this->procurementService->createGoodsReceipt($request->all(), $request->input('items'));

        return redirect()->route('library.procurement.index')->with('success', "GRN {$grn->grn_number} created and inventory updated.");
    }

    public function storeSupplier(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:150',
            'contact_person' => 'nullable|string|max:100',
            'phone'          => 'nullable|string|max:30',
            'email'          => 'nullable|email|max:100',
            'gst_number'     => 'nullable|string|max:30',
            'address'        => 'nullable|string',
            'city'           => 'nullable|string|max:100',
            'state'          => 'nullable|string|max:100',
            'pincode'        => 'nullable|string|max:20',
        ]);

        LibrarySupplier::create($data);

        return redirect()->route('library.procurement.index')->with('success', "Supplier '{$data['name']}' added.");
    }
}

