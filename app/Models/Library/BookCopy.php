<?php

namespace App\Models\Library;

use App\Models\Book;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookCopy extends Model
{
    protected $table = 'book_copies';

    protected $fillable = [
        'book_id',
        'accession_number',
        'barcode',
        'rfid_tag',
        'copy_number',
        'shelf_id',
        'condition',
        'status',
        'is_reference_only',
        'purchase_order_item_id',
        'cost_price',
        'remarks',
        'last_inspected_at',
    ];

    protected $casts = [
        'is_reference_only' => 'boolean',
        'cost_price'        => 'decimal:2',
        'last_inspected_at' => 'datetime',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function shelf(): BelongsTo
    {
        return $this->belongsTo(LibraryShelf::class, 'shelf_id');
    }

    public function purchaseOrderItem(): BelongsTo
    {
        return $this->belongsTo(LibraryPurchaseOrderItem::class, 'purchase_order_item_id');
    }

    public function issues(): HasMany
    {
        return $this->hasMany(\App\Models\BookIssue::class, 'copy_id');
    }

    public function activeIssue()
    {
        return $this->hasOne(\App\Models\BookIssue::class, 'copy_id')->where('status', 'issued');
    }

    public function damageReports(): HasMany
    {
        return $this->hasMany(LibraryDamageReport::class, 'book_copy_id');
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available' && !$this->is_reference_only;
    }
}

