<?php

namespace App\Services;

use App\Models\SalesDetail;
use App\Models\SalesHead;
use App\Models\SalesPayment;
use Illuminate\Support\Facades\DB;

class ReportService
{
    // ── Shared query base ──────────────────────────────────────────────────────

    private function baseQuery(array $filters)
    {
        return SalesHead::query()
            ->where('status', 'paid')
            ->whereBetween('created_at', [
                $filters['date_from'] . ' 00:00:00',
                $filters['date_to']   . ' 23:59:59',
            ])
            ->when(
                isset($filters['branch_id']),
                fn($q) => $q->where('branch_id', $filters['branch_id'])
            )
            ->when(
                isset($filters['cashier_id']),
                fn($q) => $q->where('cashier_id', $filters['cashier_id'])
            );
    }

    // ── 1. Daily Summary ───────────────────────────────────────────────────────

    public function getSalesSummary(array $filters): array
    {
        $query = SalesHead::query()
            ->with(['cashier', 'payment'])
            ->whereBetween('created_at', [
                $filters['date_from'] . ' 00:00:00',
                $filters['date_to']   . ' 23:59:59',
            ])
            ->when(
                isset($filters['branch_id']),
                fn($q) => $q->where('branch_id', $filters['branch_id'])
            )
            ->when(
                isset($filters['status']),
                fn($q) => $q->where('status', $filters['status'])
            )
            ->orderByDesc('created_at');

        $rows = $query->get();

        return [
            'rows'           => $rows,
            'total_amount'   => $rows->sum('total_amount'),
            'total_discount' => $rows->sum('discount_amount'),
            'total_grand'    => $rows->sum('grand_total'),
            'total_trx'      => $rows->count(),
        ];
    }

    // ── 2. Sales by Item ───────────────────────────────────────────────────────

    public function getSalesByItem(array $filters): array
    {
        $salesHeadIds = $this->baseQuery($filters)->pluck('id');

        $rows = SalesDetail::query()
            ->whereIn('sales_head_id', $salesHeadIds)
            ->join('items', 'items.id', '=', 'sales_detail.item_id')
            ->selectRaw('
                items.id,
                items.name,
                items.sku,
                SUM(sales_detail.qty)      AS total_qty,
                SUM(sales_detail.subtotal) AS total_revenue
            ')
            ->groupBy('items.id', 'items.name', 'items.sku')
            ->orderByDesc('total_revenue')
            ->get();

        return [
            'rows'          => $rows,
            'total_revenue' => $rows->sum('total_revenue'),
            'total_qty'     => $rows->sum('total_qty'),
        ];
    }

    // ── 3. Payment Breakdown ───────────────────────────────────────────────────

    public function getPaymentBreakdown(array $filters): array
    {
        $salesHeadIds = $this->baseQuery($filters)->pluck('id');

        $rows = SalesPayment::query()
            ->whereIn('sales_head_id', $salesHeadIds)
            ->selectRaw('
                payment_method,
                COUNT(*)           AS total_transactions,
                SUM(amount_paid)   AS total_amount
            ')
            ->groupBy('payment_method')
            ->get();

        return [
            'rows'          => $rows,
            'total_amount'  => $rows->sum('total_amount'),
            'total_trx'     => $rows->sum('total_transactions'),
        ];
    }

    public function getSalesByBranch(array $filters): array
    {
        $rows = SalesHead::query()
            ->where('status', 'paid')
            ->whereBetween('sales_head.created_at', [
                $filters['date_from'] . ' 00:00:00',
                $filters['date_to']   . ' 23:59:59',
            ])
            ->join('branches', 'branches.id', '=', 'sales_head.branch_id')
            ->selectRaw('
            branches.id,
            branches.name,
            branches.city,
            COUNT(sales_head.id)        AS total_transactions,
            SUM(sales_head.grand_total) AS total_revenue,
            AVG(sales_head.grand_total) AS avg_order_value
        ')
            ->groupBy('branches.id', 'branches.name', 'branches.city')
            ->orderByDesc('total_revenue')
            ->get();

        return [
            'rows'          => $rows,
            'total_revenue' => $rows->sum('total_revenue'),
            'total_trx'     => $rows->sum('total_transactions'),
        ];
    }
}
