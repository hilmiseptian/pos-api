<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderPaymentResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id'             => $this->id,
      'sales_head_id'  => $this->sales_head_id,
      'payment_method' => $this->payment_method,
      'amount_paid'    => $this->amount_paid,
      'change_amount'  => $this->change_amount,
      'paid_at'        => $this->paid_at,
      'created_at'     => $this->created_at,
    ];
  }
}
