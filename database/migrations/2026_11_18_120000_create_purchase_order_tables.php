<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_order_header', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('po_number', 40)->unique('purchase_order_header_po_number_unique');
            $table->string('ref_num', 40);
            $table->date('po_date');
            $table->date('due_date');
            $table->enum('status', ['draft', 'ordered', 'partially_received', 'received', 'cancelled'])->default('draft');
            $table->decimal('total_qty', 15, 3)->default(0);
            $table->decimal('total_price', 15, 2)->default(0);
            $table->foreignId('created_by')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            $table->foreign('ref_num', 'purchase_order_header_ref_num_fk')
                ->references('pr_number')->on('purchase_request_header')
                ->cascadeOnUpdate()->restrictOnDelete();
            $table->index(['branch_id', 'status'], 'purchase_order_header_branch_status_index');
            $table->index(['ref_num', 'status'], 'purchase_order_header_ref_status_index');
            $table->index(['po_date', 'due_date'], 'purchase_order_header_dates_index');
        });

        Schema::create('purchase_order_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('po_header_id')->constrained('purchase_order_header')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnUpdate()->restrictOnDelete();
            $table->decimal('qty', 15, 3);
            $table->decimal('price', 12, 2);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            $table->index(['po_header_id', 'item_id'], 'purchase_order_detail_header_item_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_detail');
        Schema::dropIfExists('purchase_order_header');
    }
};