<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use App\Traits\ApiResponse;

class DashboardController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    public function summary()
    {
        return $this->respondWithItem(
            new \Illuminate\Http\Resources\Json\JsonResource(
                $this->dashboardService->getSummary()
            )
        );
    }
}