<?php

// app/Services/ItemService.php
namespace App\Services;

use App\Repositories\ItemRepository;

class ItemService
{
  public function __construct(
    protected ItemRepository $itemRepository
  ) {}

  public function list()
  {
    return $this->itemRepository->paginate();
  }

  public function listAll()
  {
    return $this->itemRepository->getAll();
  }

  public function show(int $id)
  {
    return $this->itemRepository->find($id);
  }

  public function store(array $data)
  {
    return $this->itemRepository->create($data);
  }

  public function update(int $id, array $data)
  {
    $item = $this->itemRepository->find($id);
    return $this->itemRepository->update($item, $data);
  }

  public function destroy(int $id)
  {
    $item = $this->itemRepository->find($id);
    return $this->itemRepository->delete($item);
  }
}
