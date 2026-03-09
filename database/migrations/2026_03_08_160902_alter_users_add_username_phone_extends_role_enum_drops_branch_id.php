<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable()->after('name');
            $table->string('phone')->nullable()->after('email');
            $table->boolean('is_active')->default(true)->after('role');
            // extend role to include admin
            $table->enum('role', ['owner', 'admin', 'cashier'])->default('cashier')->change();
            // drop branch_id — replaced by pivot table
            $table->dropConstrainedForeignId('branch_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'phone']);
            $table->enum('role', ['owner', 'cashier'])->default('cashier')->change();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
        });
    }
};
