<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Handles the user's return from PayFast after payment.
 *
 * PayFast redirects the user to return_url (we pass ?order_id=X so we know the order).
 * Status is set automatically when PayFast sends the ITN to our notify_url (webhook).
 * This handler just redirects to the order page with a message.
 */
class PaymentReturnController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $orderId = $request->query('order_id');

        if (! $orderId) {
            return redirect()->route('dashboard')->with('status', __('Thank you. Payment confirmation will be processed shortly.'));
        }

        $order = Order::find($orderId);

        if (! $order || $order->user_id !== Auth::id()) {
            return redirect()->route('orders.index')->with('status', __('Order not found.'));
        }

        $message = $order->status === 'paid'
            ? __('Payment confirmed. Your order has been marked as paid.')
            : __('Thank you. Payment status is updated automatically when PayFast confirms; if it still shows Pending, use "Mark as paid" after confirming in PayFast (e.g. when testing locally).');

        return redirect()->route('orders.show', $order)->with('status', $message);
    }
}
