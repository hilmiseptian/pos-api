<?php

namespace App\Http\Controllers;

use App\Http\Resources\SubCategoryResource;
use App\Services\SubCategoryService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubCategoryController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected SubCategoryService $categoryService
    ) {}

    public function index()
    {
        return $this->respondWithList(
            SubCategoryResource::collection(
                $this->categoryService->list()
            )
        );
    }

    public function show(int $id)
    {
        return $this->respondWithItem(
            new SubCategoryResource($this->categoryService->detail($id))
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')
                    ->where('company_id', auth()->user()->company_id),
            ],
            'name'      => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $data['company_id'] = auth()->user()->company_id;

        $subCategory = $this->categoryService->create($data);

        return $this->respondWithItem(
            new SubCategoryResource($subCategory),
            'Sub category created successfully',
            201
        );
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:255',
            'is_active'   => 'boolean',
        ]);

        $subCategory = $this->categoryService->update($id, $data);

        return $this->respondWithItem(
            new SubCategoryResource($subCategory),
            'Sub category updated successfully'
        );
    }

    public function destroy(int $id)
    {
        $subCategory = $this->categoryService->detail($id);
        $this->categoryService->delete($subCategory);

        return $this->respondWithMessage('Sub category deleted successfully');
    }
}