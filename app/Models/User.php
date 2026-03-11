<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'password',
        'company_id',
        'role',       // superadmin | owner | admin | cashier (structural)
        'role_id',    // FK → roles table (dynamic permissions)
        'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_active'         => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'branch_user')->withTimestamps();
    }

    /**
     * The dynamic role with permissions (null for superadmin/owner).
     */
    public function dynamicRole()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    // ── Role Checks ────────────────────────────────────────────────────────────

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCashier(): bool
    {
        return $this->role === 'cashier';
    }

    /**
     * Check if user can perform an action by permission slug.
     *
     * - superadmin  → always true (bypasses everything)
     * - owner       → always true within their company
     * - others      → check role_permissions table
     * - no role_id  → always false (deny all)
     */
    public function hasPermission(string $slug): bool
    {
        if ($this->isSuperAdmin() || $this->isOwner()) {
            return true;
        }

        if (!$this->role_id) {
            return false;
        }

        return $this->dynamicRole
            ?->permissions
            ->contains('slug', $slug)
            ?? false;
    }

    /**
     * Get all permission slugs for this user (used in API response).
     */
    public function getPermissions(): array
    {
        if ($this->isSuperAdmin() || $this->isOwner()) {
            return ['*']; // wildcard — frontend grants access to everything
        }

        if (!$this->role_id) {
            return [];
        }

        return $this->dynamicRole
            ?->permissions
            ->pluck('slug')
            ->toArray()
            ?? [];
    }

    public function primaryBranch(): ?Branch
    {
        return $this->branches()->first();
    }
}