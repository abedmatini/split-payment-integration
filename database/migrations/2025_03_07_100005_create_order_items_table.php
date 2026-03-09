<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 3 – Create order_items table
 *
 * WHY: An order has many line items (one per product). We store quantity and
 * price_at_purchase so the order is a snapshot: if the product price changes
 * later, the order still shows what the user paid. This is standard for
 * e-commerce. order_id + product_id could have duplicates (same product
 * in one order with one line per quantity is fine; we use one row per
 * product with quantity for simplicity).
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->unsignedInteger('quantity');
            $table->decimal('price_at_purchase', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
