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
      'name'       => 'required|string|max:255',
      'city'       => 'nullable|string|max:255',
      'address'    => 'nullable|string',
      'is_active'  => 'boolean',
      // code is NOT here — generated automatically
    ]);

    $branch = $this->service->create($data);

    return response()->json([
      'message' => 'Branch created successfully',
      'data'    => $branch,
    ], 201);
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
      'name'       => 'required|string|max:255',
      'city'       => 'nullable|string|max:255',
      'address'    => 'nullable|string',
      'is_active'  => 'boolean',
      // code is NOT here — cannot be changed after creation
    ]);

    $branch = $this->service->update($id, $data);

    return response()->json([
      'message' => 'Branch updated successfully',
      'data'    => $branch,
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