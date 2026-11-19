<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibraryAsset extends Model
{
    protected $table = 'library_assets';

    protected $fillable = [
        'asset_code',
        'name',
        'category',
        'brand_model',
        'serial_number',
        'purchase_date',
        'purchase_cost',
        'current_value',
        'location_room',
        'status',
        'warranty_until',
        'supplier_id',
        'remarks',
    ];

    protected $casts = [
        'purchase_date'  => 'date',
        'warranty_until' => 'date',
        'purchase_cost'  => 'decimal:2',
        'current_value'  => 'decimal:2',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(LibrarySupplier::class, 'supplier_id');
    }

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(LibraryAssetMaintenance::class, 'asset_id');
    }

    public function damageReports(): HasMany
    {
        return $this->hasMany(LibraryDamageReport::class, 'asset_id');
    }
}

