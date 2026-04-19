<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'sku'           => $this->sku,
            'description'   => $this->description,
            'cost_price'    => $this->cost_price,
            'selling_price' => $this->selling_price,
            'stock'         => $this->stock,
            'min_stock'     => $this->min_stock,
            'unit'          => $this->unit,
            'is_active'     => $this->is_active,
            'company_id'    => $this->company_id,
            'category_id'   => $this->category_id,
            'category'      => new CategoryResource(
                $this->whenLoaded('category')
            ),
            'created_at'    => $this->created_at,
        ];
    }
}