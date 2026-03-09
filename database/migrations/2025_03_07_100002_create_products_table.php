<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 3 – Create products table
 *
 * WHY: E-commerce needs a catalog. We keep it simple: title, description, price.
 * Price is decimal(10,2) for ZAR (currency). We don't store stock or SKU in
 * this MVP; the table is the single source of truth for what can be sold.
 * Eloquent will map this to a Product model for easy querying.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
