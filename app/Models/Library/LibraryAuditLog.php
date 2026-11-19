<?php

namespace App\Models\Library;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LibraryAuditLog extends Model
{
    protected $table = 'library_audit_logs';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'ip_address',
        'user_agent',
        'payload_before',
        'payload_after',
        'remarks',
        'created_at',
    ];

    protected $casts = [
        'payload_before' => 'array',
        'payload_after'  => 'array',
        'created_at'     => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function log(string $action, string $entityType, ?int $entityId, ?array $before = null, ?array $after = null, ?string $remarks = null): self
    {
        return static::create([
            'user_id'        => auth()->id(),
            'action'         => $action,
            'entity_type'    => $entityType,
            'entity_id'      => $entityId,
            'ip_address'     => request()->ip(),
            'user_agent'     => request()->userAgent(),
            'payload_before' => $before,
            'payload_after'  => $after,
            'remarks'        => $remarks,
            'created_at'     => now(),
        ]);
    }
}

