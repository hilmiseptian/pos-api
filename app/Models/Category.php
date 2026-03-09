<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
  use HasFactory, CompanyScope;

  protected $fillable = [
    'company_id',
    'name',
    'code',
    'is_active',
    'sort_order',
  ];

  protected $casts = [
    'is_active'  => 'boolean',
    'sort_order' => 'integer',
  ];

  // ── Relationships ──────────────────────────────────────────────────────────

  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  public function branches()
  {
    return $this->belongsToMany(Branch::class, 'branch_category')
      ->withTimestamps();
  }

  public function subCategories()
  {
    return $this->hasMany(SubCategory::class);
  }

  public function items()
  {
    return $this->hasMany(Item::class);
  }
}