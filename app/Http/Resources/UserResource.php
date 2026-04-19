<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id'             => $this->id,
      'name'           => $this->name,
      'username'       => $this->username,
      'email'          => $this->email,
      'phone'          => $this->phone,
      'type'           => $this->type,
      'role_id'        => $this->role_id,
      'is_active'      => $this->is_active,
      'company_id'     => $this->company_id,
      'email_verified' => $this->hasVerifiedEmail(),
      'dynamic_role'   => new RoleResource($this->whenLoaded('dynamicRole')),
      'branches'       => BranchResource::collection($this->whenLoaded('branches')),
      'created_at'     => $this->created_at,
    ];
  }
}
