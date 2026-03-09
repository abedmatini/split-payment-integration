<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Phase 4 – Cart model
 *
 * WHY: Each row is one product in a user's cart. We use relationships so we
 * can write $cart->user, $cart->product instead of manual joins. belongsTo
 * assumes foreign key cart.user_id and cart.product_id (Laravel convention).
 * The inverse (User hasMany Cart) lets us do $user->carts with eager loading
 * to avoid N+1 queries when displaying the cart.
 */
class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
