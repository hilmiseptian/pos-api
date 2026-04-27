<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Item;
use App\Models\Role;
use App\Models\SubCategory;
use App\Models\User;

class DashboardService
{
  public function getSummary(): array
  {
    return [
      'categories'    => Category::count(),
      'subcategories' => SubCategory::count(),
      'branches'      => Branch::count(),
      'items'         => Item::count(),
      'roles'         => Role::where('company_id', auth()->user()->company_id)->count(),
      'users'         => User::where('company_id', auth()->user()->company_id)
        ->where('type', 'staff')
        ->count(),
    ];
  }
}
