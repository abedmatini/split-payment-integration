<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Phase 12 – Order history and status
 *
 * WHY: index() lists the current user's orders (scoped by auth). show() displays
 * a single order with items (and authorizes that the order belongs to the user).
 * complete() allows marking a paid order as completed (manual fulfilment step).
 */
class OrderController extends Controller
{
    /**
     * List the current user's orders (newest first).
     */
    public function index(): View
    {
        $orders = Auth::user()
            ->orders()
            ->withCount('items')
            ->latest()
            ->get();

        return view('orders.index', compact('orders'));
    }

    /**
     * Show a single order with its items. Only the order owner can view.
     */
    public function show(Order $order): View|RedirectResponse
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load('items.product');

        return view('orders.show', compact('order'));
    }

    /**
     * Mark a paid order as completed (manual step after fulfilment).
     * Only the order owner; only when status is 'paid'.
     */
    public function complete(Order $order): RedirectResponse
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status !== 'paid') {
            return redirect()
                ->route('orders.show', $order)
                ->with('status', __('Only paid orders can be marked as completed.'));
        }

        $order->update(['status' => 'completed']);

        return redirect()
            ->route('orders.show', $order)
            ->with('status', __('Order marked as completed.'));
    }

    /**
     * Mark a pending order as paid (e.g. when payment succeeded but webhook did not run).
     * Only the order owner; only when status is 'pending'.
     */
    public function markPaid(Order $order): RedirectResponse
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status !== 'pending') {
            return redirect()
                ->route('orders.show', $order)
                ->with('status', __('Only pending orders can be marked as paid.'));
        }

        $order->update(['status' => 'paid']);

        // Deduct rewards if any (same as webhook would do)
        $rewardsUsed = (float) $order->rewards_used;
        if ($rewardsUsed > 0) {
            $user = $order->user;
            $user->rewards_balance = max(0, (float) $user->rewards_balance - $rewardsUsed);
            $user->save();
        }

        return redirect()
            ->route('orders.show', $order)
            ->with('status', __('Order marked as paid.'));
    }
}
