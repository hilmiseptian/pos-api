<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
  protected $fillable = [
    'branch_id',
    'user_id',
    'batch',
    'date',
    'open_time',
    'close_time',
    'status',
  ];

  protected $casts = [
    'date'       => 'date',
    'open_time'  => 'datetime',
    'close_time' => 'datetime',
  ];

  public function branch()
  {
    return $this->belongsTo(Branch::class);
  }
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public static function generateBatch(int $branchId): string
  {
    $date   = now()->format('Ymd');
    $prefix = "SHIFT-{$date}-";

    $count = self::where('batch', 'like', $prefix . '%')
      ->where('branch_id', $branchId)
      ->lockForUpdate()
      ->count();

    return $prefix . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
  }
}
