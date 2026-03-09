<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Email Verification — public, secured by signed URL
|--------------------------------------------------------------------------
*/

Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {

    $user = User::findOrFail($id);

    // 1. Validate signed URL
    if (! URL::hasValidSignature($request)) {
        return redirect(env('FRONTEND_URL') . '/verify-email?error=invalid-link');
    }

    // 2. Validate hash matches user email
    if (! hash_equals(sha1($user->getEmailForVerification()), (string) $hash)) {
        return redirect(env('FRONTEND_URL') . '/verify-email?error=invalid-link');
    }

    // 3. Already verified — just redirect
    if ($user->hasVerifiedEmail()) {
        return redirect(env('FRONTEND_URL') . '/pos?verified=already');
    }

    // 4. Mark verified and redirect to app
    $user->markEmailAsVerified();

    return redirect(env('FRONTEND_URL') . '/pos?verified=1');
})->middleware('signed')->name('verification.verify');

/*
|--------------------------------------------------------------------------
| Protected Routes (Sanctum auth, no email verification gate)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // ── Auth ───────────────────────────────────────────────────────────────
    Route::delete('/logout', [AuthController::class, 'logout']);
    Route::get('/user', fn(Request $r) => $r->user()->load('company', 'branch'));

    // ── Resend verification email ──────────────────────────────────────────
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return response()->json(['message' => 'Verification email sent.']);
    })->middleware('throttle:6,1')->name('verification.send');
    // Users
    Route::apiResource('users', UserController::class);

    // ── Items ──────────────────────────────────────────────────────────────
    Route::get('/items/all', [ItemController::class, 'all']);
    Route::apiResource('items', ItemController::class);

    // ── Resources ──────────────────────────────────────────────────────────
    Route::apiResource('employees',     EmployeeController::class);
    Route::apiResource('categories',    CategoryController::class);
    Route::apiResource('subcategories', SubCategoryController::class);
    Route::apiResource('companies',     CompanyController::class);
    Route::apiResource('branches',      BranchController::class);

    // ── Orders ─────────────────────────────────────────────────────────────
    Route::prefix('orders')->group(function () {
        Route::get('/',              [OrderController::class, 'index']);
        Route::get('/open',          [OrderController::class, 'openOrders']);
        Route::post('/',             [OrderController::class, 'store']);
        Route::get('/{id}',          [OrderController::class, 'show']);
        Route::patch('/{id}/cancel', [OrderController::class, 'cancel']);

        Route::post('/{id}/items',              [OrderController::class, 'addItem']);
        Route::put('/{id}/items/{detailId}',    [OrderController::class, 'updateItem']);
        Route::delete('/{id}/items/{detailId}', [OrderController::class, 'removeItem']);
        Route::post('/{id}/payment',            [OrderController::class, 'processPayment']);
    });
});