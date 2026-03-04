<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
  public function __construct(
    protected OrderService $salesService
  ) {}

  // ── SalesHead ──────────────────────────────────────────────────────────────

  public function index()
  {
    return response()->json(
      $this->salesService->list()
    );
  }

  public function openOrders()
  {
    return response()->json([
      'data' => $this->salesService->openOrders()
    ]);
  }

  public function store(Request $request)
  {
    $data = $request->validate([
      'notes' => 'nullable|string',
    ]);

    $sales = $this->salesService->create(
      cashierId: $request->user()->id,
      notes: $data['notes'] ?? null,
    );

    return response()->json([
      'message' => 'Order created successfully',
      'data'    => $sales,
    ], 201);
  }

  public function show(int $id)
  {
    return response()->json([
      'data' => $this->salesService->detail($id)
    ]);
  }

  public function cancel(int $id)
  {
    $sales = $this->salesService->cancel($id);

    return response()->json([
      'message' => 'Order cancelled successfully',
      'data'    => $sales,
    ]);
  }

  // ── SalesDetail ───────────────────────────────────────────────────────────

  public function addItem(Request $request, int $id)
  {
    $data = $request->validate([
      'item_id'         => 'required|exists:items,id',
      'qty'             => 'required|integer|min:1',
      'discount_amount' => 'nullable|numeric|min:0',
      'notes'           => 'nullable|string',
    ]);

    $detail = $this->salesService->addItem($id, $data);

    return response()->json([
      'message' => 'Item added successfully',
      'data'    => $detail,
    ], 201);
  }

  public function updateItem(Request $request, int $id, int $detailId)
  {
    $data = $request->validate([
      'qty'             => 'required|integer|min:1',
      'discount_amount' => 'nullable|numeric|min:0',
      'notes'           => 'nullable|string',
    ]);

    $detail = $this->salesService->updateItem($id, $detailId, $data);

    return response()->json([
      'message' => 'Item updated successfully',
      'data'    => $detail,
    ]);
  }

  public function removeItem(int $id, int $detailId)
  {
    $this->salesService->removeItem($id, $detailId);

    return response()->json([
      'message' => 'Item removed successfully',
    ]);
  }

  // ── SalesPayment ──────────────────────────────────────────────────────────

  public function processPayment(Request $request, int $id)
  {
    $data = $request->validate([
      'payment_method' => 'required|in:cash,qris',
      'amount_paid'    => 'required|numeric|min:0',
    ]);

    $payment = $this->salesService->processPayment($id, $data);

    return response()->json([
      'message' => 'Payment processed successfully',
      'data'    => $payment,
    ]);
  }
}
