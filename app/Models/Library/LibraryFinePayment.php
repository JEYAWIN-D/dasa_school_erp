<?php

namespace App\Models\Library;

use App\Models\BookIssue;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LibraryFinePayment extends Model
{
    protected $table = 'library_fine_payments';

    protected $fillable = [
        'book_issue_id',
        'receipt_no',
        'amount_paid',
        'waiver_amount',
        'payment_mode',
        'transaction_reference',
        'waiver_reason',
        'approved_by',
        'received_by',
        'notes',
    ];

    protected $casts = [
        'amount_paid'   => 'decimal:2',
        'waiver_amount' => 'decimal:2',
    ];

    public function issue(): BelongsTo
    {
        return $this->belongsTo(BookIssue::class, 'book_issue_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}

