<?php

namespace App\Models\Library;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LibraryStockVerificationItem extends Model
{
    protected $table = 'library_stock_verification_items';

    protected $fillable = [
        'session_id',
        'book_copy_id',
        'scanned_shelf_id',
        'expected_shelf_id',
        'verification_status',
        'scanned_at',
        'scanned_by',
        'condition_at_scan',
        'notes',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(LibraryStockVerificationSession::class, 'session_id');
    }

    public function copy(): BelongsTo
    {
        return $this->belongsTo(BookCopy::class, 'book_copy_id');
    }

    public function scannedShelf(): BelongsTo
    {
        return $this->belongsTo(LibraryShelf::class, 'scanned_shelf_id');
    }

    public function expectedShelf(): BelongsTo
    {
        return $this->belongsTo(LibraryShelf::class, 'expected_shelf_id');
    }

    public function scanner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }
}

