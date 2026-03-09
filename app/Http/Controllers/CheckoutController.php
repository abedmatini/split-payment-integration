<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use App\Services\PayFastService;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Phase 8/9 – Checkout controller
 *
 * WHY: show() displays the checkout form; store() creates the order and
 * redirects to the payment step. paymentRedirect() shows the "pay now" page
 * and, when PayFast is configured, builds form data so the view can POST
 * the user to PayFast. We authorize that the order belongs to the current user.
 */
class CheckoutController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private PayFastService $payFastService
    ) {}

    /**
     * Show checkout form: cart summary, rewards balance, optional "Use my rewards" and amounts.
     */
    public function show(): View|RedirectResponse
    {
        $carts = Cart::where('user_id', Auth::id())->with('product')->get();

        if ($carts->isEmpty()) {
            return redirect()->route('cart.index')->with('status', __('Your cart is empty. Add items before checkout.'));
        }

        $total = $carts->sum(fn (Cart $cart) => $cart->quantity * (float) $cart->product->price);
        $rewardsBalance = (float) Auth::user()->rewards_balance;

        return view('checkout.show', compact('carts', 'total', 'rewardsBalance'));
    }

    /**
     * Create order from cart and redirect to payment step.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'use_rewards' => 'sometimes|boolean',
        ]);

        $useRewards = (bool) ($validated['use_rewards'] ?? false);

        try {
            $order = $this->orderService->createOrderFromCart(Auth::user(), $useRewards);
        } catch (\InvalidArgumentException $e) {
            return redirect()->route('cart.index')->with('status', $e->getMessage());
        }

        return redirect()->route('checkout.payment', $order);
    }

    /**
     * Show "pay now" page for the order. When PayFast is configured, pass form
     * data so the view can auto-submit to PayFast; otherwise show a message.
     */
    public function paymentRedirect(Order $order): View|RedirectResponse
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status !== 'pending') {
            return redirect()->route('dashboard')->with('status', __('This order has already been processed.'));
        }

        $paymentFormData = null;
        if ($this->payFastConfigured()) {
            $paymentFormData = $this->payFastService->getPaymentFormData($order, Auth::user());
        }

        return view('checkout.payment', compact('order', 'paymentFormData'));
    }

    private function payFastConfigured(): bool
    {
        $id = config('services.payfast.merchant_id');
        $key = config('services.payfast.merchant_key');

        return $id && $key;
    }
}
