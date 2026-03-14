<?php

namespace App\Services;

use App\Models\SubCategory;
use App\Repositories\SubCategoryRepository;

class SubCategoryService
{
  public function __construct(
    protected SubCategoryRepository $subCategoryRepository
  ) {}

  public function list()
  {
    return $this->subCategoryRepository->paginate();
  }

  public function detail(int $id)
  {
    return $this->subCategoryRepository->findById($id);
  }

  public function create(array $data)
  {
    return $this->subCategoryRepository->create($data);
  }

  public function update(int $id, array $data)
  {
    return $this->subCategoryRepository->update($id, $data);
  }

  public function delete(SubCategory $subCategory): bool
  {
    return $this->subCategoryRepository->delete($subCategory);
  }
}
