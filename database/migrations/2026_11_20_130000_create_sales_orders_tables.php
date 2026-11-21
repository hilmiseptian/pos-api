<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_order_header', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('so_number', 40)->unique('sales_order_header_number_unique');
            $table->date('so_date');
            $table->date('delivery_date')->nullable();
            $table->string('customer_name', 150);
            $table->text('delivery_address')->nullable();
            $table->enum('status', ['open', 'partially_delivered', 'delivered', 'cancelled'])->default('open');
            $table->unsignedBigInteger('total_qty')->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            $table->index(['company_id', 'branch_id', 'status'], 'sales_order_header_scope_status_index');
            $table->index(['company_id', 'so_date'], 'sales_order_header_scope_date_index');
            $table->index(['customer_id', 'so_date'], 'sales_order_header_customer_date_index');
        });

        Schema::create('sales_order_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('so_header_id')->constrained('sales_order_header')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedBigInteger('qty');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2);
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            $table->index(['so_header_id', 'item_id'], 'sales_order_detail_header_item_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_order_detail');
        Schema::dropIfExists('sales_order_header');
    }
};
