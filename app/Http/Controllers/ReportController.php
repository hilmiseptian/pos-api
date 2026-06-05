<?php

namespace App\Http\Controllers;

use App\Http\Resources\SalesSummaryResource;
use App\Services\ReportService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
  use ApiResponse;

  public function __construct(
    protected ReportService $reportService
  ) {}

  public function salesSummary(Request $request)
  {
    $filters = $this->getFilters($request);
    $data    = $this->reportService->getSalesSummary($filters);

    return response()->json([
      'data' => [
        'rows'           => SalesSummaryResource::collection($data['rows']),
        'total_amount'   => $data['total_amount'],
        'total_discount' => $data['total_discount'],
        'total_grand'    => $data['total_grand'],
        'total_trx'      => $data['total_trx'],
      ],
    ]);
  }

  // update getFilters to include status
  private function getFilters(Request $request): array
  {
    return $request->validate([
      'date_from'  => 'required|date',
      'date_to'    => 'required|date|after_or_equal:date_from',
      'branch_id'  => 'nullable|exists:branches,id',
      'cashier_id' => 'nullable|exists:users,id',
      'status'     => 'nullable|in:open,paid,cancelled',
    ]);
  }

  public function salesByItem(Request $request)
  {
    $filters = $this->getFilters($request);
    $data = $this->reportService->getSalesByItem($filters);
    return $this->respondWithItem(
      new \Illuminate\Http\Resources\Json\JsonResource($data)
    );
  }

  public function paymentBreakdown(Request $request)
  {
    $filters = $this->getFilters($request);
    $data = $this->reportService->getPaymentBreakdown($filters);
    return $this->respondWithItem(
      new \Illuminate\Http\Resources\Json\JsonResource($data)
    );
  }

  public function salesByBranch(Request $request)
  {
    $filters = $this->getFilters($request);
    $data    = $this->reportService->getSalesByBranch($filters);
    return $this->respondWithItem(
      new \Illuminate\Http\Resources\Json\JsonResource($data)
    );
  }
}
