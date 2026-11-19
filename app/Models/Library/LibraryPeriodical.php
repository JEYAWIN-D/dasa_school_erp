<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LibraryPeriodical extends Model
{
    protected $table = 'library_periodicals';

    protected $fillable = [
        'title',
        'periodical_type',
        'frequency',
        'issn',
        'publisher',
        'language',
        'rack_id',
        'shelf_id',
        'is_active',
        'remarks',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function rack(): BelongsTo
    {
        return $this->belongsTo(LibraryRack::class, 'rack_id');
    }

    public function shelf(): BelongsTo
    {
        return $this->belongsTo(LibraryShelf::class, 'shelf_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(LibrarySubscription::class, 'periodical_id');
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(LibrarySubscription::class, 'periodical_id')
            ->where('status', 'active')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->latest('id');
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(LibraryPeriodicalReceipt::class, 'periodical_id');
    }
}

