<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 3 – Add rewards_balance to users
 *
 * WHY: Our MVP lets users pay with "store reward credit" (internal balance).
 * We store that balance on the User model so it's easy to read (e.g. checkout)
 * and update (e.g. after payment webhook). Default 500.00 gives new users
 * starter rewards for testing. We use decimal(10,2) for money to avoid
 * floating-point errors (never use float for currency).
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('rewards_balance', 10, 2)->default(500.00)->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('rewards_balance');
        });
    }
};
