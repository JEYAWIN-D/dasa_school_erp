<?php

namespace App\Models\Library;

use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibraryStockVerificationSession extends Model
{
    protected $table = 'library_stock_verification_sessions';

    protected $fillable = [
        'session_code',
        'title',
        'academic_year_id',
        'start_date',
        'end_date',
        'status',
        'total_expected_copies',
        'total_scanned_copies',
        'matched_count',
        'missing_count',
        'damaged_count',
        'misplaced_count',
        'initiated_by',
        'completed_by',
        'summary_notes',
    ];

    protected $casts = [
        'start_date'            => 'date',
        'end_date'              => 'date',
        'total_expected_copies' => 'integer',
        'total_scanned_copies'  => 'integer',
        'matched_count'         => 'integer',
        'missing_count'         => 'integer',
        'damaged_count'         => 'integer',
        'misplaced_count'       => 'integer',
    ];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function initiator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    public function completer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(LibraryStockVerificationItem::class, 'session_id');
    }
}

