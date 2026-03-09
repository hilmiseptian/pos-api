<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
  public function __construct(
    protected CategoryService $categoryService
  ) {}

  public function index()
  {
    return response()->json([
      'data' => $this->categoryService->list()
    ]);
  }

  public function all()
  {
    return response()->json([
      'data' => $this->categoryService->listAll()
    ]);
  }

  public function show($id)
  {
    return response()->json([
      'data' => $this->categoryService->find($id)
    ]);
  }

  public function store(Request $request)
  {
    $companyId     = auth()->user()->company_id;
    $userBranchIds = $this->getAccessibleBranchIds();

    $data = $request->validate([
      'name'         => 'required|string|max:255',
      // code removed — generated automatically
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

    return response()->json([
      'message' => 'Category created successfully',
      'data'    => $category,
    ], 201);
  }

  public function update(Request $request, $id)
  {
    $companyId     = auth()->user()->company_id;
    $userBranchIds = $this->getAccessibleBranchIds();
    $category      = $this->categoryService->find($id);

    $data = $request->validate([
      'name'         => 'required|string|max:255',
      // code removed — immutable after creation
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

    return response()->json([
      'message' => 'Category updated successfully',
      'data'    => $category,
    ]);
  }

  public function destroy($id)
  {
    $category = $this->categoryService->find($id);
    $this->categoryService->delete($category);

    return response()->json([
      'message' => 'Category deleted successfully'
    ]);
  }

  // ── Helper ─────────────────────────────────────────────────────────────────

  private function getAccessibleBranchIds(): array
  {
    $user = auth()->user();

    if ($user->isOwner()) {
      return \App\Models\Branch::where('company_id', $user->company_id)
        ->pluck('id')
        ->toArray();
    }

    return $user->branches()->pluck('branches.id')->toArray();
  }
}