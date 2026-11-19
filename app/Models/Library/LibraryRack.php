<?php

namespace App\Models\Library;

use App\Models\BookCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class LibraryRack extends Model
{
    protected $table = 'library_racks';

    protected $fillable = [
        'rack_number',
        'name',
        'building',
        'floor',
        'room',
        'section',
        'category_id',
        'capacity',
        'qr_code_path',
        'is_active',
        'remarks',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacity'  => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(BookCategory::class, 'category_id');
    }

    public function shelves(): HasMany
    {
        return $this->hasMany(LibraryShelf::class, 'rack_id');
    }

    public function copies(): HasManyThrough
    {
        return $this->hasManyThrough(BookCopy::class, LibraryShelf::class, 'rack_id', 'shelf_id');
    }

    public function currentOccupancy(): int
    {
        return $this->copies()->whereIn('status', ['available', 'maintenance'])->count();
    }
}

