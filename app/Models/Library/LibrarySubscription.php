<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibrarySubscription extends Model
{
    protected $table = 'library_subscriptions';

    protected $fillable = [
        'periodical_id',
        'supplier_id',
        'subscription_number',
        'start_date',
        'end_date',
        'cost',
        'copies_per_issue',
        'auto_renew',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date'       => 'date',
        'end_date'         => 'date',
        'cost'             => 'decimal:2',
        'copies_per_issue' => 'integer',
        'auto_renew'       => 'boolean',
    ];

    public function periodical(): BelongsTo
    {
        return $this->belongsTo(LibraryPeriodical::class, 'periodical_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(LibrarySupplier::class, 'supplier_id');
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(LibraryPeriodicalReceipt::class, 'subscription_id');
    }

    public function isExpired(): bool
    {
        return $this->end_date && $this->end_date->isPast();
    }
}

