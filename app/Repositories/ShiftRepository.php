<?php

namespace App\Repositories;

use App\Models\Shift;

class ShiftRepository
{
  public function activeForBranch(int $branchId): ?Shift
  {
    return Shift::where('branch_id', $branchId)
      ->where('status', 'open')
      ->whereDate('date', today())
      ->latest()
      ->first();
  }

  public function todayForBranch(int $branchId)
  {
    return Shift::where('branch_id', $branchId)
      ->whereDate('date', today())
      ->orderBy('open_time', 'desc')
      ->get();
  }

  public function open(int $branchId, int $userId): Shift
  {
    return \DB::transaction(function () use ($branchId, $userId) {
      return Shift::create([
        'branch_id' => $branchId,
        'user_id'   => $userId,
        'batch'     => Shift::generateBatch($branchId),
        'date'      => today(),
        'open_time' => now(),
        'status'    => 'open',
      ]);
    });
  }

  public function close(Shift $shift): Shift
  {
    $shift->update([
      'status'     => 'closed',
      'close_time' => now(),
    ]);
    return $shift;
  }
}
