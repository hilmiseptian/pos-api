<?php

namespace App\Http\Controllers;

use App\Http\Resources\BranchResource;
use App\Services\BranchService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class BranchController extends Controller
{
  use ApiResponse;

  public function __construct(
    protected BranchService $service
  ) {}

  public function index()
  {
    $branches = $this->service->list();

    return $this->respondWithList(
      BranchResource::collection($branches)
    );
  }

  public function store(Request $request)
  {
    $data = $request->validate([
      'name'      => 'required|string|max:255',
      'city'      => 'nullable|string|max:255',
      'address'   => 'nullable|string',
      'is_active' => 'boolean',
    ]);

    $branch = $this->service->create($data);

    return $this->respondWithItem(
      new BranchResource($branch),
      'Branch created successfully',
      201
    );
  }

  public function show($id)
  {
    $branch = $this->service->find($id);

    return $this->respondWithItem(
      new BranchResource($branch)
    );
  }

  public function update(Request $request, $id)
  {
    $data = $request->validate([
      'name'      => 'required|string|max:255',
      'city'      => 'nullable|string|max:255',
      'address'   => 'nullable|string',
      'is_active' => 'boolean',
    ]);

    $branch = $this->service->update($id, $data);

    return $this->respondWithItem(
      new BranchResource($branch),
      'Branch updated successfully'
    );
  }

  public function destroy($id)
  {
    $this->service->delete($id);

    return $this->respondWithMessage('Branch deleted successfully');
  }
}