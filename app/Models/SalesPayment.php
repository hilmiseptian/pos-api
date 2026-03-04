<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesPayment extends Model
{
    use HasFactory;

    protected $table = 'sales_payment';

    protected $fillable = [
        'sales_head_id',
        'payment_method',
        'amount_paid',
        'change_amount',
        'paid_at',
    ];

    protected $casts = [
        'amount_paid'   => 'decimal:2',
        'change_amount' => 'decimal:2',
        'paid_at'       => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function salesHead()
    {
        return $this->belongsTo(SalesHead::class, 'sales_head_id');
    }
}
