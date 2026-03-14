<?php

namespace App\Repositories;

use App\Models\Branch;

class BranchRepository
{
  public function getAll()
  {
    return Branch::with('company')->orderBy('name')->get();
  }

  public function paginate(int $perPage = 10)
  {
    return Branch::with('company')->orderBy('name')->paginate($perPage);
  }

  public function findById(int $id): Branch
  {
    return Branch::with('company')->findOrFail($id);
  }

  public function create(array $data): Branch
  {
    return Branch::create($data);
  }

  public function update(int $id, array $data): Branch
  {
    $branch = $this->findById($id);
    $branch->update($data);
    return $branch;
  }

  public function delete(int $id): void
  {
    $this->findById($id)->delete();
  }
}