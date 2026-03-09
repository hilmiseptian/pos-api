<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryRepository
{
  public function paginate(int $perPage = 10): LengthAwarePaginator
  {
    // CompanyScope auto-applies
    return Category::with('branches')
      ->orderBy('sort_order')
      ->orderBy('name')
      ->paginate($perPage);
  }

  public function getAll()
  {
    return Category::with('branches')
      ->where('is_active', true)
      ->orderBy('sort_order')
      ->orderBy('name')
      ->get();
  }

  public function findById(int $id): Category
  {
    return Category::with('branches')->findOrFail($id);
  }

  public function create(array $data, array $branchIds = []): Category
  {
    $category = Category::create($data);

    if (!empty($branchIds)) {
      $category->branches()->sync($branchIds);
    }

    return $category->load('branches');
  }

  public function update(Category $category, array $data, array $branchIds = []): Category
  {
    $category->update($data);
    $category->branches()->sync($branchIds);
    return $category->load('branches');
  }

  public function delete(Category $category): bool
  {
    $category->branches()->detach();
    return $category->delete();
  }
}