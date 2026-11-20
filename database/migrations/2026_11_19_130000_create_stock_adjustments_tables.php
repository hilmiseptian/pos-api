<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_adjustments_head', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('adjustment_number', 50)->unique('stock_adjustments_head_number_unique');
            $table->date('adjustment_date');
            $table->string('adjustment_type', 30);
            $table->string('reason', 255)->nullable();
            $table->enum('status', ['draft', 'posted', 'cancelled'])->default('draft');
            $table->unsignedBigInteger('total_qty')->default(0);
            $table->foreignId('created_by')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            $table->index(['branch_id', 'status'], 'stock_adjustments_head_branch_status_index');
            $table->index(['branch_id', 'adjustment_date'], 'stock_adjustments_head_branch_date_index');
        });

        Schema::create('stock_adjustments_detail', function (Blueprint $table) {
            $table->id();
            $table->string('adjustment_number', 50);
            $table->foreignId('item_id')->constrained('items')->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedBigInteger('system_qty')->default(0);
            $table->unsignedBigInteger('actual_qty')->default(0);
            $table->bigInteger('difference_qty')->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            $table->foreign('adjustment_number', 'stock_adjustments_detail_number_fk')
                ->references('adjustment_number')->on('stock_adjustments_head')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->unique(['adjustment_number', 'item_id'], 'stock_adjustments_detail_number_item_unique');
            $table->index('item_id', 'stock_adjustments_detail_item_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments_detail');
        Schema::dropIfExists('stock_adjustments_head');
    }
};
