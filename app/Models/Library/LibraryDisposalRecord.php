<?php

namespace App\Models\Library;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LibraryDisposalRecord extends Model
{
    protected $table = 'library_disposal_records';

    protected $fillable = [
        'disposal_number',
        'damage_report_id',
        'book_copy_id',
        'asset_id',
        'disposal_method',
        'approved_by',
        'disposal_date',
        'original_value',
        'scrap_value_recovered',
        'revenue_receipt_no',
        'justification',
        'witness_name',
        'certificate_file_path',
    ];

    protected $casts = [
        'disposal_date'         => 'date',
        'original_value'        => 'decimal:2',
        'scrap_value_recovered' => 'decimal:2',
    ];

    public function damageReport(): BelongsTo
    {
        return $this->belongsTo(LibraryDamageReport::class, 'damage_report_id');
    }

    public function bookCopy(): BelongsTo
    {
        return $this->belongsTo(BookCopy::class, 'book_copy_id');
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(LibraryAsset::class, 'asset_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}

