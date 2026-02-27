<?php

namespace App\Repositories;

use App\Models\SubCategory;

class SubCategoryRepository
{
  public function paginate(int $perPage = 10)
  {
    return SubCategory::with('category')->paginate($perPage);
  }

  public function getAll()
  {
    return SubCategory::get();
  }

  public function findById(int $id)
  {
    return SubCategory::with('category')->findOrFail($id);
  }

  public function create(array $data)
  {
    return SubCategory::create($data);
  }

  public function update(int $id, array $data)
  {
    $category = $this->findById($id);
    $category->update($data);
    return $category;
  }

  public function delete(int $id)
  {
    $category = $this->findById($id);
    $category->delete();
  }
}
