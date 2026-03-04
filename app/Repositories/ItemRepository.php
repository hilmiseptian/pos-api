<?php

// app/Repositories/ItemRepository.php
namespace App\Repositories;

use App\Models\Item;

class ItemRepository
{
  public function getAll()
  {
    return Item::with('category')
      ->where('is_active', 1)
      ->orderBy('name')
      ->get();
  }

  public function paginate(int $perPage = 10)
  {
    return Item::with('category')->paginate($perPage);
  }

  public function find(int $id): Item
  {
    return Item::with('category')->findOrFail($id);
  }

  public function create(array $data): Item
  {
    return Item::create($data);
  }

  public function update(Item $item, array $data): Item
  {
    $item->update($data);
    return $item;
  }

  public function delete(Item $item): bool
  {
    return $item->delete();
  }
}
