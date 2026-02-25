<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\BranchService;
use Illuminate\Http\Request;

class BranchController extends Controller
{
  public function __construct(
    protected BranchService $service
  ) {}

  public function index()
  {
    return response()->json([
      'data' => $this->service->list()
    ]);
  }

  public function store(Request $request)
  {
    $data = $request->validate([
      'company_id' => 'required|exists:companies,id',
      'name' => 'required|string',
      'code' => 'required|string|unique:branches,code',
      'address' => 'nullable|string',
      'is_active' => 'boolean',
    ]);

    $branch = $this->service->create($data);

    return response()->json([
      'message' => 'Branch created successfully',
      'data' => $branch
    ]);
  }

  public function show($id)
  {
    return response()->json([
      'data' => $this->service->find($id)
    ]);
  }

  public function update(Request $request, $id)
  {
    $data = $request->validate([
      'company_id' => 'required|exists:companies,id',
      'name' => 'required|string',
      'code' => 'required|string|unique:branches,code,' . $id,
      'address' => 'nullable|string',
      'is_active' => 'boolean',
    ]);

    $branch = $this->service->update($id, $data);

    return response()->json([
      'message' => 'Branch updated successfully',
      'data' => $branch
    ]);
  }

  public function destroy($id)
  {
    $this->service->delete($id);

    return response()->json([
      'message' => 'Branch deleted successfully'
    ]);
  }
}
