<?php

namespace App\Services;

use App\Models\Item;
use App\Repositories\OrderRepository;

class OrderService
{
  public function __construct(
    protected OrderRepository $salesRepository
  ) {}

  // ── SalesHead ──────────────────────────────────────────────────────────────

  public function list()
  {
    return $this->salesRepository->paginate();
  }

  public function openOrders()
  {
    return $this->salesRepository->getOpen();
  }

  public function detail(int $id)
  {
    return $this->salesRepository->findById($id);
  }

  public function create(int $cashierId, ?string $notes = null)
  {
    return $this->salesRepository->create([
      'cashier_id' => $cashierId,
      'notes'      => $notes,
    ]);
  }

  public function cancel(int $id)
  {
    return $this->salesRepository->cancel($id);
  }

  // ── SalesDetail ───────────────────────────────────────────────────────────

  public function addItem(int $salesHeadId, array $data)
  {
    // Snapshot the price from the item at time of sale
    $item      = Item::findOrFail($data['item_id']);
    $unitPrice = $item->selling_price;
    $discount  = $data['discount_amount'] ?? 0;
    $subtotal  = ($unitPrice * $data['qty']) - $discount;

    $detail = $this->salesRepository->addDetail($salesHeadId, [
      'item_id'         => $data['item_id'],
      'qty'             => $data['qty'],
      'unit_price'      => $unitPrice,
      'discount_amount' => $discount,
      'subtotal'        => max(0, $subtotal),
      'notes'           => $data['notes'] ?? null,
    ]);

    $this->salesRepository->recalculateTotals($salesHeadId);

    return $detail;
  }

  public function updateItem(int $salesHeadId, int $detailId, array $data)
  {
    $detail    = $this->salesRepository->findDetail($detailId);
    $unitPrice = $detail->unit_price;
    $discount  = $data['discount_amount'] ?? $detail->discount_amount;
    $subtotal  = ($unitPrice * $data['qty']) - $discount;

    $updated = $this->salesRepository->updateDetail($detailId, [
      'qty'             => $data['qty'],
      'discount_amount' => $discount,
      'subtotal'        => max(0, $subtotal),
      'notes'           => $data['notes'] ?? $detail->notes,
    ]);

    $this->salesRepository->recalculateTotals($salesHeadId);

    return $updated;
  }

  public function removeItem(int $salesHeadId, int $detailId)
  {
    $this->salesRepository->removeDetail($detailId);
    $this->salesRepository->recalculateTotals($salesHeadId);
  }

  // ── SalesPayment ──────────────────────────────────────────────────────────

  public function processPayment(int $salesHeadId, array $data)
  {
    $sales        = $this->salesRepository->findById($salesHeadId);
    $changeAmount = $data['payment_method'] === 'cash'
      ? max(0, $data['amount_paid'] - $sales->grand_total)
      : 0;

    return $this->salesRepository->createPayment($salesHeadId, [
      'payment_method' => $data['payment_method'],
      'amount_paid'    => $data['amount_paid'],
      'change_amount'  => $changeAmount,
    ]);
  }
}
