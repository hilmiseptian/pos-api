<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
  use HasFactory, CompanyScope;

  protected $fillable = [
    'company_id',
    'category_id',
    'name',
    'sku',
    'description',
    'cost_price',
    'selling_price',
    'stock',
    'min_stock',
    'unit',
    'is_active',
  ];

  protected $casts = [
    'cost_price'    => 'decimal:2',
    'selling_price' => 'decimal:2',
    'is_active'     => 'boolean',
  ];

  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  public function category()
  {
    return $this->belongsTo(Category::class);
  }
}