<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibraryShelf extends Model
{
    protected $table = 'library_shelves';

    protected $fillable = [
        'rack_id',
        'shelf_number',
        'name',
        'subject',
        'capacity',
        'is_active',
        'remarks',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacity'  => 'integer',
    ];

    public function rack(): BelongsTo
    {
        return $this->belongsTo(LibraryRack::class, 'rack_id');
    }

    public function copies(): HasMany
    {
        return $this->hasMany(BookCopy::class, 'shelf_id');
    }

    public function currentCount(): int
    {
        return $this->copies()->whereIn('status', ['available', 'maintenance'])->count();
    }
}

