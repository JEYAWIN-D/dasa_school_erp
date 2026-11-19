<?php

namespace App\Models;

use App\Models\Library\BookCopy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookReservation extends Model
{
    protected $fillable = [
        'book_id',
        'copy_id',
        'student_id',
        'employee_id',
        'queue_position',
        'status', // waiting, ready, cancelled, fulfilled, expired
        'reserved_at',
        'expires_at',
        'notification_sent_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'reserved_at'          => 'datetime',
        'expires_at'           => 'datetime',
        'notification_sent_at' => 'datetime',
        'queue_position'       => 'integer',
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

    public function borrowerName(): string
    {
        if ($this->employee_id && $this->employee) {
            return $this->employee->first_name . ' ' . $this->employee->last_name;
        }
        return $this->student?->full_name ?? 'Unknown';
    }
}
