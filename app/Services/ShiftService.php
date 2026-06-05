<?php

namespace App\Services;

use App\Models\Shift;
use App\Repositories\ShiftRepository;

class ShiftService
{
  public function __construct(protected ShiftRepository $repo) {}

  public function active(int $branchId): ?Shift
  {
    return $this->repo->activeForBranch($branchId);
  }

  public function today(int $branchId)
  {
    return $this->repo->todayForBranch($branchId);
  }

  public function open(int $branchId, int $userId): Shift
  {
    return $this->repo->open($branchId, $userId);
  }

  public function close(int $shiftId): Shift
  {
    $shift = Shift::findOrFail($shiftId);

    if ($shift->status === 'closed') {
      abort(422, 'Shift is already closed.');
    }

    return $this->repo->close($shift);
  }
}
