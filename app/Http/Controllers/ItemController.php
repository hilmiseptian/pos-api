<?php

namespace App\Http\Controllers;

use App\Http\Resources\ItemResource;
use App\Services\ItemService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ItemController extends Controller
{
  use ApiResponse;

  public function __construct(
    protected ItemService $itemService
  ) {}

  public function index()
  {
    $items = $this->itemService->list();

    return $this->respondWithList(
      ItemResource::collection($items)
    );
  }

  public function all()
  {
    $items = $this->itemService->listAll();

    return $this->respondWithList(
      ItemResource::collection($items)
    );
  }

  public function show(int $id)
  {
    $item = $this->itemService->show($id);

    return $this->respondWithItem(
      new ItemResource($item)
    );
  }

  public function store(Request $request)
  {
    $data = $request->validate([
      'category_id'   => 'required|exists:categories,id',
      'name'          => 'required|string',
      'sku'           => 'required|string|unique:items',
      'selling_price' => 'required|numeric',
      'cost_price'    => 'nullable|numeric',
      'stock'         => 'integer',
      'unit'          => 'string',
      'is_active'     => 'boolean',
    ]);

    $item = $this->itemService->store($data);
    $item->loadMissing('category');

    return $this->respondWithItem(
      new ItemResource($item),
      'Item created successfully',
      201
    );
  }

  public function update(Request $request, int $id)
  {
    $data = $request->validate([
      'category_id'   => 'exists:categories,id',
      'name'          => 'string',
      'sku'           => 'string',
      'selling_price' => 'numeric',
      'cost_price'    => 'numeric',
      'stock'         => 'integer',
      'unit'          => 'string',
      'is_active'     => 'boolean',
    ]);

    $item = $this->itemService->update($id, $data);
    $item->loadMissing('category');

    return $this->respondWithItem(
      new ItemResource($item),
      'Item updated successfully'
    );
  }

  public function destroy(int $id)
  {
    $this->itemService->destroy($id);

    return $this->respondWithMessage('Item deleted successfully');
  }
}