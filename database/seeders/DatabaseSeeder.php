<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | COMPANY
            |--------------------------------------------------------------------------
            */

            $companyId = DB::table('companies')->insertGetId([
                'name' => 'Demo Mart',
                'code' => 'DMT001',
                'email' => 'demo@mart.com',
                'phone' => '08123456789',
                'address' => 'Jakarta',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | ROLE
            |--------------------------------------------------------------------------
            */

            $roleId = DB::table('roles')->insertGetId([
                'company_id' => $companyId,
                'name' => 'Super Admin',
                'description' => 'Full system access',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | PERMISSIONS
            |--------------------------------------------------------------------------
            */

            $permissions = [

                // ── Users ──────────────────────────────────────────────────────────
                ['module' => 'users', 'slug' => 'users.view',   'name' => 'View Users'],
                ['module' => 'users', 'slug' => 'users.create', 'name' => 'Create Users'],
                ['module' => 'users', 'slug' => 'users.edit',   'name' => 'Edit Users'],
                ['module' => 'users', 'slug' => 'users.delete', 'name' => 'Delete Users'],

                // ── Roles ──────────────────────────────────────────────────────────
                ['module' => 'roles', 'slug' => 'roles.view',   'name' => 'View Roles'],
                ['module' => 'roles', 'slug' => 'roles.create', 'name' => 'Create Roles'],
                ['module' => 'roles', 'slug' => 'roles.edit',   'name' => 'Edit Roles'],
                ['module' => 'roles', 'slug' => 'roles.delete', 'name' => 'Delete Roles'],

                // ── Items ──────────────────────────────────────────────────────────
                ['module' => 'items', 'slug' => 'items.view',   'name' => 'View Items'],
                ['module' => 'items', 'slug' => 'items.create', 'name' => 'Create Items'],
                ['module' => 'items', 'slug' => 'items.edit',   'name' => 'Edit Items'],
                ['module' => 'items', 'slug' => 'items.delete', 'name' => 'Delete Items'],

                // ── Categories ─────────────────────────────────────────────────────
                ['module' => 'categories', 'slug' => 'categories.view',   'name' => 'View Categories'],
                ['module' => 'categories', 'slug' => 'categories.create', 'name' => 'Create Categories'],
                ['module' => 'categories', 'slug' => 'categories.edit',   'name' => 'Edit Categories'],
                ['module' => 'categories', 'slug' => 'categories.delete', 'name' => 'Delete Categories'],

                // ── Sub Categories ─────────────────────────────────────────────────
                ['module' => 'subcategories', 'slug' => 'subcategories.view',   'name' => 'View Sub Categories'],
                ['module' => 'subcategories', 'slug' => 'subcategories.create', 'name' => 'Create Sub Categories'],
                ['module' => 'subcategories', 'slug' => 'subcategories.edit',   'name' => 'Edit Sub Categories'],
                ['module' => 'subcategories', 'slug' => 'subcategories.delete', 'name' => 'Delete Sub Categories'],

                // ── Branches ───────────────────────────────────────────────────────
                ['module' => 'branches', 'slug' => 'branches.view',   'name' => 'View Branches'],
                ['module' => 'branches', 'slug' => 'branches.create', 'name' => 'Create Branches'],
                ['module' => 'branches', 'slug' => 'branches.edit',   'name' => 'Edit Branches'],
                ['module' => 'branches', 'slug' => 'branches.delete', 'name' => 'Delete Branches'],

                // ── Companies ──────────────────────────────────────────────────────
                ['module' => 'companies', 'slug' => 'companies.view',   'name' => 'View Companies'],
                ['module' => 'companies', 'slug' => 'companies.create', 'name' => 'Create Companies'],
                ['module' => 'companies', 'slug' => 'companies.edit',   'name' => 'Edit Companies'],
                ['module' => 'companies', 'slug' => 'companies.delete', 'name' => 'Delete Companies'],

                // ── Orders / POS ───────────────────────────────────────────────────
                ['module' => 'orders', 'slug' => 'orders.view',    'name' => 'View Orders'],
                ['module' => 'orders', 'slug' => 'orders.create',  'name' => 'Create Orders'],
                ['module' => 'orders', 'slug' => 'orders.edit',    'name' => 'Edit Orders'],
                ['module' => 'orders', 'slug' => 'orders.delete',  'name' => 'Delete Orders'],
                ['module' => 'orders', 'slug' => 'orders.payment', 'name' => 'Process Payments'],
            ];

            foreach ($permissions as $permission) {

                $permissionId = DB::table('permissions')->insertGetId([
                    'module' => $permission['module'],
                    'slug' => $permission['slug'],
                    'name' => $permission['name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('role_permissions')->insert([
                    'role_id' => $roleId,
                    'permission_id' => $permissionId,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | USER
            |--------------------------------------------------------------------------
            */

            $userId = DB::table('users')->insertGetId([
                'company_id' => $companyId,
                'role_id' => $roleId,
                'type' => 'superadmin',
                'name' => 'Administrator',
                'username' => 'admin',
                'email' => 'admin@mail.com',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | BRANCH
            |--------------------------------------------------------------------------
            */

            $branchId = DB::table('branches')->insertGetId([
                'company_id' => $companyId,
                'name' => 'Main Branch',
                'code' => 'BR001',
                'city' => 'Jakarta',
                'address' => 'Central Jakarta',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | BRANCH USER
            |--------------------------------------------------------------------------
            */

            DB::table('branch_user')->insert([
                'branch_id' => $branchId,
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | CATEGORIES
            |--------------------------------------------------------------------------
            */

            DB::unprepared("
                INSERT INTO `categories`
                (`id`, `company_id`, `name`, `code`, `is_active`, `sort_order`, `created_at`, `updated_at`)
                VALUES
                (1,  {$companyId}, 'Makanan & Minuman',   'MKN', 1, 1, NOW(), NOW()),
                (2,  {$companyId}, 'Sembako',             'SMB', 1, 2, NOW(), NOW()),
                (3,  {$companyId}, 'Minuman',             'MNM', 1, 3, NOW(), NOW()),
                (4,  {$companyId}, 'Snack & Camilan',     'SNK', 1, 4, NOW(), NOW()),
                (5,  {$companyId}, 'Produk Segar',        'SGR', 1, 5, NOW(), NOW()),
                (6,  {$companyId}, 'Bumbu Dapur',         'BMB', 1, 6, NOW(), NOW()),
                (7,  {$companyId}, 'Perawatan Diri',      'PRW', 1, 7, NOW(), NOW()),
                (8,  {$companyId}, 'Perlengkapan Rumah',  'PLR', 1, 8, NOW(), NOW()),
                (9,  {$companyId}, 'Obat-obatan',         'OBT', 1, 9, NOW(), NOW()),
                (10, {$companyId}, 'Alat Tulis & Kantor', 'ATK', 1, 10, NOW(), NOW());
            ");

            /*
            |--------------------------------------------------------------------------
            | SUB CATEGORIES
            |--------------------------------------------------------------------------
            */

            DB::unprepared("
                INSERT INTO `sub_categories`
                (`id`, `company_id`, `category_id`, `name`, `is_active`, `created_at`, `updated_at`)
                VALUES
                -- Makanan & Minuman (category_id: 1)
                (1, {$companyId}, 1, 'Mie Instan',             1, NOW(), NOW()),
                (2, {$companyId}, 1, 'Beras & Serealia',       1, NOW(), NOW()),
                (3, {$companyId}, 1, 'Minyak & Lemak',         1, NOW(), NOW()),
                (4, {$companyId}, 1, 'Gula & Pemanis',         1, NOW(), NOW()),
                (5, {$companyId}, 1, 'Teh & Kopi',             1, NOW(), NOW()),
                (6, {$companyId}, 1, 'Susu & Produk Olahan',   1, NOW(), NOW()),
                (7, {$companyId}, 1, 'Tepung & Bahan Kue',     1, NOW(), NOW()),
                (8, {$companyId}, 1, 'Saus & Kecap',           1, NOW(), NOW()),

                -- Sembako (category_id: 2)
                (9, {$companyId}, 2, 'Bahan Pokok',            1, NOW(), NOW()),
                (10, {$companyId}, 2, 'Sabun & Kebersihan',     1, NOW(), NOW()),
                (11, {$companyId}, 2, 'Deterjen & Laundry',     1, NOW(), NOW()),
                (12, {$companyId}, 2, 'Perawatan Gigi',         1, NOW(), NOW()),
                (13, {$companyId}, 2, 'Perawatan Rambut',       1, NOW(), NOW()),

                -- Minuman (category_id: 3)
                (14, {$companyId}, 3, 'Air Mineral',            1, NOW(), NOW()),
                (15, {$companyId}, 3, 'Minuman Isotonik',       1, NOW(), NOW()),
                (16, {$companyId}, 3, 'Minuman Bersoda',        1, NOW(), NOW()),
                (17, {$companyId}, 3, 'Minuman Berenergi',      1, NOW(), NOW()),
                (18, {$companyId}, 3, 'Minuman Teh',            1, NOW(), NOW()),
                (19, {$companyId}, 3, 'Minuman Kopi',           1, NOW(), NOW()),
                (20, {$companyId}, 3, 'Minuman Serbuk',         1, NOW(), NOW()),

                -- Snack & Camilan (category_id: 4)
                (21, {$companyId}, 4, 'Keripik & Snack Gurih',  1, NOW(), NOW()),
                (22, {$companyId}, 4, 'Biskuit & Wafer',        1, NOW(), NOW()),
                (23, {$companyId}, 4, 'Kacang-kacangan',        1, NOW(), NOW()),
                (24, {$companyId}, 4, 'Roti & Bakeri',          1, NOW(), NOW()),
                (25, {$companyId}, 4, 'Permen & Cokelat',       1, NOW(), NOW()),

                -- Produk Segar (category_id: 5)
                (26, {$companyId}, 5, 'Daging & Unggas',        1, NOW(), NOW()),
                (27, {$companyId}, 5, 'Ikan & Seafood',         1, NOW(), NOW()),
                (28, {$companyId}, 5, 'Tahu & Tempe',           1, NOW(), NOW()),
                (29, {$companyId}, 5, 'Sayuran Hijau',          1, NOW(), NOW()),
                (30, {$companyId}, 5, 'Buah-buahan',            1, NOW(), NOW()),
                (31, {$companyId}, 5, 'Umbi & Rimpang',         1, NOW(), NOW()),

                -- Bumbu Dapur (category_id: 6)
                (32, {$companyId}, 6, 'Bumbu Segar',            1, NOW(), NOW()),
                (33, {$companyId}, 6, 'Bumbu Instan & Sachet',  1, NOW(), NOW()),
                (34, {$companyId}, 6, 'Saus & Sambal',          1, NOW(), NOW()),
                (35, {$companyId}, 6, 'Rempah Kering',          1, NOW(), NOW()),
                (36, {$companyId}, 6, 'Penyedap Rasa',          1, NOW(), NOW()),
                (37, {$companyId}, 6, 'Santan & Kelapa',        1, NOW(), NOW()),

                -- Perawatan Diri (category_id: 7)
                (38, {$companyId}, 7, 'Perawatan Kulit',        1, NOW(), NOW()),
                (39, {$companyId}, 7, 'Perawatan Wajah',        1, NOW(), NOW()),
                (40, {$companyId}, 7, 'Deodorant & Parfum',     1, NOW(), NOW()),
                (41, {$companyId}, 7, 'Perawatan Bayi',         1, NOW(), NOW()),
                (42, {$companyId}, 7, 'Perawatan Gigi & Mulut', 1, NOW(), NOW()),
                (43, {$companyId}, 7, 'Alat Kesehatan Pribadi', 1, NOW(), NOW()),

                -- Perlengkapan Rumah (category_id: 8)
                (44, {$companyId}, 8, 'Pembersih Rumah',        1, NOW(), NOW()),
                (45, {$companyId}, 8, 'Perawatan Pakaian',      1, NOW(), NOW()),
                (46, {$companyId}, 8, 'Perlengkapan Dapur',     1, NOW(), NOW()),
                (47, {$companyId}, 8, 'Elektronik Rumah',       1, NOW(), NOW()),
                (48, {$companyId}, 8, 'Tisu & Kertas',          1, NOW(), NOW()),
                (49, {$companyId}, 8, 'Perlengkapan Umum',      1, NOW(), NOW()),

                -- Obat-obatan (category_id: 9)
                (50, {$companyId}, 9, 'Obat Bebas',             1, NOW(), NOW()),
                (51, {$companyId}, 9, 'Obat Herbal & Jamu',     1, NOW(), NOW()),
                (52, {$companyId}, 9, 'Antiseptik & P3K',       1, NOW(), NOW()),
                (53, {$companyId}, 9, 'Suplemen & Vitamin',     1, NOW(), NOW()),
                (54, {$companyId}, 9, 'Obat Luar',              1, NOW(), NOW()),

                -- Alat Tulis & Kantor (category_id: 10)
                (55, {$companyId}, 10, 'Alat Tulis',            1, NOW(), NOW()),
                (56, {$companyId}, 10, 'Buku & Kertas',         1, NOW(), NOW()),
                (57, {$companyId}, 10, 'Perekat & Pemotong',    1, NOW(), NOW()),
                (58, {$companyId}, 10, 'Pengarsipan & Map',     1, NOW(), NOW()),
                (59, {$companyId}, 10, 'Alat Jilid & Stapler',  1, NOW(), NOW());
            ");

            /*
            |--------------------------------------------------------------------------
            | ITEMS
            |--------------------------------------------------------------------------
            */

            DB::unprepared("
                INSERT INTO `items` 
                (`id`, `company_id`, `category_id`, `name`, `sku`, `description`, `cost_price`, `selling_price`, `stock`, `min_stock`, `unit`, `is_active`, `created_at`, `updated_at`) 
                VALUES
                -- Makanan & Minuman (category_id: 1)
                (1, {$companyId}, 1, 'Indomie Goreng Original', 'SKU-001', 'Mie instan goreng rasa original', 2500.00, 3500.00, 500, 50, 'pcs', 1, NOW(), NOW()),
                (2, {$companyId}, 1, 'Indomie Kuah Ayam Bawang', 'SKU-002', 'Mie instan kuah rasa ayam bawang', 2500.00, 3500.00, 500, 50, 'pcs', 1, NOW(), NOW()),
                (3, {$companyId}, 1, 'Beras Premium Cap Jago 5kg', 'SKU-003', 'Beras putih premium kemasan 5kg', 55000.00, 68000.00, 200, 20, 'box', 1, NOW(), NOW()),
                (4, {$companyId}, 1, 'Minyak Goreng Bimoli 2L', 'SKU-004', 'Minyak goreng sawit kemasan 2 liter', 28000.00, 34000.00, 150, 15, 'liter', 1, NOW(), NOW()),
                (5, {$companyId}, 1, 'Gula Pasir Gulaku 1kg', 'SKU-005', 'Gula pasir putih kemasan 1kg', 13000.00, 16000.00, 300, 30, 'kg', 1, NOW(), NOW()),
                (6, {$companyId}, 1, 'Teh Botol Sosro 350ml', 'SKU-006', 'Minuman teh manis dalam botol 350ml', 4000.00, 6000.00, 400, 40, 'pcs', 1, NOW(), NOW()),
                (7, {$companyId}, 1, 'Kopi Kapal Api Special 165g', 'SKU-007', 'Kopi bubuk robusta pilihan kemasan 165g', 12000.00, 15000.00, 250, 25, 'pcs', 1, NOW(), NOW()),
                (8, {$companyId}, 1, 'Susu Ultra Milk Full Cream 1L', 'SKU-008', 'Susu UHT full cream kemasan 1 liter', 15000.00, 18500.00, 180, 20, 'liter', 1, NOW(), NOW()),
                (9, {$companyId}, 1, 'Tepung Terigu Segitiga Biru 1kg', 'SKU-009', 'Tepung terigu serbaguna kemasan 1kg', 9000.00, 12000.00, 300, 30, 'kg', 1, NOW(), NOW()),
                (10,{$companyId}, 1, 'Kecap Manis Bango 600ml', 'SKU-010', 'Kecap manis dari kedelai pilihan 600ml', 18000.00, 22000.00, 200, 20, 'pcs', 1, NOW(), NOW()),

                -- Sembako (category_id: 2)
                (11, {$companyId}, 2, 'Telur Ayam Negeri 1kg', 'SKU-011', 'Telur ayam ras segar per kilogram', 22000.00, 27000.00, 100, 20, 'kg', 1, NOW(), NOW()),
                (12, {$companyId}, 2, 'Garam Halus Refina 500g', 'SKU-012', 'Garam halus beryodium kemasan 500g', 3000.00, 5000.00, 400, 40, 'pcs', 1, NOW(), NOW()),
                (13, {$companyId}, 2, 'Minyak Tanah 1L', 'SKU-013', 'Minyak tanah untuk kompor', 8000.00, 11000.00, 100, 10, 'liter', 1, NOW(), NOW()),
                (14, {$companyId}, 2, 'Sabun Mandi Lifebuoy', 'SKU-014', 'Sabun mandi antibakteri 110g', 4500.00, 6500.00, 350, 35, 'pcs', 1, NOW(), NOW()),
                (15, {$companyId}, 2, 'Deterjen Rinso 900g', 'SKU-015', 'Deterjen bubuk untuk mencuci pakaian 900g', 18000.00, 23000.00, 200, 20, 'pcs', 1, NOW(), NOW()),
                (16, {$companyId}, 2, 'Pasta Gigi Pepsodent 190g', 'SKU-016', 'Pasta gigi fluoride untuk keluarga 190g', 12000.00, 15000.00, 250, 25, 'pcs', 1, NOW(), NOW()),
                (17, {$companyId}, 2, 'Shampoo Pantene 170ml', 'SKU-017', 'Sampo anti-rontok kemasan 170ml', 18000.00, 23000.00, 200, 20, 'pcs', 1, NOW(), NOW()),
                (18, {$companyId}, 2, 'Minyak Goreng Tropical 1L', 'SKU-018', 'Minyak goreng sawit kemasan 1 liter', 14500.00, 18000.00, 200, 20, 'liter', 1, NOW(), NOW()),
                (19, {$companyId}, 2, 'Kopi Nescafe Classic 50g', 'SKU-019', 'Kopi instan klasik kemasan 50g', 10000.00, 13500.00, 300, 30, 'pcs', 1, NOW(), NOW()),
                (20, {$companyId}, 2, 'Gula Merah 500g', 'SKU-020', 'Gula merah aren asli kemasan 500g', 9000.00, 12000.00, 150, 15, 'kg', 1, NOW(), NOW()),

                -- Minuman (category_id: 3)
                (21, {$companyId}, 3, 'Aqua Galon 19L', 'SKU-021', 'Air mineral dalam galon 19 liter', 18000.00, 22000.00, 80, 10, 'pcs', 1, NOW(), NOW()),
                (22, {$companyId}, 3, 'Aqua Botol 600ml', 'SKU-022', 'Air mineral dalam botol 600ml', 2500.00, 4000.00, 500, 50, 'pcs', 1, NOW(), NOW()),
                (23, {$companyId}, 3, 'Pocari Sweat 500ml', 'SKU-023', 'Minuman isotonik kemasan 500ml', 6000.00, 8500.00, 300, 30, 'pcs', 1, NOW(), NOW()),
                (24, {$companyId}, 3, 'Sprite 1.5L', 'SKU-024', 'Minuman bersoda rasa lemon-lime 1.5L', 9000.00, 12000.00, 200, 20, 'pcs', 1, NOW(), NOW()),
                (25, {$companyId}, 3, 'Coca-Cola 390ml', 'SKU-025', 'Minuman bersoda kaleng 390ml', 6500.00, 9000.00, 300, 30, 'pcs', 1, NOW(), NOW()),
                (26, {$companyId}, 3, 'Fanta Strawberry 390ml', 'SKU-026', 'Minuman bersoda rasa stroberi kaleng 390ml', 6500.00, 9000.00, 250, 25, 'pcs', 1, NOW(), NOW()),
                (27, {$companyId}, 3, 'Mizone 500ml', 'SKU-027', 'Minuman berenergi rasa jeruk 500ml', 5000.00, 7000.00, 300, 30, 'pcs', 1, NOW(), NOW()),
                (28, {$companyId}, 3, 'Teh Pucuk Harum 350ml', 'SKU-028', 'Minuman teh hijau kemasan 350ml', 3500.00, 5500.00, 400, 40, 'pcs', 1, NOW(), NOW()),
                (29, {$companyId}, 3, 'Nutrisari Jeruk Sachet', 'SKU-029', 'Minuman serbuk rasa jeruk per sachet', 1000.00, 1500.00, 1000, 100, 'pcs', 1, NOW(), NOW()),
                (30, {$companyId}, 3, 'Good Day Cappuccino 250ml', 'SKU-030', 'Kopi susu cappuccino dalam kaleng 250ml', 6000.00, 8500.00, 250, 25, 'pcs', 1, NOW(), NOW()),

                -- Snack & Camilan (category_id: 4)
                (31, {$companyId},  4, 'Chitato Sapi Panggang 68g', 'SKU-031', 'Keripik kentang rasa sapi panggang 68g', 8000.00, 11000.00, 300, 30, 'pcs', 1, NOW(), NOW()),
                (32, {$companyId},  4, 'Pringles Original 107g', 'SKU-032', 'Keripik kentang original kemasan tabung 107g', 28000.00, 35000.00, 150, 15, 'pcs', 1, NOW(), NOW()),
                (33, {$companyId},  4, 'Oreo Original 137g', 'SKU-033', 'Biskuit sandwich cokelat krim 137g', 12000.00, 16000.00, 250, 25, 'pcs', 1, NOW(), NOW()),
                (34, {$companyId},  4, 'Wafer Tango Coklat', 'SKU-034', 'Wafer berlapis cokelat kemasan 130g', 9000.00, 13000.00, 300, 30, 'pcs', 1, NOW(), NOW()),
                (35, {$companyId},  4, 'Kacang Garuda Rasa Bawang 100g', 'SKU-035', 'Kacang kulit rasa bawang 100g', 8000.00, 11000.00, 250, 25, 'pcs', 1, NOW(), NOW()),
                (36, {$companyId},  4, 'Slai O Lai Stroberi', 'SKU-036', 'Biskuit sandwich rasa stroberi kemasan 128g', 9000.00, 13000.00, 250, 25, 'pcs', 1, NOW(), NOW()),
                (37, {$companyId},  4, 'Makaroni Ngehe Pedas', 'SKU-037', 'Snack makaroni pedas kemasan 70g', 5000.00, 8000.00, 400, 40, 'pcs', 1, NOW(), NOW()),
                (38, {$companyId},  4, 'Roti Tawar Sari Roti', 'SKU-038', 'Roti tawar putih kemasan 300g', 13000.00, 17000.00, 100, 20, 'pcs', 1, NOW(), NOW()),
                (39, {$companyId},  4, 'Keripik Singkong Pedas 100g', 'SKU-039', 'Keripik singkong rasa pedas asli 100g', 6000.00, 9000.00, 300, 30, 'pcs', 1, NOW(), NOW()),
                (40, {$companyId},  4, 'Biskuat Energi Coklat', 'SKU-040', 'Biskuit berenergi rasa coklat kemasan 120g', 7000.00, 10000.00, 300, 30, 'pcs', 1, NOW(), NOW()),

                -- Produk Segar (category_id: 5)
                (41, {$companyId}, 5, 'Ayam Broiler 1kg', 'SKU-041', 'Ayam potong segar per kilogram', 28000.00, 35000.00, 50, 10, 'kg', 1, NOW(), NOW()),
                (42, {$companyId}, 5, 'Daging Sapi Has Dalam 1kg', 'SKU-042', 'Daging sapi has dalam segar per kg', 110000.00, 130000.00, 30, 5, 'kg', 1, NOW(), NOW()),
                (43, {$companyId}, 5, 'Ikan Lele 1kg', 'SKU-043', 'Ikan lele segar per kilogram', 18000.00, 24000.00, 40, 10, 'kg', 1, NOW(), NOW()),
                (44, {$companyId}, 5, 'Tahu Putih 5 Buah', 'SKU-044', 'Tahu putih segar isi 5 buah', 5000.00, 8000.00, 100, 20, 'pcs', 1, NOW(), NOW()),
                (45, {$companyId}, 5, 'Tempe 1 Papan', 'SKU-045', 'Tempe kedelai segar 1 papan 250g', 5000.00, 8000.00, 100, 20, 'pcs', 1, NOW(), NOW()),
                (46, {$companyId}, 5, 'Kangkung 1 Ikat', 'SKU-046', 'Sayur kangkung segar 1 ikat', 2500.00, 4000.00, 80, 20, 'pcs', 1, NOW(), NOW()),
                (47, {$companyId}, 5, 'Bayam 1 Ikat', 'SKU-047', 'Sayur bayam segar 1 ikat', 2500.00, 4000.00, 80, 20, 'pcs', 1, NOW(), NOW()),
                (48, {$companyId}, 5, 'Tomat 1kg', 'SKU-048', 'Tomat merah segar per kilogram', 8000.00, 12000.00, 60, 15, 'kg', 1, NOW(), NOW()),
                (49, {$companyId}, 5, 'Cabai Merah Keriting 1kg', 'SKU-049', 'Cabai merah keriting segar per kg', 30000.00, 38000.00, 40, 10, 'kg', 1, NOW(), NOW()),
                (50, {$companyId}, 5, 'Bawang Merah 1kg', 'SKU-050', 'Bawang merah lokal segar per kg', 25000.00, 32000.00, 60, 15, 'kg', 1, NOW(), NOW()),

                -- Bumbu Dapur (category_id: 6)
                (51, {$companyId}, 6, 'Bawang Putih 1kg', 'SKU-051', 'Bawang putih lokal segar per kg', 20000.00, 27000.00, 80, 15, 'kg', 1, NOW(), NOW()),
                (52, {$companyId}, 6, 'Jahe 500g', 'SKU-052', 'Jahe segar per 500g', 8000.00, 12000.00, 60, 10, 'kg', 1, NOW(), NOW()),
                (53, {$companyId}, 6, 'Kunyit 500g', 'SKU-053', 'Kunyit segar per 500g', 6000.00, 9000.00, 60, 10, 'kg', 1, NOW(), NOW()),
                (54, {$companyId}, 6, 'Bumbu Nasi Goreng Indofood', 'SKU-054', 'Bumbu instan nasi goreng sachet 45g', 3000.00, 5000.00, 500, 50, 'pcs', 1, NOW(), NOW()),
                (55, {$companyId}, 6, 'Bumbu Rendang Bamboe', 'SKU-055', 'Bumbu instan rendang kemasan 60g', 5000.00, 8000.00, 400, 40, 'pcs', 1, NOW(), NOW()),
                (56, {$companyId}, 6, 'Sambal ABC Extra Pedas 335ml', 'SKU-056', 'Saus sambal ekstra pedas kemasan 335ml', 15000.00, 20000.00, 200, 20, 'pcs', 1, NOW(), NOW()),
                (57, {$companyId}, 6, 'Saus Tomat ABC 335ml', 'SKU-057', 'Saus tomat kemasan botol 335ml', 13000.00, 17000.00, 200, 20, 'pcs', 1, NOW(), NOW()),
                (58, {$companyId}, 6, 'Merica Bubuk Ladaku 50g', 'SKU-058', 'Merica bubuk halus kemasan 50g', 7000.00, 10000.00, 300, 30, 'pcs', 1, NOW(), NOW()),
                (59, {$companyId}, 6, 'Royco Kaldu Ayam 46g', 'SKU-059', 'Penyedap rasa kaldu ayam sachet 46g', 4000.00, 6000.00, 500, 50, 'pcs', 1, NOW(), NOW()),
                (60, {$companyId}, 6, 'Santan Kara 65ml', 'SKU-060', 'Santan kelapa instan kemasan 65ml', 4500.00, 7000.00, 400, 40, 'pcs', 1, NOW(), NOW()),

                -- Perawatan Diri (category_id: 7)
                (61, {$companyId}, 7, 'Vaseline Lotion 200ml', 'SKU-061', 'Losion pelembab kulit kemasan 200ml', 18000.00, 24000.00, 150, 15, 'pcs', 1, NOW(), NOW()),
                (62, {$companyId}, 7, 'Citra Hazeline Snow 60g', 'SKU-062', 'Krim wajah pelembab kemasan 60g', 12000.00, 17000.00, 200, 20, 'pcs', 1, NOW(), NOW()),
                (63, {$companyId}, 7, 'Deodorant Rexona Men 50ml', 'SKU-063', 'Deodoran roll-on pria kemasan 50ml', 15000.00, 21000.00, 150, 15, 'pcs', 1, NOW(), NOW()),
                (64, {$companyId}, 7, 'Sabun Cuci Muka Pond\'s 100g', 'SKU-064', 'Sabun wajah pembersih kemasan 100g', 14000.00, 20000.00, 150, 15, 'pcs', 1, NOW(), NOW()),
                (65, {$companyId}, 7, 'Minyak Kayu Putih Cap Lang 60ml', 'SKU-065', 'Minyak kayu putih aromaterapi 60ml', 18000.00, 25000.00, 200, 20, 'pcs', 1, NOW(), NOW()),
                (66, {$companyId}, 7, 'Bedak Bayi Johnson 200g', 'SKU-066', 'Bedak tabur untuk bayi kemasan 200g', 22000.00, 30000.00, 120, 15, 'pcs', 1, NOW(), NOW()),
                (67, {$companyId}, 7, 'Sikat Gigi Formula Medium', 'SKU-067', 'Sikat gigi bulu medium untuk dewasa', 7000.00, 11000.00, 200, 20, 'pcs', 1, NOW(), NOW()),
                (68, {$companyId}, 7, 'Kapas Kecantikan 100 lembar', 'SKU-068', 'Kapas bersih untuk perawatan wajah', 8000.00, 12000.00, 150, 15, 'pcs', 1, NOW(), NOW()),
                (69, {$companyId}, 7, 'Hansaplast Strip 10 pcs', 'SKU-069', 'Plester luka steril isi 10 buah', 8000.00, 12000.00, 200, 20, 'box', 1, NOW(), NOW()),
                (70, {$companyId}, 7, 'Kondisioner Sunsilk Hitam Berkilau 170ml', 'SKU-070', 'Kondisioner rambut hitam berkilau 170ml', 17000.00, 23000.00, 150, 15, 'pcs', 1, NOW(), NOW()),

                -- Perlengkapan Rumah (category_id: 8)
                (71, {$companyId},  8, 'Sabun Cuci Piring Sunlight 400ml', 'SKU-071', 'Sabun cuci piring jeruk nipis 400ml', 10000.00, 14000.00, 200, 20, 'pcs', 1, NOW(), NOW()),
                (72, {$companyId},  8, 'Pewangi Pakaian Molto 800ml', 'SKU-072', 'Pelembut dan pewangi pakaian 800ml', 19000.00, 25000.00, 150, 15, 'pcs', 1, NOW(), NOW()),
                (73, {$companyId},  8, 'Pembersih Lantai Wipol 770ml', 'SKU-073', 'Pembersih lantai aroma karbol 770ml', 13000.00, 18000.00, 150, 15, 'pcs', 1, NOW(), NOW()),
                (74, {$companyId},  8, 'Kantong Plastik Kresek 1 Pack', 'SKU-074', 'Kantong plastik serba guna isi 50 lembar', 7000.00, 10000.00, 300, 30, 'pcs', 1, NOW(), NOW()),
                (75, {$companyId},  8, 'Tisu Paseo 250 lembar', 'SKU-075', 'Tisu meja lembut kemasan 250 lembar', 14000.00, 19000.00, 200, 20, 'pcs', 1, NOW(), NOW()),
                (76, {$companyId},  8, 'Korek Api Gas Tokai', 'SKU-076', 'Korek api gas isi ulang', 6000.00, 9000.00, 300, 30, 'pcs', 1, NOW(), NOW()),
                (77, {$companyId},  8, 'Lilin Madu 6 Batang', 'SKU-077', 'Lilin putih serbaguna isi 6 batang', 5000.00, 8000.00, 200, 20, 'pcs', 1, NOW(), NOW()),
                (78, {$companyId},  8, 'Karet Gelang 1 Ons', 'SKU-078', 'Karet gelang serba guna 1 ons', 3000.00, 5000.00, 300, 30, 'pcs', 1, NOW(), NOW()),
                (79, {$companyId},  8, 'Baterai ABC AA 2 Buah', 'SKU-079', 'Baterai kering AA alkaline isi 2', 9000.00, 13000.00, 200, 20, 'pcs', 1, NOW(), NOW()),
                (80, {$companyId},  8, 'Lampu LED Philips 9W', 'SKU-080', 'Lampu LED hemat energi 9 watt putih', 22000.00, 30000.00, 100, 10, 'pcs', 1, NOW(), NOW()),

                -- Obat-obatan (category_id: 9)
                (81, {$companyId}, 9, 'Paracetamol 500mg 10 Tab', 'SKU-081', 'Obat penurun panas dan pereda nyeri 10 tablet', 5000.00, 8000.00, 300, 30, 'box', 1, NOW(), NOW()),
                (82, {$companyId}, 9, 'Antasida DOEN 10 Tab', 'SKU-082', 'Obat maag dan gangguan lambung 10 tablet', 4000.00, 7000.00, 300, 30, 'box', 1, NOW(), NOW()),
                (83, {$companyId}, 9, 'Bodrex Flu & Batuk', 'SKU-083', 'Obat flu batuk kemasan 10 tablet', 8000.00, 12000.00, 250, 25, 'box', 1, NOW(), NOW()),
                (84, {$companyId}, 9, 'Tolak Angin Cair 15ml', 'SKU-084', 'Jamu herbal untuk masuk angin sachet 15ml', 5000.00, 8000.00, 400, 40, 'pcs', 1, NOW(), NOW()),
                (85, {$companyId}, 9, 'Betadine 30ml', 'SKU-085', 'Antiseptik luka kemasan 30ml', 15000.00, 22000.00, 150, 15, 'pcs', 1, NOW(), NOW()),
                (86, {$companyId}, 9, 'Minyak Angin Fresh Care', 'SKU-086', 'Minyak angin cair roll-on 10ml', 10000.00, 15000.00, 200, 20, 'pcs', 1, NOW(), NOW()),
                (87, {$companyId}, 9, 'Oralit 200ml Sachet', 'SKU-087', 'Larutan oralit untuk diare sachet 200ml', 2500.00, 4000.00, 500, 50, 'pcs', 1, NOW(), NOW()),
                (88, {$companyId}, 9, 'Vitamin C Redoxon 1000mg', 'SKU-088', 'Suplemen vitamin C effervescent 1 tablet', 4000.00, 6500.00, 300, 30, 'pcs', 1, NOW(), NOW()),
                (89, {$companyId}, 9, 'Promag Cair 60ml', 'SKU-089', 'Obat maag cair kemasan 60ml', 13000.00, 18000.00, 200, 20, 'pcs', 1, NOW(), NOW()),
                (90, {$companyId}, 9, 'Counterpain Cool 30g', 'SKU-090', 'Krim pereda nyeri otot kemasan 30g', 20000.00, 28000.00, 150, 15, 'pcs', 1, NOW(), NOW()),

                -- Alat Tulis & Kantor (category_id: 10)
                (91, {$companyId}, 10, 'Ballpoint Pilot G2 Hitam', 'SKU-091', 'Pulpen gel hitam medium tip', 8000.00, 12000.00, 200, 20, 'pcs', 1, NOW(), NOW()),
                (92, {$companyId}, 10, 'Buku Tulis Sidu 58 Lembar', 'SKU-092', 'Buku tulis bergaris 58 lembar', 5000.00, 8000.00, 300, 30, 'pcs', 1, NOW(), NOW()),
                (93, {$companyId}, 10, 'Pensil 2B Faber Castell', 'SKU-093', 'Pensil hitam 2B untuk menulis dan menggambar', 4000.00, 6500.00, 300, 30, 'pcs', 1, NOW(), NOW()),
                (94, {$companyId}, 10, 'Penghapus Steadler Putih', 'SKU-094', 'Penghapus putih bersih untuk pensil', 3000.00, 5000.00, 300, 30, 'pcs', 1, NOW(), NOW()),
                (95, {$companyId}, 10, 'Lem UHU 21g', 'SKU-095', 'Lem serbaguna kemasan tube 21g', 8000.00, 12000.00, 200, 20, 'pcs', 1, NOW(), NOW()),
                (96, {$companyId}, 10, 'Gunting Joyko Medium', 'SKU-096', 'Gunting kantor serbaguna ukuran medium', 12000.00, 18000.00, 100, 10, 'pcs', 1, NOW(), NOW()),
                (97, {$companyId}, 10, 'Selotip Bening 1 inch', 'SKU-097', 'Selotip transparan lebar 1 inch x 25m', 8000.00, 12000.00, 200, 20, 'pcs', 1, NOW(), NOW()),
                (98, {$companyId}, 10, 'Map Plastik Mika A4', 'SKU-098', 'Map plastik transparan ukuran A4', 3000.00, 5000.00, 300, 30, 'pcs', 1, NOW(), NOW()),
                (99, {$companyId}, 10, 'Stapler Max HD-10', 'SKU-099', 'Stapler kecil untuk kertas 10-20 lembar', 22000.00, 32000.00, 80, 10, 'pcs', 1, NOW(), NOW()),
                (100, {$companyId}, 10, 'Isi Stapler No.10 1000 pcs', 'SKU-100', 'Isi stapler no.10 kemasan 1000 buah', 5000.00, 8000.00, 200, 20, 'box', 1, NOW(), NOW());
            ");

            DB::commit();
        } catch (\Throwable $e) {

            DB::rollBack();

            throw $e;
        }
    }
}
