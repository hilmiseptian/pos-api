<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
  public function paginate(int $perPage = 10): LengthAwarePaginator
  {
    return User::with(['branches', 'dynamicRole'])
      ->where('company_id', auth()->user()->company_id)
      ->where('type', 'staff')   // only manage staff; owners/superadmins excluded
      ->orderBy('name')
      ->paginate($perPage);
  }

  public function findById(int $id): User
  {
    return User::with(['branches', 'dynamicRole'])
      ->where('company_id', auth()->user()->company_id)
      ->where('type', 'staff')
      ->findOrFail($id);
  }

  public function create(array $data, array $branchIds = []): User
  {
    $user = User::create([
      'name'       => $data['name'],
      'username'   => $data['username'],
      'email'      => $data['email'],
      'phone'      => $data['phone'] ?? null,
      'password'   => Hash::make($data['password']),
      'company_id' => auth()->user()->company_id,
      'type'       => 'staff',
      'role_id'    => $data['role_id'],
      'is_active'  => $data['is_active'] ?? true,
    ]);

    if (!empty($branchIds)) {
      $user->branches()->sync($branchIds);
    }

    return $user->load(['branches', 'dynamicRole']);
  }

  public function update(User $user, array $data, array $branchIds = []): User
  {
    $updateData = [
      'name'      => $data['name'],
      'username'  => $data['username'],
      'email'     => $data['email'],
      'phone'     => $data['phone'] ?? null,
      'role_id'   => $data['role_id'],
      'is_active' => $data['is_active'] ?? $user->is_active,
    ];

    if (!empty($data['password'])) {
      $updateData['password'] = Hash::make($data['password']);
    }

    $user->update($updateData);
    $user->branches()->sync($branchIds);

    return $user->load(['branches', 'dynamicRole']);
  }

  public function delete(User $user): bool
  {
    $user->branches()->detach();
    return $user->delete();
  }
}