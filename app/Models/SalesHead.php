<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesHead extends Model
{
    use HasFactory;

    protected $table = 'sales_head';

    protected $fillable = [
        'cashier_id',
        'sales_number',
        'status',
        'total_amount',
        'discount_amount',
        'grand_total',
        'notes',
    ];

    protected $casts = [
        'total_amount'    => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'grand_total'     => 'decimal:2',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function details()
    {
        return $this->hasMany(SalesDetail::class, 'sales_head_id');
    }

    public function payment()
    {
        return $this->hasOne(SalesPayment::class, 'sales_head_id');
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    public static function generateSalesNumber(): string
    {
        $date   = now()->format('Ymd');
        $prefix = "ORD-{$date}-";

        $last = self::where('sales_number', 'like', $prefix . '%')
            ->orderBy('sales_number', 'desc')
            ->lockForUpdate()
            ->first();

        $next = $last
            ? (int) substr($last->sales_number, -3) + 1
            : 1;

        return $prefix . str_pad($next, 3, '0', STR_PAD_LEFT);
    }
}
