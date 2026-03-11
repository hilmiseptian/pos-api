<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Permissions are seeded once and never hardcoded in app logic.
     * Format: module.action
     */
    private array $permissions = [
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

    public function run(): void
    {
        foreach ($this->permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }
    }
}