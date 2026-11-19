<?php

namespace App\Models;

use App\Models\Library\BookCopy;
use App\Models\Library\LibraryFinePayment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookIssue extends Model
{
    protected $fillable = [
        'book_id',
        'copy_id',
        'student_id',
        'employee_id',
        'issued_by',
        'issue_date',
        'due_date',
        'return_date',
        'status',
        'fine_amount',
        'damage_fine',
        'lost_fine',
        'fine_paid',
        'renew_count',
        'fine_waived',
        'waiver_amount',
        'waiver_reason',
        'waived_by',
        'waived_at',
        'fine_collected_at',
        'fine_collection_mode',
        'remarks',
        'notes',
        'returned_to',
        'issue_receipt_no',
        'return_receipt_no',
        'return_condition',
        'override_by',
        'override_reason',
        'inspection_notes',
    ];

    protected $casts = [
        'issue_date'    => 'date',
        'due_date'      => 'date',
        'return_date'   => 'date',
        'waived_at'     => 'datetime',
        'fine_waived'   => 'boolean',
        'fine_paid'     => 'boolean',
        'fine_amount'   => 'decimal:2',
        'damage_fine'   => 'decimal:2',
        'lost_fine'     => 'decimal:2',
        'waiver_amount' => 'decimal:2',
        'renew_count'   => 'integer',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function copy(): BelongsTo
    {
        return $this->belongsTo(BookCopy::class, 'copy_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function finePayments(): HasMany
    {
        return $this->hasMany(LibraryFinePayment::class, 'book_issue_id');
    }

    public function borrowerName(): string
    {
        if ($this->employee_id && $this->employee) {
            return $this->employee->first_name . ' ' . $this->employee->last_name;
        }
        return $this->student?->full_name ?? 'Unknown';
    }

    public function borrowerIdentifier(): string
    {
        if ($this->employee_id && $this->employee) {
            return $this->employee->employee_id ?? ('EMP-' . $this->employee_id);
        }
        return $this->student?->admission_number ?? ('ADM-' . ($this->student_id ?? 'N/A'));
    }

    public function getDaysOverdueAttribute(): int
    {
        if ($this->status !== 'issued' || !$this->due_date) return 0;
        return max(0, now()->startOfDay()->diffInDays($this->due_date->startOfDay(), false) * -1);
    }

    public function totalFineCalculated(): float
    {
        return (float) ($this->fine_amount > 0 ? $this->fine_amount : ($this->damage_fine + $this->lost_fine));
    }

    public function totalFinePaid(): float
    {
        return (float) $this->finePayments()->sum('amount_paid');
    }

    public function totalPendingFine(): float
    {
        if ($this->fine_waived) return 0.0;
        $total = $this->totalFineCalculated();
        $paid = $this->totalFinePaid();
        $waived = (float) ($this->waiver_amount ?? 0);
        return max(0.0, round($total - $paid - $waived, 2));
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function returnedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_to');
    }

    public function waivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'waived_by');
    }

    public function overrideBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'override_by');
    }
}
