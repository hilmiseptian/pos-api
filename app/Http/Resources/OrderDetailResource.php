<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'sales_head_id'   => $this->sales_head_id,
            'item_id'         => $this->item_id,
            'qty'             => $this->qty,
            'unit_price'      => $this->unit_price,
            'discount_amount' => $this->discount_amount,
            'subtotal'        => $this->subtotal,
            'notes'           => $this->notes,
            'item'            => new ItemResource($this->whenLoaded('item')),
            'created_at'      => $this->created_at,
        ];
    }
}