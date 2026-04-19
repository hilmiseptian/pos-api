<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id'         => $this->id,
      'name'       => $this->name,
      'code'       => $this->code,
      'city'       => $this->city,
      'address'    => $this->address,
      'is_active'  => $this->is_active,
      'company_id' => $this->company_id,
      'created_at' => $this->created_at,
    ];
  }
}
