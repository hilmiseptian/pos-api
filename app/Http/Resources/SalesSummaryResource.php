<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalesSummaryResource extends JsonResource
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
      'created_at'      => $this->created_at,
      'cashier'         => $this->whenLoaded('cashier', fn() => [
        'id'   => $this->cashier->id,
        'name' => $this->cashier->name,
      ]),
      'payment' => $this->whenLoaded('payment', fn() => [
        'payment_method' => $this->payment?->payment_method,
        'paid_at'        => $this->payment?->paid_at,
      ]),
    ];
  }
}
