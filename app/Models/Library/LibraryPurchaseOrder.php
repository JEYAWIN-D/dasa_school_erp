<?php

namespace App\Models\Library;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibraryPurchaseOrder extends Model
{
    protected $table = 'library_purchase_orders';

    protected $fillable = [
        'po_number',
        'supplier_id',
        'order_date',
        'expected_delivery_date',
        'status',
        'total_amount',
        'tax_amount',
        'grand_total',
        'created_by',
        'approved_by',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'order_date'             => 'date',
        'expected_delivery_date' => 'date',
        'approved_at'            => 'datetime',
        'total_amount'           => 'decimal:2',
        'tax_amount'             => 'decimal:2',
        'grand_total'            => 'decimal:2',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(LibrarySupplier::class, 'supplier_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(LibraryPurchaseOrderItem::class, 'purchase_order_id');
    }

    public function goodsReceipts(): HasMany
    {
        return $this->hasMany(LibraryGoodsReceipt::class, 'purchase_order_id');
    }
}

