
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE suppliers
            MODIFY created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            MODIFY updated_at TIMESTAMP NULL DEFAULT NULL
                ON UPDATE CURRENT_TIMESTAMP
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE suppliers
            MODIFY created_at TIMESTAMP NULL DEFAULT NULL,
            MODIFY updated_at TIMESTAMP NULL DEFAULT NULL
        ");
    }
};
