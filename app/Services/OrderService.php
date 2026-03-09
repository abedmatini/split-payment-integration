<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Phase 8 – Order service
 *
 * WHY: Business logic for creating an order from the cart lives here so the
 * controller stays thin and the logic is reusable and testable. We compute
 * total from cart rows, then rewards_used (capped by user balance and total)
 * and card_amount. DB::transaction ensures order + items are created and
 * cart cleared atomically; on exception everything rolls back. We do not
 * deduct rewards here – the webhook does that after payment is confirmed.
 */
class OrderService
{
    /**
     * Create an order from the user's cart and clear the cart.
     * Rewards are applied only if $useRewards is true; amount is capped by balance and total.
     *
     * @return Order The created order (status: pending).
     */
    public function createOrderFromCart(User $user, bool $useRewards): Order
    {
        $carts = Cart::where('user_id', $user->id)->with('product')->get();

        if ($carts->isEmpty()) {
            throw new \InvalidArgumentException('Cart is empty.');
        }

        $total = $carts->sum(fn (Cart $cart) => $cart->quantity * (float) $cart->product->price);
        $balance = (float) $user->rewards_balance;
        $rewardsUsed = $useRewards ? min($balance, $total) : 0.0;
        $cardAmount = $total - $rewardsUsed;

        return DB::transaction(function () use ($user, $carts, $total, $rewardsUsed, $cardAmount) {
            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $total,
                'rewards_used' => $rewardsUsed,
                'card_amount' => $cardAmount,
                'status' => 'pending',
            ]);

            foreach ($carts as $cart) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cart->product_id,
                    'quantity' => $cart->quantity,
                    'price_at_purchase' => $cart->product->price,
                ]);
            }

            Cart::where('user_id', $user->id)->delete();

            return $order;
        });
    }
}
