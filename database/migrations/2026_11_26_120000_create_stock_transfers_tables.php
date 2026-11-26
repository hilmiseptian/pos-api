<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transfer_header', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('from_branch_id')->constrained('branches')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('to_branch_id')->constrained('branches')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('transfer_number', 40)->unique('stock_transfer_header_number_unique');
            $table->date('transfer_date');
            $table->enum('status', ['draft', 'in_progress', 'completed', 'cancelled'])->default('draft');
            $table->unsignedBigInteger('total_qty')->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            $table->index(['company_id', 'from_branch_id', 'status'], 'stock_transfer_header_from_status_index');
            $table->index(['company_id', 'to_branch_id', 'status'], 'stock_transfer_header_to_status_index');
            $table->index(['company_id', 'transfer_date'], 'stock_transfer_header_date_index');
        });

        Schema::create('stock_transfer_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transfer_header_id')->constrained('stock_transfer_header')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedBigInteger('qty');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            $table->unique(['transfer_header_id', 'item_id'], 'stock_transfer_detail_header_item_unique');
        });

        Schema::create('stock_transfer_receipt_header', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('transfer_header_id')->constrained('stock_transfer_header')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('gd_header_id')->constrained('goods_delivery_header')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('receipt_number', 40)->unique('stock_transfer_receipt_header_number_unique');
            $table->date('receipt_date');
            $table->enum('status', ['posted'])->default('posted');
            $table->unsignedBigInteger('total_qty')->default(0);
            $table->foreignId('received_by')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            $table->index(['company_id', 'branch_id', 'receipt_date'], 'stock_transfer_receipt_scope_date_index');
            $table->index(['transfer_header_id', 'receipt_date'], 'stock_transfer_receipt_transfer_date_index');
            $table->index('gd_header_id', 'stock_transfer_receipt_gd_index');
        });

        Schema::create('stock_transfer_receipt_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receipt_header_id')->constrained('stock_transfer_receipt_header')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedBigInteger('qty');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            $table->unique(['receipt_header_id', 'item_id'], 'stock_transfer_receipt_detail_header_item_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transfer_receipt_detail');
        Schema::dropIfExists('stock_transfer_receipt_header');
        Schema::dropIfExists('stock_transfer_detail');
        Schema::dropIfExists('stock_transfer_header');
    }
};
