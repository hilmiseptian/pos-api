<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
  use ApiResponse;

  public function __construct(
    protected CategoryService $categoryService
  ) {}

  public function index()
  {
    $categories = $this->categoryService->list();

    return $this->respondWithList(
      CategoryResource::collection($categories)
    );
  }

  public function all()
  {
    $categories = $this->categoryService->listAll();

    return $this->respondWithList(
      CategoryResource::collection($categories)
    );
  }

  public function show($id)
  {
    $category = $this->categoryService->find($id);

    return $this->respondWithItem(
      new CategoryResource($category)
    );
  }

  public function store(Request $request)
  {
    $companyId     = auth()->user()->company_id;
    $userBranchIds = $this->getAccessibleBranchIds();

    $data = $request->validate([
      'name'         => 'required|string|max:255',
      'is_active'    => 'boolean',
      'sort_order'   => 'integer|min:0',
      'branch_ids'   => 'required|array|min:1',
      'branch_ids.*' => [
        'integer',
        Rule::exists('branches', 'id')->where('company_id', $companyId),
        Rule::in($userBranchIds),
      ],
    ]);

    $data['company_id'] = $companyId;

    $category = $this->categoryService->create($data);

    return $this->respondWithItem(
      new CategoryResource($category),
      'Category created successfully',
      201
    );
  }

  public function update(Request $request, $id)
  {
    $companyId     = auth()->user()->company_id;
    $userBranchIds = $this->getAccessibleBranchIds();
    $category      = $this->categoryService->find($id);

    $data = $request->validate([
      'name'         => 'required|string|max:255',
      'is_active'    => 'boolean',
      'sort_order'   => 'integer|min:0',
      'branch_ids'   => 'required|array|min:1',
      'branch_ids.*' => [
        'integer',
        Rule::exists('branches', 'id')->where('company_id', $companyId),
        Rule::in($userBranchIds),
      ],
    ]);

    $category = $this->categoryService->update($category, $data);

    return $this->respondWithItem(
      new CategoryResource($category),
      'Category updated successfully'
    );
  }

  public function destroy($id)
  {
    $category = $this->categoryService->find($id);
    $this->categoryService->delete($category);

    return $this->respondWithMessage('Category deleted successfully');
  }

  private function getAccessibleBranchIds(): array
  {
    $user = auth()->user();

    if ($user->isOwner() || $user->isSuperAdmin()) {
      return \App\Models\Branch::where('company_id', $user->company_id)
        ->pluck('id')
        ->toArray();
    }

    return $user->branches()->pluck('branches.id')->toArray();
  }
}