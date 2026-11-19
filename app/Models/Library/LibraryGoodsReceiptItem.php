<?php

namespace App\Models\Library;

use App\Models\Book;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LibraryGoodsReceiptItem extends Model
{
    protected $table = 'library_goods_receipt_items';

    protected $fillable = [
        'goods_receipt_id',
        'purchase_order_item_id',
        'book_id',
        'quantity_received',
        'unit_cost',
        'accession_number_start',
        'accession_number_end',
        'condition_note',
    ];

    protected $casts = [
        'unit_cost'         => 'decimal:2',
        'quantity_received' => 'integer',
    ];

    public function goodsReceipt(): BelongsTo
    {
        return $this->belongsTo(LibraryGoodsReceipt::class, 'goods_receipt_id');
    }

    public function purchaseOrderItem(): BelongsTo
    {
        return $this->belongsTo(LibraryPurchaseOrderItem::class, 'purchase_order_item_id');
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class, 'book_id');
    }
}

