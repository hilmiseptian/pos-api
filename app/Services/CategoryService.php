<?php

namespace App\Services;

use App\Repositories\CategoryRepository;

class CategoryService
{
  public function __construct(
    protected CategoryRepository $categoryRepository
  ) {}

  public function list()
  {
    return $this->categoryRepository->paginate();
  }

  public function detail(int $id)
  {
    return $this->categoryRepository->findById($id);
  }

  public function create(array $data)
  {
    return $this->categoryRepository->create($data);
  }

  public function update(int $id, array $data)
  {
    return $this->categoryRepository->update($id, $data);
  }

  public function delete(int $id)
  {
    $this->categoryRepository->delete($id);
  }
}
