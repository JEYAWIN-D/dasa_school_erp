<?php

namespace App\Models\Library;

use App\Models\Employee;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LibraryDamageReport extends Model
{
    protected $table = 'library_damage_reports';

    protected $fillable = [
        'report_number',
        'book_copy_id',
        'asset_id',
        'incident_type',
        'reported_by',
        'borrower_student_id',
        'borrower_employee_id',
        'reported_date',
        'damage_description',
        'action_proposed',
        'repair_cost',
        'fine_charged',
        'status',
        'reviewed_by',
        'review_notes',
    ];

    protected $casts = [
        'reported_date' => 'date',
        'repair_cost'   => 'decimal:2',
        'fine_charged'  => 'decimal:2',
    ];

    public function bookCopy(): BelongsTo
    {
        return $this->belongsTo(BookCopy::class, 'book_copy_id');
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(LibraryAsset::class, 'asset_id');
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function borrowerStudent(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'borrower_student_id');
    }

    public function borrowerEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'borrower_employee_id');
    }

    public function disposalRecord(): HasOne
    {
        return $this->hasOne(LibraryDisposalRecord::class, 'damage_report_id');
    }
}

