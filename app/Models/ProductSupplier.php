<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductSupplier extends Model
{
    protected $fillable = [
        'name',
        'code',
        'logo',
        'contact_person',
        'phone',
        'mobile',
        'email',
        'line_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'supplier_id');
    }
} 