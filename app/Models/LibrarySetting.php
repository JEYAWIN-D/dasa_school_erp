<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibrarySetting extends Model
{
    protected $fillable = [
        'member_type',
        'fine_per_day',
        'loan_days',
        'max_books',
        'max_renewals',
        'grace_period_days',
        'max_fine_limit',
        'lost_book_fine_type',
        'lost_book_multiplier',
        'allow_holiday_fine',
        'reservation_expiry_hours',
    ];

    protected $casts = [
        'fine_per_day'             => 'decimal:2',
        'max_fine_limit'           => 'decimal:2',
        'lost_book_multiplier'     => 'decimal:2',
        'loan_days'                => 'integer',
        'max_books'                => 'integer',
        'max_renewals'             => 'integer',
        'grace_period_days'        => 'integer',
        'reservation_expiry_hours' => 'integer',
        'allow_holiday_fine'       => 'boolean',
    ];

    public static function forType(string $type = 'student'): self
    {
        return static::firstOrCreate(['member_type' => $type], [
            'fine_per_day'             => 1.00,
            'loan_days'                => $type === 'employee' ? 30 : 14,
            'max_books'                => $type === 'employee' ? 5 : 2,
            'max_renewals'             => $type === 'employee' ? 2 : 1,
            'grace_period_days'        => 0,
            'max_fine_limit'           => 500.00,
            'lost_book_fine_type'      => 'fixed_multiplier',
            'lost_book_multiplier'     => 1.50,
            'allow_holiday_fine'       => false,
            'reservation_expiry_hours' => 48,
        ]);
    }
}
