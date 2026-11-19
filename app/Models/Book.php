<?php

namespace App\Models;

use App\Models\Library\BookCopy;
use App\Models\Library\LibraryRack;
use App\Models\Library\LibraryShelf;
use App\Models\Library\LibrarySupplier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    protected $fillable = [
        'accession_number',
        'title',
        'subtitle',
        'author',
        'co_author',
        'publisher',
        'isbn',
        'edition',
        'publish_year',
        'category_id',
        'category',
        'subject',
        'subject_id',
        'class_suitability',
        'language',
        'pages',
        'total_copies',
        'available_copies',
        'lost_copies',
        'purchase_price',
        'current_value',
        'purchase_date',
        'supplier_id',
        'bill_number',
        'invoice_reference',
        'location',
        'rack_id',
        'shelf_id',
        'description',
        'cover_image',
        'is_available',
        'status',
        'is_active',
        'is_deaccessioned',
        'deaccession_date',
        'deaccession_reason',
        'deaccession_by',
    ];

    protected $casts = [
        'purchase_date'    => 'date',
        'is_available'     => 'boolean',
        'is_active'        => 'boolean',
        'is_deaccessioned' => 'boolean',
        'deaccession_date' => 'date',
        'purchase_price'   => 'decimal:2',
        'current_value'    => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Book $book) {
            if (empty($book->accession_number)) {
                $book->accession_number = 'ACC-' . time() . '-' . mt_rand(100, 999);
            }
        });
    }

    public function copies(): HasMany
    {
        return $this->hasMany(BookCopy::class, 'book_id');
    }

    public function availableCopiesList(): HasMany
    {
        return $this->hasMany(BookCopy::class, 'book_id')->where('status', 'available');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BookCategory::class, 'category_id');
    }

    public function rack(): BelongsTo
    {
        return $this->belongsTo(LibraryRack::class, 'rack_id');
    }

    public function shelf(): BelongsTo
    {
        return $this->belongsTo(LibraryShelf::class, 'shelf_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(LibrarySupplier::class, 'supplier_id');
    }

    public function issues(): HasMany
    {
        return $this->hasMany(BookIssue::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(BookReservation::class);
    }

    public function subjectRelation(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function refreshCopiesCount(): void
    {
        $total = $this->copies()->count();
        $avail = $this->copies()->where('status', 'available')->count();
        $this->update([
            'total_copies'     => $total,
            'available_copies' => $avail,
            'is_available'     => $avail > 0,
        ]);
    }
}
