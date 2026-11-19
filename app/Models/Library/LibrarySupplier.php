<?php

namespace App\Models\Library;

use App\Models\Book;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibrarySupplier extends Model
{
    protected $table = 'library_suppliers';

    protected $fillable = [
        'name',
        'contact_person',
        'phone',
        'email',
        'gst_number',
        'address',
        'city',
        'state',
        'pincode',
        'bank_name',
        'bank_account_no',
        'ifsc_code',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(LibraryPurchaseOrder::class, 'supplier_id');
    }

    public function goodsReceipts(): HasMany
    {
        return $this->hasMany(LibraryGoodsReceipt::class, 'supplier_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(LibrarySubscription::class, 'supplier_id');
    }

    public function books(): HasMany
    {
        return $this->hasMany(Book::class, 'supplier_id');
    }
}

