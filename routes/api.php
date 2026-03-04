<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SubCategoryController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Protected Routes (Requires Sanctum Auth)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    // Authentication
    Route::delete('/logout', [AuthController::class, 'logout']);
    Route::get('/user', fn(Request $request) => $request->user());
    // Employees
    Route::apiResource('employees', EmployeeController::class);
    Route::get('/items/all', [ItemController::class, 'all']); // ← add this
    Route::apiResource('items', ItemController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('subcategories', SubCategoryController::class);
    Route::apiResource('companies', CompanyController::class);
    Route::apiResource('branches', BranchController::class);

    // ── Sales Head ─────────────────────────────────────────────────────────────
    Route::prefix('orders')->group(function () {
        Route::get('/',              [OrderController::class, 'index']);        // GET    /sales           (paginated all)
        Route::get('/open',          [OrderController::class, 'openOrders']);   // GET    /sales/open      (ongoing orders list)
        Route::post('/',             [OrderController::class, 'store']);        // POST   /sales           (create order)
        Route::get('/{id}',          [OrderController::class, 'show']);         // GET    /sales/{id}      (order detail)
        Route::patch('/{id}/cancel', [OrderController::class, 'cancel']);       // PATCH  /sales/{id}/cancel

        // ── Sales Detail ───────────────────────────────────────────────────────
        Route::post('/{id}/items',                [OrderController::class, 'addItem']);     // POST   /sales/{id}/items
        Route::put('/{id}/items/{detailId}',      [OrderController::class, 'updateItem']); // PUT    /sales/{id}/items/{detailId}
        Route::delete('/{id}/items/{detailId}',   [OrderController::class, 'removeItem']); // DELETE /sales/{id}/items/{detailId}

        // ── Sales Payment ──────────────────────────────────────────────────────
        Route::post('/{id}/payment', [OrderController::class, 'processPayment']); // POST /sales/{id}/payment
    });
});
