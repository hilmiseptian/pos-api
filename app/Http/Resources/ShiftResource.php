<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShiftResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id'         => $this->id,
      'batch'      => $this->batch,
      'branch_id'  => $this->branch_id,
      'user_id'    => $this->user_id,
      'date'       => $this->date->toDateString(),
      'open_time'  => $this->open_time,
      'close_time' => $this->close_time,
      'status'     => $this->status,
      'branch'     => new BranchResource($this->whenLoaded('branch')),
      'user'       => new UserResource($this->whenLoaded('user')),
    ];
  }
}
