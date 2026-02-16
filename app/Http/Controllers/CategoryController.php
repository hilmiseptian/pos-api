<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
  public function __construct(
    protected CategoryService $categoryService
  ) {}

  public function index()
  {
    return response()->json(
      $this->categoryService->list()
    );
  }

  public function store(Request $request)
  {
    $data = $request->validate([
      'name' => 'required|string|max:255',
      'code' => 'required|string|max:100|unique:categories,code',
      'is_active' => 'boolean',
      'sort_order' => 'integer',
    ]);

    $category = $this->categoryService->create($data);

    return response()->json([
      'message' => 'Category created successfully',
      'data' => $category
    ], 201);
  }

  public function show(int $id)
  {
    return response()->json([
      'data' => $this->categoryService->detail($id)
    ]);
  }

  public function update(Request $request, int $id)
  {
    $data = $request->validate([
      'name' => 'required|string|max:255',
      'code' => 'required|string|max:100|unique:categories,code,' . $id,
      'is_active' => 'boolean',
      'sort_order' => 'integer',
    ]);

    $category = $this->categoryService->update($id, $data);

    return response()->json([
      'message' => 'Category updated successfully',
      'data' => $category
    ]);
  }

  public function destroy(int $id)
  {
    $this->categoryService->delete($id);

    return response()->json([
      'message' => 'Category deleted successfully'
    ]);
  }
}
