<?php

// app/Repositories/ItemRepository.php
namespace App\Repositories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Builder;

class ItemRepository
{
  public function getAll()
  {
    return $this->withBranchStock()
      ->where('is_active', 1)
      ->orderBy('name')
      ->get();
  }

  public function paginate(int $perPage = 10)
  {
    return $this->withBranchStock()->paginate($perPage);
  }

  public function find(int $id): Item
  {
    return $this->withBranchStock()->where('items.id', $id)->firstOrFail();
  }

  public function create(array $data): Item
  {
    $item = Item::create($data);
    return $this->find($item->id);
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

  private function withBranchStock(): Builder
  {
    $query = Item::query()->with('category')->select('items.*');
    $branchId = auth()->user()?->primaryBranch()?->id;

    if (!$branchId) {
      return $query->selectRaw('0 as branch_stock');
    }

    return $query
      ->leftJoin('stock_period', function ($join) use ($branchId) {
        $join->on('stock_period.item_id', '=', 'items.id')
          ->on('stock_period.company_id', '=', 'items.company_id')
          ->where('stock_period.branch_id', '=', $branchId);
      })
      ->addSelect(\Illuminate\Support\Facades\DB::raw('COALESCE(stock_period.qty, 0) as branch_stock'));
  }
}
