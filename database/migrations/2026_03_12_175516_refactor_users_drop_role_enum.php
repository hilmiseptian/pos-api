<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Refactor: replace the `role` enum (admin|cashier) with a `type` enum
 * that only captures structural hierarchy (superadmin|owner|staff).
 *
 * All display/permission roles are now handled exclusively via `role_id`
 * (FK → roles table). Staff users (former admin/cashier) must have a
 * role_id assigned; superadmin/owner bypass permissions as before.
 *
 * Data migration:
 *   - 'superadmin' → type = 'superadmin', role_id = null
 *   - 'owner'      → type = 'owner',      role_id = null
 *   - 'admin'      → type = 'staff',      role_id unchanged
 *   - 'cashier'    → type = 'staff',      role_id unchanged
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Add the new `type` column alongside the existing `role` enum
        Schema::table('users', function (Blueprint $table) {
            $table->enum('type', ['superadmin', 'owner', 'staff'])
                ->default('staff')
                ->after('role');
        });

        // 2. Migrate data from old `role` → new `type`
        DB::table('users')->update([
            'type' => DB::raw("
                CASE role
                    WHEN 'superadmin' THEN 'superadmin'
                    WHEN 'owner'      THEN 'owner'
                    ELSE                   'staff'
                END
            "),
        ]);

        // 3. Drop the old `role` enum column
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }

    public function down(): void
    {
        // 1. Re-add the old `role` enum
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['superadmin', 'owner', 'admin', 'cashier'])
                ->default('cashier')
                ->after('type');
        });

        // 2. Restore data (staff → admin; we can't recover cashier vs admin)
        DB::table('users')->update([
            'role' => DB::raw("
                CASE type
                    WHEN 'superadmin' THEN 'superadmin'
                    WHEN 'owner'      THEN 'owner'
                    ELSE                   'admin'
                END
            "),
        ]);

        // 3. Drop the new `type` column
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
