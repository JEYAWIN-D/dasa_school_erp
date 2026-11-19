<?php

namespace App\Models\Library;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LibraryPeriodicalReceipt extends Model
{
    protected $table = 'library_periodical_receipts';

    protected $fillable = [
        'periodical_id',
        'subscription_id',
        'issue_date',
        'volume_no',
        'issue_no',
        'received_date',
        'copies_received',
        'received_by',
        'condition',
        'remarks',
    ];

    protected $casts = [
        'issue_date'      => 'date',
        'received_date'   => 'date',
        'copies_received' => 'integer',
    ];

    public function periodical(): BelongsTo
    {
        return $this->belongsTo(LibraryPeriodical::class, 'periodical_id');
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(LibrarySubscription::class, 'subscription_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}

