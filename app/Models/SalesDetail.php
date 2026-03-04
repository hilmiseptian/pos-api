<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesDetail extends Model
{
    use HasFactory;

    protected $table = 'sales_detail';

    protected $fillable = [
        'sales_head_id',
        'item_id',
        'qty',
        'unit_price',
        'discount_amount',
        'subtotal',
        'notes',
    ];

    protected $casts = [
        'unit_price'      => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'subtotal'        => 'decimal:2',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function salesHead()
    {
        return $this->belongsTo(SalesHead::class, 'sales_head_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
