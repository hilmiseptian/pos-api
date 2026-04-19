<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id'         => $this->id,
      'name'       => $this->name,
      'code'       => $this->code,
      'is_active'  => $this->is_active,
      'sort_order' => $this->sort_order,
      'company_id' => $this->company_id,
      'branches'   => BranchResource::collection(
        $this->whenLoaded('branches')
      ),
      'created_at' => $this->created_at,
    ];
  }
}
