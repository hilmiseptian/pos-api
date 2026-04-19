<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'sales_number'    => $this->sales_number,
            'status'          => $this->status,
            'total_amount'    => $this->total_amount,
            'discount_amount' => $this->discount_amount,
            'grand_total'     => $this->grand_total,
            'notes'           => $this->notes,
            'cashier_id'      => $this->cashier_id,
            'branch_id'       => $this->branch_id,
            'cashier'         => new UserResource($this->whenLoaded('cashier')),
            'branch'          => new BranchResource($this->whenLoaded('branch')),
            'details'         => OrderDetailResource::collection(
                $this->whenLoaded('details')
            ),
            'payment'         => new OrderPaymentResource(
                $this->whenLoaded('payment')
            ),
            'created_at'      => $this->created_at,
        ];
    }
}