<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;

class UserService
{
  public function __construct(
    protected UserRepository $userRepository
  ) {}

  public function list()
  {
    return $this->userRepository->paginate();
  }

  public function find(int $id): User
  {
    return $this->userRepository->findById($id);
  }

  public function create(array $data): User
  {
    $branchIds = $data['branch_ids'] ?? [];
    return $this->userRepository->create($data, $branchIds);
  }

  public function update(User $user, array $data): User
  {
    $branchIds = $data['branch_ids'] ?? [];
    return $this->userRepository->update($user, $data, $branchIds);
  }

  public function delete(User $user): bool
  {
    return $this->userRepository->delete($user);
  }
}
