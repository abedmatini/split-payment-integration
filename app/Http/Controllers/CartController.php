<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Phase 6/7 – Cart controller
 *
 * WHY: index() loads the current user's cart rows with product relation (eager
 * loading) to avoid N+1. update/destroy receive the Cart model via route
 * binding; we authorize by checking user_id so one user can't change another's
 * cart. store() adds or increments; update() sets quantity; destroy() removes
 * one line. All responses redirect back or to cart with a status message.
 */
class CartController extends Controller
{
    /**
     * Show the current user's cart (items with product details and totals).
     */
    public function index(): View
    {
        $carts = Cart::where('user_id', Auth::id())
            ->with('product')
            ->orderBy('updated_at', 'desc')
            ->get();

        $total = $carts->sum(fn (Cart $cart) => $cart->quantity * (float) $cart->product->price);

        return view('cart.index', compact('carts', 'total'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = Cart::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'product_id' => $validated['product_id'],
            ],
            ['quantity' => 0]
        );

        $cart->quantity += $validated['quantity'];
        $cart->save();

        return redirect()->route('products.index')->with('status', __('Added to cart.'));
    }

    /**
     * Update quantity for one cart line. Only the owner can update.
     */
    public function update(Request $request, Cart $cart): RedirectResponse
    {
        if ($cart->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart->quantity = $validated['quantity'];
        $cart->save();

        return redirect()->route('cart.index')->with('status', __('Cart updated.'));
    }

    /**
     * Remove one item from the cart. Only the owner can delete.
     */
    public function destroy(Cart $cart): RedirectResponse
    {
        if ($cart->user_id !== Auth::id()) {
            abort(403);
        }

        $cart->delete();

        return redirect()->route('cart.index')->with('status', __('Item removed from cart.'));
    }
}
