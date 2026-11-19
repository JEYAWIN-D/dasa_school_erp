<?php

namespace App\Services\Library;

use App\Models\Book;
use App\Models\Library\BookCopy;
use App\Models\Library\LibraryAuditLog;
use App\Models\Library\LibraryGoodsReceipt;
use App\Models\Library\LibraryGoodsReceiptItem;
use App\Models\Library\LibraryPurchaseOrder;
use App\Models\Library\LibraryPurchaseOrderItem;
use App\Models\Library\LibrarySupplier;
use Exception;
use Illuminate\Support\Facades\DB;

class ProcurementService
{
    public function __construct(protected BookCopyService $copyService) {}

    /**
     * Create purchase order.
     */
    public function createPurchaseOrder(array $header, array $items): LibraryPurchaseOrder
    {
        return DB::transaction(function () use ($header, $items) {
            $poNumber = 'PO-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            $total = 0.0;
            foreach ($items as $item) {
                $qty = (int) $item['quantity_ordered'];
                $price = (float) $item['unit_price'];
                $disc = (float) ($item['discount_percent'] ?? 0);
                $subtotal = ($qty * $price) * (1 - ($disc / 100));
                $total += $subtotal;
            }

            $taxRate = (float) ($header['tax_percent'] ?? 0);
            $taxAmount = round($total * ($taxRate / 100), 2);
            $grandTotal = round($total + $taxAmount, 2);

            $po = LibraryPurchaseOrder::create([
                'po_number'              => $poNumber,
                'supplier_id'            => $header['supplier_id'],
                'order_date'             => $header['order_date'] ?? now()->toDateString(),
                'expected_delivery_date' => $header['expected_delivery_date'] ?? null,
                'status'                 => 'draft',
                'total_amount'           => $total,
                'tax_amount'             => $taxAmount,
                'grand_total'            => $grandTotal,
                'created_by'             => auth()->id(),
                'notes'                  => $header['notes'] ?? null,
            ]);

            foreach ($items as $item) {
                $qty = (int) $item['quantity_ordered'];
                $price = (float) $item['unit_price'];
                $disc = (float) ($item['discount_percent'] ?? 0);
                $itemTotal = round(($qty * $price) * (1 - ($disc / 100)), 2);

                $po->items()->create([
                    'book_title'        => $item['book_title'],
                    'isbn'              => $item['isbn'] ?? null,
                    'author'            => $item['author'] ?? null,
                    'publisher'         => $item['publisher'] ?? null,
                    'edition'           => $item['edition'] ?? null,
                    'category_id'       => $item['category_id'] ?? null,
                    'quantity_ordered'  => $qty,
                    'quantity_received' => 0,
                    'unit_price'        => $price,
                    'discount_percent'  => $disc,
                    'total_price'       => $itemTotal,
                ]);
            }

            LibraryAuditLog::log('PURCHASE_ORDER_CREATED', 'LibraryPurchaseOrder', $po->id, null, [
                'po_number'   => $poNumber,
                'grand_total' => $grandTotal,
                'items_count' => count($items),
            ], "Created Purchase Order {$poNumber} totaling ₹{$grandTotal}");

            return $po;
        });
    }

    /**
     * Approve purchase order.
     */
    public function approvePurchaseOrder(int $poId, int $approverId): LibraryPurchaseOrder
    {
        return DB::transaction(function () use ($poId, $approverId) {
            $po = LibraryPurchaseOrder::lockForUpdate()->findOrFail($poId);

            if ($po->status !== 'draft' && $po->status !== 'submitted') {
                throw new Exception("Cannot approve PO with status '{$po->status}'.");
            }

            $po->update([
                'status'      => 'approved',
                'approved_by' => $approverId,
                'approved_at' => now(),
            ]);

            LibraryAuditLog::log('PURCHASE_ORDER_APPROVED', 'LibraryPurchaseOrder', $po->id, null, [
                'approved_by' => $approverId,
            ], "Approved Purchase Order {$po->po_number}");

            return $po;
        });
    }

