<?php

namespace App\Repositories;

use App\Models\SalesDetail;
use App\Models\SalesHead;
use App\Models\SalesPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderRepository
{
  // ── SalesHead ──────────────────────────────────────────────────────────────

  public function paginate(int $perPage = 10, $filters = null)
  {
    return SalesHead::with(['cashier', 'details', 'payment'])
      ->whereHas('cashier', function ($q) {
        $q->where('company_id', auth()->user()->company_id);
      })->when(
        isset($filters['branch_id']),
        fn($q) => $q->where('branch_id', $filters['branch_id'])
      )
      ->latest()
      ->paginate($perPage);
  }

  public function getOpen()
  {
    return SalesHead::with(['details'])
      ->where('status', 'open')
      ->latest()
      ->get();
  }

  public function findById(int $id): SalesHead
  {
    return SalesHead::with(['cashier', 'details.item', 'payment'])
      ->findOrFail($id);
  }

  public function create(array $data): SalesHead
  {
    return DB::transaction(function () use ($data) {
      return SalesHead::create([
        'cashier_id'      => $data['cashier_id'],
        'company_id'      => $data['company_id'],
        'branch_id'       => $data['branch_id'],
        'sales_number'    => SalesHead::generateSalesNumber(),
        'status'          => 'open',
        'total_amount'    => 0,
        'discount_amount' => 0,
        'grand_total'     => 0,
        'notes'           => $data['notes'] ?? null,
      ]);
    });
  }

  public function update(int $id, array $data): SalesHead
  {
    $sales = $this->findById($id);
    $sales->update($data);
    return $sales;
  }

  public function cancel(int $id): SalesHead
  {
    return DB::transaction(function () use ($id) {
      $sales = $this->lockOpenOrder($id);
      $sales->update(['status' => 'cancelled']);
      return $sales->fresh(['cashier', 'details', 'payment']);
    });
  }

  // ── SalesDetail ───────────────────────────────────────────────────────────

  public function findDetail(int $detailId): SalesDetail
  {
    return SalesDetail::findOrFail($detailId);
  }

  public function addDetail(int $salesHeadId, array $data): SalesDetail
  {
    return DB::transaction(function () use ($salesHeadId, $data) {
      $sales = $this->lockOpenOrder($salesHeadId);
      $detail = SalesDetail::create([
        'sales_head_id'   => $salesHeadId,
        'item_id'         => $data['item_id'],
        'qty'             => $data['qty'],
        'unit_price'      => $data['unit_price'],
        'discount_amount' => $data['discount_amount'] ?? 0,
        'subtotal'        => $data['subtotal'],
        'notes'           => $data['notes'] ?? null,
      ]);
      $this->recalculateTotalsLocked($sales);
      return $detail;
    });
  }

  public function updateDetail(int $salesHeadId, int $detailId, array $data): SalesDetail
  {
    return DB::transaction(function () use ($salesHeadId, $detailId, $data) {
      $sales = $this->lockOpenOrder($salesHeadId);
      $detail = SalesDetail::where('sales_head_id', $salesHeadId)->findOrFail($detailId);
      $detail->update($data);
      $this->recalculateTotalsLocked($sales);
      return $detail->fresh();
    });
  }

  public function removeDetail(int $salesHeadId, int $detailId): void
  {
    DB::transaction(function () use ($salesHeadId, $detailId) {
      $sales = $this->lockOpenOrder($salesHeadId);
      SalesDetail::where('sales_head_id', $salesHeadId)->findOrFail($detailId)->delete();
      $this->recalculateTotalsLocked($sales);
    });
  }

  // ── SalesPayment ──────────────────────────────────────────────────────────

  public function createPayment(int $salesHeadId, array $data): SalesPayment
  {
    return DB::transaction(function () use ($salesHeadId, $data) {
      $sales = SalesHead::whereKey($salesHeadId)->lockForUpdate()->firstOrFail();
      if ($sales->status !== 'open') {
        throw ValidationException::withMessages(['order' => 'Only open orders can be paid.']);
      }
      if (!$sales->company_id || !$sales->branch_id) {
        throw ValidationException::withMessages(['branch' => 'This order is not assigned to a branch.']);
      }

      $lines = DB::table('sales_detail')
        ->select('item_id')
        ->selectRaw('SUM(qty) as qty')
        ->where('sales_head_id', $salesHeadId)
        ->groupBy('item_id')
        ->orderBy('item_id')
        ->get();
      if ($lines->isEmpty()) {
        throw ValidationException::withMessages(['order' => 'An empty order cannot be paid.']);
      }

      foreach ($lines as $line) {
        DB::statement(
          'INSERT INTO stock_period (company_id, branch_id, item_id, stock_date, qty) VALUES (?, ?, ?, CURRENT_DATE(), 0) ON DUPLICATE KEY UPDATE id=id',
          [$sales->company_id, $sales->branch_id, $line->item_id]
        );
        $stock = DB::table('stock_period')
          ->where('company_id', $sales->company_id)
          ->where('branch_id', $sales->branch_id)
          ->where('item_id', $line->item_id)
          ->lockForUpdate()
          ->first();

        if (!$stock || (int) $stock->qty < (int) $line->qty) {
          throw ValidationException::withMessages([
            'stock' => "Insufficient stock for item {$line->item_id} at this branch.",
          ]);
        }

        $average = DB::table('stock_card')
          ->where('company_id', $sales->company_id)
          ->where('branch_id', $sales->branch_id)
          ->where('item_id', $line->item_id)
          ->selectRaw('COALESCE(ROUND((SUM(in_qty * hpp) - SUM(out_qty * hpp)) / NULLIF(SUM(in_qty) - SUM(out_qty), 0), 4), 0) as average_hpp')
          ->value('average_hpp');

        DB::table('stock_period')
          ->where('id', $stock->id)
          ->update([
            'qty' => DB::raw('qty - ' . (int) $line->qty),
            'stock_date' => now()->toDateString(),
          ]);
        DB::table('stock_card')->insert([
          'company_id' => $sales->company_id,
          'stock_date' => now(),
          'ref_num' => $sales->sales_number,
          'branch_id' => $sales->branch_id,
          'item_id' => $line->item_id,
          'trans_type' => 'pos_sale',
          'in_qty' => 0,
          'out_qty' => (int) $line->qty,
          'hpp' => $average ?? 0,
        ]);
      }

      $changeAmount = $data['payment_method'] === 'cash'
        ? max(0, $data['amount_paid'] - $sales->grand_total)
        : 0;
      $payment = SalesPayment::create([
        'sales_head_id'  => $salesHeadId,
        'payment_method' => $data['payment_method'],
        'amount_paid'    => $data['amount_paid'],
        'change_amount'  => $changeAmount,
        'paid_at'        => now(),
      ]);

      $sales->update(['status' => 'paid']);

      return $payment;
    });
  }

  private function lockOpenOrder(int $salesHeadId): SalesHead
  {
    $sales = SalesHead::whereKey($salesHeadId)->lockForUpdate()->firstOrFail();
    if ($sales->status !== 'open') {
      throw ValidationException::withMessages(['order' => 'Only open orders can be changed.']);
    }
    return $sales;
  }

  private function recalculateTotalsLocked(SalesHead $sales): void
  {
    $totalAmount = (float) SalesDetail::where('sales_head_id', $sales->id)->sum('subtotal');
    $sales->update([
      'total_amount' => $totalAmount,
      'grand_total' => max(0, $totalAmount - (float) $sales->discount_amount),
    ]);
  }
}
