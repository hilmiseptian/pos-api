<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_request_header', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('pr_number', 40)->unique('purchase_request_header_pr_number_unique');
            $table->date('pr_date');
            $table->enum('status', ['open', 'partially_ordered', 'ordered', 'cancelled'])->default('open');
            $table->decimal('total_qty', 15, 3)->default(0);
            $table->decimal('total_price', 15, 2)->default(0);
            $table->foreignId('created_by')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            $table->index(['company_id', 'branch_id', 'status'], 'purchase_request_header_scope_status_index');
            $table->index(['company_id', 'pr_date'], 'purchase_request_header_scope_date_index');
        });

        Schema::create('purchase_request_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('pr_number', 40);
            $table->decimal('qty', 15, 3);
            $table->decimal('price', 12, 2);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            $table->foreign('pr_number', 'purchase_request_detail_pr_number_fk')
                ->references('pr_number')->on('purchase_request_header')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->index(['pr_number', 'item_id'], 'purchase_request_detail_pr_item_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_request_detail');
        Schema::dropIfExists('purchase_request_header');
    }
};