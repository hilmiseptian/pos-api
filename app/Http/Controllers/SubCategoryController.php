<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\SubCategoryService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubCategoryController extends Controller
{
    public function __construct(
        protected SubCategoryService $categoryService
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
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')
                    ->where('company_id', auth()->user()->company_id), // ✅ must belong to same company
            ],
            'name'      => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $data['company_id'] = auth()->user()->company_id; // ✅ auto-set company

        $category = $this->categoryService->create($data);

        return response()->json([
            'message' => 'Sub category created successfully',
            'data'    => $category,
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
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $category = $this->categoryService->update($id, $data);

        return response()->json([
            'message' => 'Sub Category updated successfully',
            'data' => $category
        ]);
    }

    public function destroy(int $id)
    {
        $category = $this->categoryService->detail($id);
        $this->categoryService->delete($category);

        return response()->json([
            'message' => 'Sub Category deleted successfully'
        ]);
    }
}
