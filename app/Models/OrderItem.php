<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Phase 4 – OrderItem model
 *
 * WHY: One row per product in an order (line item). belongsTo(Order) and
 * belongsTo(Product) let us show $item->product->title and $item->order
 * without extra queries when we eager load. The table is order_items
 * (Laravel's snake_case plural for OrderItem). We don't use HasFactory
 * because we typically create these via OrderService, not factories.
 */
class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price_at_purchase',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'price_at_purchase' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
