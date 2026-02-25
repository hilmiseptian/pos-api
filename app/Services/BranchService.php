<?php


namespace App\Services;

use App\Repositories\BranchRepository;

class BranchService
{

  public function __construct(protected BranchRepository $repository) {}

  public function list(int $perPage = 10)
  {
    return $this->repository->paginate($perPage);
  }

  public function find(int $id)
  {
    return $this->repository->find($id);
  }

  public function create(array $data)
  {
    return $this->repository->create($data);
  }

  public function update(int $id, array $data)
  {
    $branch = $this->repository->find($id);
    return $this->repository->update($branch, $data);
  }

  public function delete(int $id)
  {
    $branch = $this->repository->find($id);
    return $this->repository->delete($branch);
  }
}
