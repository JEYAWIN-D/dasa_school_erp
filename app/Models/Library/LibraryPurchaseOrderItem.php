<?php

namespace App\Models\Library;

use App\Models\BookCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibraryPurchaseOrderItem extends Model
{
    protected $table = 'library_purchase_order_items';

    protected $fillable = [
        'purchase_order_id',
        'book_title',
        'isbn',
        'author',
        'publisher',
        'edition',
        'category_id',
        'quantity_ordered',
        'quantity_received',
        'unit_price',
        'discount_percent',
        'total_price',
    ];

    protected $casts = [
        'unit_price'        => 'decimal:2',
        'discount_percent'  => 'decimal:2',
        'total_price'       => 'decimal:2',
        'quantity_ordered'  => 'integer',
        'quantity_received' => 'integer',
    ];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(LibraryPurchaseOrder::class, 'purchase_order_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BookCategory::class, 'category_id');
    }

    public function copies(): HasMany
    {
        return $this->hasMany(BookCopy::class, 'purchase_order_item_id');
    }
}

