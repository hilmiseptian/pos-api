<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. sales_head ─────────────────────────────────────────────────────
        Schema::create('sales_head', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cashier_id')->constrained('users')->restrictOnDelete();
            $table->string('sales_number')->unique();
            $table->enum('status', ['open', 'paid', 'cancelled'])->default('open');
            $table->decimal('total_amount', 12, 2)->default(0);   // sum of subtotals before discount
            $table->decimal('discount_amount', 12, 2)->default(0); // order-level discount
            $table->decimal('grand_total', 12, 2)->default(0);    // total_amount - discount_amount
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // ── 2. sales_detail ───────────────────────────────────────────────────
        Schema::create('sales_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_head_id')->constrained('sales_head')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();
            $table->integer('qty');
            $table->decimal('unit_price', 12, 2);      // snapshot of selling_price at time of sale
            $table->decimal('discount_amount', 12, 2)->default(0); // per-item promo/discount
            $table->decimal('subtotal', 12, 2);        // (unit_price * qty) - discount_amount
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // ── 3. sales_payment ──────────────────────────────────────────────────
        Schema::create('sales_payment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_head_id')->constrained('sales_head')->cascadeOnDelete();
            $table->enum('payment_method', ['cash', 'qris']);
            $table->decimal('amount_paid', 12, 2);
            $table->decimal('change_amount', 12, 2)->default(0); // only relevant for cash
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_payment');
        Schema::dropIfExists('sales_detail');
        Schema::dropIfExists('sales_head');
    }
};
