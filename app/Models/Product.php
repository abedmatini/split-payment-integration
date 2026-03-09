<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Phase 4 – Product model
 *
 * WHY: Eloquent maps this class to the `products` table (Laravel convention:
 * lower-case plural of the class name). $fillable defines which attributes
 * can be mass-assigned (e.g. Product::create($data)) – a security measure
 * so request data can't set columns we don't intend. We don't need
 * $table here because "products" is the default for Product.
 */
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'price',
    ];

    /**
     * Cast price for consistent decimal handling in PHP.
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }
}
