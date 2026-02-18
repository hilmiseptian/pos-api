<?php

// app/Models/Company.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
  protected $fillable = [
    'name',
    'code',
    'email',
    'phone',
    'address',
    'logo',
    'is_active',
  ];

  protected $casts = [
    'is_active' => 'boolean',
  ];

  public function users()
  {
    return $this->hasMany(User::class);
  }

  public function categories()
  {
    return $this->hasMany(Category::class);
  }

  public function items()
  {
    return $this->hasMany(Item::class);
  }
}
