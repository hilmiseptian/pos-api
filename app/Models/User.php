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
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

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

    /**
     * All branches this user is assigned to (admin / cashier via pivot).
     */
    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'branch_user')
            ->withTimestamps();
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

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
     * First assigned branch (used for cashier single-branch context).
     */
    public function primaryBranch(): ?Branch
    {
        return $this->branches()->first();
    }
}