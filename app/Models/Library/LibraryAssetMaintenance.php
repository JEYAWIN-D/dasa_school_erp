<?php

namespace App\Models\Library;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LibraryAssetMaintenance extends Model
{
    protected $table = 'library_asset_maintenance';

    protected $fillable = [
        'asset_id',
        'maintenance_date',
        'service_provider',
        'cost',
        'issue_description',
        'action_taken',
        'next_scheduled_date',
        'status',
        'conducted_by',
    ];

    protected $casts = [
        'maintenance_date'    => 'date',
        'next_scheduled_date' => 'date',
        'cost'                => 'decimal:2',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(LibraryAsset::class, 'asset_id');
    }

    public function conductor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'conducted_by');
    }
}

