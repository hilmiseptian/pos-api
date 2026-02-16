<?php

namespace App\Repositories;

use App\Models\Category;

class CategoryRepository
{
  public function paginate(int $perPage = 10)
  {
    return Category::paginate($perPage);
  }

  public function getAll()
  {
    return Category::orderBy('sort_order')->get();
  }

  public function findById(int $id)
  {
    return Category::findOrFail($id);
  }

  public function create(array $data)
  {
    return Category::create($data);
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
