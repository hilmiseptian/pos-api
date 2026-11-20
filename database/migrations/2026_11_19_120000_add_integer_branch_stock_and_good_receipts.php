<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->assertWholeQuantities('purchase_request_header', 'total_qty');
        $this->assertWholeQuantities('purchase_request_detail', 'qty');
        $this->assertWholeQuantities('purchase_order_header', 'total_qty');
        $this->assertWholeQuantities('purchase_order_detail', 'qty');
        $this->assertWholeQuantities('sales_detail', 'qty');

        DB::statement('ALTER TABLE purchase_request_header MODIFY total_qty BIGINT UNSIGNED NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE purchase_request_detail MODIFY qty BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE purchase_order_header MODIFY total_qty BIGINT UNSIGNED NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE purchase_order_detail MODIFY qty BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE sales_detail MODIFY qty BIGINT UNSIGNED NOT NULL');

        Schema::create('stock_period', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnUpdate()->restrictOnDelete();
            $table->date('stock_date');
            $table->unsignedBigInteger('qty')->default(0);
            $table->unique(['company_id', 'branch_id', 'item_id'], 'stock_period_company_branch_item_unique');
            $table->index(['company_id', 'branch_id', 'stock_date'], 'stock_period_branch_date_index');
        });

        Schema::create('stock_card', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnUpdate()->restrictOnDelete();
            $table->dateTime('stock_date');
            $table->string('ref_num', 50);
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('trans_type', 50);
            $table->unsignedBigInteger('in_qty')->default(0);
            $table->unsignedBigInteger('out_qty')->default(0);
            $table->decimal('hpp', 20, 4)->default(0);
            $table->index(['company_id', 'branch_id', 'item_id', 'stock_date'], 'stock_card_item_date_index');
            $table->index(['company_id', 'ref_num'], 'stock_card_reference_index');
        });

        DB::statement("INSERT INTO stock_period (company_id, branch_id, item_id, stock_date, qty)
            SELECT b.company_id, b.id, i.id, CURRENT_DATE(), 0
            FROM branches b
            INNER JOIN items i ON i.company_id = b.company_id");

        Schema::create('good_receipts_head', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('po_number', 40);
            $table->string('gr_number', 40)->unique('good_receipts_head_gr_number_unique');
            $table->date('gr_date');
            $table->enum('status', ['draft', 'posted', 'cancelled'])->default('draft');
            $table->unsignedBigInteger('total_qty')->default(0);
            $table->decimal('total_price', 15, 2)->default(0);
            $table->foreignId('created_by')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            $table->foreign('po_number', 'good_receipts_head_po_number_fk')
                ->references('po_number')->on('purchase_order_header')
                ->cascadeOnUpdate()->restrictOnDelete();
            $table->index(['branch_id', 'status'], 'good_receipts_head_branch_status_index');
            $table->index(['po_number', 'status'], 'good_receipts_head_po_status_index');
        });

        Schema::create('good_receipts_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('gr_number', 40);
            $table->unsignedBigInteger('qty');
            $table->decimal('price', 12, 2);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            $table->foreign('gr_number', 'good_receipts_detail_gr_number_fk')
                ->references('gr_number')->on('good_receipts_head')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->unique(['gr_number', 'item_id'], 'good_receipts_detail_gr_item_unique');
        });

        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('stock');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->integer('stock')->default(0)->after('selling_price');
        });

        Schema::dropIfExists('good_receipts_detail');
        Schema::dropIfExists('good_receipts_head');
        Schema::dropIfExists('stock_card');
        Schema::dropIfExists('stock_period');

        DB::statement('ALTER TABLE sales_detail MODIFY qty INT NOT NULL');
        DB::statement('ALTER TABLE purchase_order_detail MODIFY qty DECIMAL(15,3) NOT NULL');
        DB::statement('ALTER TABLE purchase_order_header MODIFY total_qty DECIMAL(15,3) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE purchase_request_detail MODIFY qty DECIMAL(15,3) NOT NULL');
        DB::statement('ALTER TABLE purchase_request_header MODIFY total_qty DECIMAL(15,3) NOT NULL DEFAULT 0');
    }

    private function assertWholeQuantities(string $table, string $column): void
    {
        $fractionalOrNegative = DB::table($table)
            ->whereRaw("{$column} < 0 OR {$column} <> FLOOR({$column})")
            ->exists();

        if ($fractionalOrNegative) {
            throw new RuntimeException("Cannot convert {$table}.{$column}: negative or fractional quantities exist.");
        }
    }
};
