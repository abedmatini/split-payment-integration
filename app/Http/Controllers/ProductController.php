<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

/**
 * Phase 6 – Product controller
 *
 * WHY: Controllers handle HTTP and return responses. index() is the only
 * action we need for listing: load products from the database and pass
 * them to the view. We use Eloquent's all() for a simple list; for many
 * products we could switch to paginate() later. Returning a View (Blade)
 * is the typical web response; the view receives $products for the loop.
 */
class ProductController extends Controller
{
    /**
     * Display the product listing (public, no auth required).
     */
    public function index(): View
    {
        $products = Product::orderBy('title')->get();

        return view('products.index', compact('products'));
    }
}
