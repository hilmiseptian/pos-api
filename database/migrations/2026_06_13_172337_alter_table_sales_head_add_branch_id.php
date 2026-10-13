<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_head', function (Blueprint $table) {
            $table->foreignId('branch_id')
                ->nullable()
                ->after('company_id')
                ->constrained('branches')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sales_head', function (Blueprint $table) {
            $table->dropConstrainedForeignId('branch_id');
        });
    }
};