    /**
     * Process Goods Receipt Note (GRN) and inject physical book copies into inventory.
     */
    public function createGoodsReceipt(array $header, array $receivedItems): LibraryGoodsReceipt
    {
        return DB::transaction(function () use ($header, $receivedItems) {
            $grnNumber = 'GRN-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            $totalItems = 0;
            $totalAmount = 0.0;

            foreach ($receivedItems as $item) {
                $qty = (int) $item['quantity_received'];
                $cost = (float) $item['unit_cost'];
                $totalItems += $qty;
                $totalAmount += ($qty * $cost);
            }

            $grn = LibraryGoodsReceipt::create([
                'grn_number'           => $grnNumber,
                'purchase_order_id'    => $header['purchase_order_id'] ?? null,
                'supplier_id'          => $header['supplier_id'],
                'invoice_number'       => $header['invoice_number'],
                'invoice_date'         => $header['invoice_date'],
                'receipt_date'         => $header['receipt_date'] ?? now()->toDateString(),
                'received_by'          => auth()->id(),
                'verified_by'          => $header['verified_by'] ?? auth()->id(),
                'total_items_received' => $totalItems,
                'total_amount'         => round($totalAmount, 2),
                'remarks'              => $header['remarks'] ?? null,
            ]);

            foreach ($receivedItems as $item) {
                $qty = (int) $item['quantity_received'];
                $cost = (float) $item['unit_cost'];
                $poItemId = $item['purchase_order_item_id'] ?? null;

                // Find or create Book Title Master
                $book = null;
                if (!empty($item['book_id'])) {
                    $book = Book::find($item['book_id']);
                } elseif (!empty($item['isbn'])) {
                    $book = Book::where('isbn', trim($item['isbn']))->first();
                }

                if (!$book) {
                    $accPrefix = 'ACC-' . date('Y') . strtoupper(substr(uniqid(), -4));
                    $book = Book::create([
                        'title'             => $item['book_title'],
                        'accession_number'  => $accPrefix,
                        'author'            => $item['author'] ?? null,
                        'publisher'         => $item['publisher'] ?? null,
                        'isbn'              => $item['isbn'] ?? null,
                        'category_id'       => $item['category_id'] ?? null,
                        'purchase_price'    => $cost,
                        'current_value'     => $cost,
                        'purchase_date'     => $grn->receipt_date,
                        'supplier_id'       => $grn->supplier_id,
                        'bill_number'       => $grn->invoice_number,
                        'invoice_reference' => $grn->invoice_number,
                        'is_available'      => true,
                        'status'            => 'active',
                    ]);
                }

                // Generate Physical Copies
                $copies = $this->copyService->createCopies($book, $qty, [
                    'cost_price'             => $cost,
                    'shelf_id'               => $item['shelf_id'] ?? null,
                    'condition'              => 'new',
                    'status'                 => 'available',
                    'purchase_order_item_id' => $poItemId,
                ]);

                $firstAcc = $copies[0]->accession_number ?? null;
                $lastAcc  = end($copies)->accession_number ?? null;

                $grn->items()->create([
                    'purchase_order_item_id' => $poItemId,
                    'book_id'                => $book->id,
                    'quantity_received'      => $qty,
                    'unit_cost'              => $cost,
                    'accession_number_start' => $firstAcc,
                    'accession_number_end'   => $lastAcc,
                    'condition_note'         => $item['condition_note'] ?? 'Brand new',
                ]);

                // Update PO Item received count if linked
                if ($poItemId) {
                    $poItem = LibraryPurchaseOrderItem::find($poItemId);
                    if ($poItem) {
                        $poItem->increment('quantity_received', $qty);
                    }
                }
            }

            // Update PO Status if all items received
            if (!empty($header['purchase_order_id'])) {
                $po = LibraryPurchaseOrder::with('items')->find($header['purchase_order_id']);
                if ($po) {
                    $allComplete = $po->items->every(fn($i) => $i->quantity_received >= $i->quantity_ordered);
                    $po->update(['status' => $allComplete ? 'completed' : 'partially_received']);
                }
            }

            LibraryAuditLog::log('GOODS_RECEIPT_CREATED', 'LibraryGoodsReceipt', $grn->id, null, [
                'grn_number'   => $grnNumber,
                'total_copies' => $totalItems,
                'total_amount' => $totalAmount,
            ], "Received {$totalItems} physical book copies under GRN {$grnNumber}");

            return $grn;
        });
    }
}

