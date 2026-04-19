<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id'          => $this->id,
      'name'        => $this->name,
      'description' => $this->description,
      'is_active'   => $this->is_active,
      'company_id'  => $this->company_id,
      'permissions' => PermissionResource::collection(
        $this->whenLoaded('permissions')
      ),
      'created_at'  => $this->created_at,
    ];
  }
}
