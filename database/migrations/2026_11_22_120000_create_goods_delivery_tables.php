<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goods_delivery_header', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('ref_type', 50);
            $table->string('ref_num', 50);
            $table->string('gd_number', 40)->unique('goods_delivery_header_number_unique');
            $table->date('gd_date');
            $table->enum('status', ['posted'])->default('posted');
            $table->unsignedBigInteger('total_qty')->default(0);
            $table->foreignId('created_by')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            $table->index(['company_id', 'branch_id', 'status'], 'goods_delivery_header_scope_status_index');
            $table->index(['company_id', 'ref_type', 'ref_num', 'status'], 'goods_delivery_header_source_index');
            $table->index(['company_id', 'gd_date'], 'goods_delivery_header_scope_date_index');
        });

        Schema::create('goods_delivery_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gd_header_id')->constrained('goods_delivery_header')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedBigInteger('qty');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            $table->unique(['gd_header_id', 'item_id'], 'goods_delivery_detail_header_item_unique');
            $table->index('item_id', 'goods_delivery_detail_item_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_delivery_detail');
        Schema::dropIfExists('goods_delivery_header');
    }
};
