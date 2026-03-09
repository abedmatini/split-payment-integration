<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 3 – Create orders table
 *
 * WHY: An order is the record of a purchase. We store total_amount (cart total),
 * rewards_used (how much came from user's balance), and card_amount (what was
 * charged via PayFast). That supports split payment and reporting. status
 * tracks lifecycle: pending (created) → paid (webhook confirmed) → completed
 * (manual, admin). payfast_reference stores the gateway's ID for idempotency
 * and support. All money columns are decimal for accuracy.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('total_amount', 10, 2);
            $table->decimal('rewards_used', 10, 2)->default(0);
            $table->decimal('card_amount', 10, 2);
            $table->string('status', 20)->default('pending');
            $table->string('payfast_reference')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
