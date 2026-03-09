<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Phase 4 – Order model
 *
 * WHY: An order is the record of a purchase. belongsTo(User) lets us
 * scope queries (e.g. auth()->user()->orders) and access $order->user.
 * hasMany(OrderItem) gives us $order->items so we can display line items
 * and loop in Blade. We cast money columns to decimal for consistency.
 * status is kept as string for simplicity (could be enum in PHP 8.1+).
 */
class Order extends Model
{
    protected $fillable = [
        'user_id',
        'total_amount',
        'rewards_used',
        'card_amount',
        'status',
        'payfast_reference',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'rewards_used' => 'decimal:2',
            'card_amount' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Order items (line items). Name "items" so we can use $order->items in views.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
