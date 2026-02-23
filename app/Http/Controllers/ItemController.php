<?php

// app/Http/Controllers/ItemController.php
namespace App\Http\Controllers;

use App\Services\ItemService;
use Illuminate\Http\Request;

class ItemController extends Controller
{
  public function __construct(
    protected ItemService $itemService
  ) {}

  public function index()
  {
    return response()->json(
      $this->itemService->list()
    );
  }

  public function store(Request $request)
  {
    $data = $request->validate([
      'category_id' => 'required|exists:categories,id',
      'name' => 'required|string',
      'sku' => 'required|string|unique:items',
      'selling_price' => 'required|numeric',
      'cost_price' => 'nullable|numeric',
      'stock' => 'integer',
      'unit' => 'string',
      'is_active' => 'boolean',
    ]);

    return response()->json([
      'message' => 'Item created',
      'data' => $this->itemService->store($data),
    ], 201);
  }


  public function show(int $id)
  {
    return response()->json(
      $this->itemService->show($id)
    );
  }

  public function update(Request $request, int $id)
  {
    $data = $request->validate([
      'category_id' => 'exists:categories,id',
      'name' => 'string',
      'sku' => 'string',
      'selling_price' => 'numeric',
      'cost_price' => 'numeric',
      'stock' => 'integer',
      'unit' => 'string',
      'is_active' => 'boolean',
    ]);

    return response()->json([
      'message' => 'Item updated',
      'data' => $this->itemService->update($id, $data),
    ]);
  }


  public function destroy(int $id)
  {
    $this->itemService->destroy($id);

    return response()->json([
      'message' => 'Item deleted',
    ]);
  }
}
