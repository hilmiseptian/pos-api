<?php

namespace App\Http\Controllers;

use App\Http\Resources\ShiftResource;
use App\Services\ShiftService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
  use ApiResponse;

  public function __construct(protected ShiftService $service) {}

  /** GET /shifts/active — active shift for caller's primary branch */
  public function active(Request $request)
  {
    $branchId = $this->resolveBranchId($request);
    $shift    = $this->service->active($branchId);

    return $shift
      ? $this->respondWithItem(new ShiftResource($shift->load('branch', 'user')))
      : response()->json(['data' => null]);
  }

  /** GET /shifts/today — all shifts today for caller's branch */
  public function today(Request $request)
  {
    $branchId = $this->resolveBranchId($request);
    $shifts   = $this->service->today($branchId);

    return $this->respondWithList(
      ShiftResource::collection($shifts->load('branch', 'user'))
    );
  }

  /** POST /shifts/open */
  public function open(Request $request)
  {
    $branchId = $this->resolveBranchId($request);

    if ($this->service->active($branchId)) {
      return response()->json(['message' => 'A shift is already open for this branch.'], 422);
    }

    $shift = $this->service->open($branchId, $request->user()->id);

    return $this->respondWithItem(
      new ShiftResource($shift->load('branch', 'user')),
      'Shift opened successfully',
      201
    );
  }

  /** PATCH /shifts/{id}/close */
  public function close(int $id)
  {
    $shift = $this->service->close($id);

    return $this->respondWithItem(
      new ShiftResource($shift->load('branch', 'user')),
      'Shift closed successfully'
    );
  }

  private function resolveBranchId(Request $request): int
  {
    $branchId = $request->query('branch_id');

    if ($branchId) return (int) $branchId;

    $user = $request->user();

    // if ($user->isOwner() || $user->isSuperAdmin()) {
    //   abort(400, 'branch_id is required for owner/superadmin.');
    // }

    $branch = $user->primaryBranch();

    if (!$branch) abort(403, 'No branch assigned.');

    return $branch->id;
  }
}
