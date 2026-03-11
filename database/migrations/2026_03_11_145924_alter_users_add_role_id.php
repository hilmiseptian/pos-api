<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Extend enum to include superadmin
            $table->enum('role', ['superadmin', 'owner', 'admin', 'cashier'])
                ->default('cashier')
                ->change();

            // Dynamic role FK — null for superadmin/owner (they bypass permissions)
            $table->foreignId('role_id')
                ->nullable()
                ->after('role')
                ->constrained('roles')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
            $table->enum('role', ['owner', 'admin', 'cashier'])
                ->default('cashier')
                ->change();
        });
    }
};
