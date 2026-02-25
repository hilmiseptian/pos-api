<?php

// app/Repositories/BranchRepository.php

namespace App\Repositories;

use App\Models\Branch;

class BranchRepository
{
  public function paginate(int $perPage = 10)
  {
    return Branch::with('company')->paginate($perPage);
  }

  public function find(int $id): Branch
  {
    return Branch::with('company')->findOrFail($id);
  }

  public function create(array $data): Branch
  {
    return Branch::create($data);
  }

  public function update(Branch $branch, array $data): Branch
  {
    $branch->update($data);
    return $branch;
  }

  public function delete(Branch $branch): bool
  {
    return $branch->delete();
  }
}
