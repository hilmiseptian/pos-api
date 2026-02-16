<?php

// app/Models/Item.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
  protected $fillable = [
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

  public function category()
  {
    return $this->belongsTo(Category::class);
  }
}
