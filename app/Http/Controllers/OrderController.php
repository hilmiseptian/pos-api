<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderDetailResource;
use App\Http\Resources\OrderPaymentResource;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
  use ApiResponse;

  public function __construct(
    protected OrderService $orderService
  ) {}

  public function index()
  {
    return $this->respondWithList(
      OrderResource::collection($this->orderService->list())
    );
  }

  public function openOrders()
  {
    return $this->respondWithList(
      OrderResource::collection($this->orderService->openOrders())
    );
  }

  public function show(int $id)
  {
    return $this->respondWithItem(
      new OrderResource($this->orderService->detail($id))
    );
  }

  public function store(Request $request)
  {
    $data  = $request->validate(['notes' => 'nullable|string']);
    $order = $this->orderService->create(
      cashierId: $request->user()->id,
      notes: $data['notes'] ?? null,
    );

    return $this->respondWithItem(
      new OrderResource($order),
      'Order created successfully',
      201
    );
  }

  public function cancel(int $id)
  {
    $order = $this->orderService->cancel($id);

    return $this->respondWithItem(
      new OrderResource($order),
      'Order cancelled successfully'
    );
  }

  public function addItem(Request $request, int $id)
  {
    $data   = $request->validate([
      'item_id'         => 'required|exists:items,id',
      'qty'             => 'required|integer|min:1',
      'discount_amount' => 'nullable|numeric|min:0',
      'notes'           => 'nullable|string',
    ]);

    $detail = $this->orderService->addItem($id, $data);

    return $this->respondWithItem(
      new OrderDetailResource($detail),
      'Item added successfully',
      201
    );
  }

  public function updateItem(Request $request, int $id, int $detailId)
  {
    $data   = $request->validate([
      'qty'             => 'required|integer|min:1',
      'discount_amount' => 'nullable|numeric|min:0',
      'notes'           => 'nullable|string',
    ]);

    $detail = $this->orderService->updateItem($id, $detailId, $data);

    return $this->respondWithItem(
      new OrderDetailResource($detail),
      'Item updated successfully'
    );
  }

  public function removeItem(int $id, int $detailId)
  {
    $this->orderService->removeItem($id, $detailId);

    return $this->respondWithMessage('Item removed successfully');
  }

  public function processPayment(Request $request, int $id)
  {
    $data    = $request->validate([
      'payment_method' => 'required|in:cash,qris',
      'amount_paid'    => 'required|numeric|min:0',
    ]);

    $payment = $this->orderService->processPayment($id, $data);

    return $this->respondWithItem(
      new OrderPaymentResource($payment),
      'Payment processed successfully'
    );
  }
}