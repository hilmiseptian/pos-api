<?php

namespace App\Repositories;

use App\Models\SalesDetail;
use App\Models\SalesHead;
use App\Models\SalesPayment;
use Illuminate\Support\Facades\DB;

class OrderRepository
{
  // ── SalesHead ──────────────────────────────────────────────────────────────

  public function paginate(int $perPage = 10)
  {
    return SalesHead::with(['cashier', 'details', 'payment'])
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
    $sales = $this->findById($id);
    $sales->update(['status' => 'cancelled']);
    return $sales;
  }

  public function recalculateTotals(int $salesHeadId): void
  {
    $sales        = $this->findById($salesHeadId);
    $totalAmount  = $sales->details->sum('subtotal');
    $grandTotal   = $totalAmount - $sales->discount_amount;

    $sales->update([
      'total_amount' => $totalAmount,
      'grand_total'  => max(0, $grandTotal),
    ]);
  }

  // ── SalesDetail ───────────────────────────────────────────────────────────

  public function findDetail(int $detailId): SalesDetail
  {
    return SalesDetail::findOrFail($detailId);
  }

  public function addDetail(int $salesHeadId, array $data): SalesDetail
  {
    return SalesDetail::create([
      'sales_head_id'   => $salesHeadId,
      'item_id'         => $data['item_id'],
      'qty'             => $data['qty'],
      'unit_price'      => $data['unit_price'],
      'discount_amount' => $data['discount_amount'] ?? 0,
      'subtotal'        => $data['subtotal'],
      'notes'           => $data['notes'] ?? null,
    ]);
  }

  public function updateDetail(int $detailId, array $data): SalesDetail
  {
    $detail = $this->findDetail($detailId);
    $detail->update($data);
    return $detail;
  }

  public function removeDetail(int $detailId): void
  {
    $this->findDetail($detailId)->delete();
  }

  // ── SalesPayment ──────────────────────────────────────────────────────────

  public function createPayment(int $salesHeadId, array $data): SalesPayment
  {
    return DB::transaction(function () use ($salesHeadId, $data) {
      $payment = SalesPayment::create([
        'sales_head_id'  => $salesHeadId,
        'payment_method' => $data['payment_method'],
        'amount_paid'    => $data['amount_paid'],
        'change_amount'  => $data['change_amount'] ?? 0,
        'paid_at'        => now(),
      ]);

      SalesHead::findOrFail($salesHeadId)
        ->update(['status' => 'paid']);

      return $payment;
    });
  }
}
