<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 3 – Create carts table
 *
 * WHY: We store the cart in the database (not session) so it persists across
 * devices and browsers. Each row = one product in a user's cart. user_id +
 * product_id could be unique so we don't duplicate rows for the same product
 * (we'll update quantity instead). Foreign keys enforce integrity: no cart
 * for deleted users/products. Indexes on user_id speed up "get my cart" queries.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();

            $table->unique(['user_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
