<?php

namespace App\Models\Library;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibraryGoodsReceipt extends Model
{
    protected $table = 'library_goods_receipts';

    protected $fillable = [
        'grn_number',
        'purchase_order_id',
        'supplier_id',
        'invoice_number',
        'invoice_date',
        'receipt_date',
        'received_by',
        'verified_by',
        'total_items_received',
        'total_amount',
        'remarks',
    ];

    protected $casts = [
        'invoice_date'         => 'date',
        'receipt_date'         => 'date',
        'total_items_received' => 'integer',
        'total_amount'         => 'decimal:2',
    ];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(LibraryPurchaseOrder::class, 'purchase_order_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(LibrarySupplier::class, 'supplier_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(LibraryGoodsReceiptItem::class, 'goods_receipt_id');
    }
}

