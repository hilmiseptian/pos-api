<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_request_header', function (Blueprint $table) {
            $table->date('req_date')->nullable()->after('pr_date');
        });

        DB::table('purchase_request_header')
            ->whereNull('req_date')
            ->update(['req_date' => DB::raw('pr_date')]);

        Schema::table('purchase_request_header', function (Blueprint $table) {
            $table->date('req_date')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('purchase_request_header', function (Blueprint $table) {
            $table->dropColumn('req_date');
        });
    }
};