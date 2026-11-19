<?php

namespace App\Models;

use App\Models\Library\LibraryRack;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookCategory extends Model
{
    protected $table = 'book_categories';

    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    public function books(): HasMany
    {
        return $this->hasMany(Book::class, 'category_id');
    }

    public function racks(): HasMany
    {
        return $this->hasMany(LibraryRack::class, 'category_id');
    }
}

