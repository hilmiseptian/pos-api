<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

// ── Public ─────────────────────────────────────────────────────────────────────
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// ── Email Verification ─────────────────────────────────────────────────────────
Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = User::findOrFail($id);

    if (!URL::hasValidSignature($request)) {
        return redirect(env('FRONTEND_URL') . '/verify-email?error=invalid-link');
    }
    if (!hash_equals(sha1($user->getEmailForVerification()), (string) $hash)) {
        return redirect(env('FRONTEND_URL') . '/verify-email?error=invalid-link');
    }
    if ($user->hasVerifiedEmail()) {
        return redirect(env('FRONTEND_URL') . '/pos?verified=already');
    }

    $user->markEmailAsVerified();
    return redirect(env('FRONTEND_URL') . '/pos?verified=1');
})->middleware('signed')->name('verification.verify');

// ── Protected ──────────────────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::delete('/logout', [AuthController::class, 'logout']);
    Route::get('/user', fn(Request $r) => $r->user()->load('company', 'dynamicRole.permissions'));

    // Email verification resend
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return response()->json(['message' => 'Verification email sent.']);
    })->middleware('throttle:6,1')->name('verification.send');

    // ── Permissions (read-only — seeded by system) ─────────────────────────
    Route::get('/permissions', [PermissionController::class, 'index'])
        ->middleware('permission:roles.view');

    // ── Roles (company-managed) ────────────────────────────────────────────
    Route::get('/roles/all', [RoleController::class, 'all']);
    Route::get('/roles',          [RoleController::class, 'index'])->middleware('permission:roles.view');
    Route::post('/roles',         [RoleController::class, 'store'])->middleware('permission:roles.create');
    Route::get('/roles/{id}',     [RoleController::class, 'show'])->middleware('permission:roles.view');
    Route::put('/roles/{id}',     [RoleController::class, 'update'])->middleware('permission:roles.edit');
    Route::delete('/roles/{id}',  [RoleController::class, 'destroy'])->middleware('permission:roles.delete');

    // ── Users ──────────────────────────────────────────────────────────────
    Route::get('/users',         [UserController::class, 'index'])->middleware('permission:users.view');
    Route::post('/users',        [UserController::class, 'store'])->middleware('permission:users.create');
    Route::get('/users/{id}',    [UserController::class, 'show'])->middleware('permission:users.view');
    Route::put('/users/{id}',    [UserController::class, 'update'])->middleware('permission:users.edit');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->middleware('permission:users.delete');

    // ── Items ──────────────────────────────────────────────────────────────
    Route::get('/items/all', [ItemController::class, 'all']);
    Route::get('/items',         [ItemController::class, 'index'])->middleware('permission:items.view');
    Route::post('/items',        [ItemController::class, 'store'])->middleware('permission:items.create');
    Route::get('/items/{id}',    [ItemController::class, 'show'])->middleware('permission:items.view');
    Route::put('/items/{id}',    [ItemController::class, 'update'])->middleware('permission:items.edit');
    Route::delete('/items/{id}', [ItemController::class, 'destroy'])->middleware('permission:items.delete');

    // ── Categories ─────────────────────────────────────────────────────────
    Route::get('/categories/all', [CategoryController::class, 'all']);
    Route::get('/categories',         [CategoryController::class, 'index'])->middleware('permission:categories.view');
    Route::post('/categories',        [CategoryController::class, 'store'])->middleware('permission:categories.create');
    Route::get('/categories/{id}',    [CategoryController::class, 'show'])->middleware('permission:categories.view');
    Route::put('/categories/{id}',    [CategoryController::class, 'update'])->middleware('permission:categories.edit');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->middleware('permission:categories.delete');

    // ── Sub Categories ─────────────────────────────────────────────────────
    Route::get('/subcategories',         [SubCategoryController::class, 'index'])->middleware('permission:subcategories.view');
    Route::post('/subcategories',        [SubCategoryController::class, 'store'])->middleware('permission:subcategories.create');
    Route::get('/subcategories/{id}',    [SubCategoryController::class, 'show'])->middleware('permission:subcategories.view');
    Route::put('/subcategories/{id}',    [SubCategoryController::class, 'update'])->middleware('permission:subcategories.edit');
    Route::delete('/subcategories/{id}', [SubCategoryController::class, 'destroy'])->middleware('permission:subcategories.delete');

    // ── Companies ──────────────────────────────────────────────────────────
    Route::get('/companies',         [CompanyController::class, 'index'])->middleware('permission:companies.view');
    Route::post('/companies',        [CompanyController::class, 'store'])->middleware('permission:companies.create');
    Route::get('/companies/{id}',    [CompanyController::class, 'show'])->middleware('permission:companies.view');
    Route::put('/companies/{id}',    [CompanyController::class, 'update'])->middleware('permission:companies.edit');
    Route::delete('/companies/{id}', [CompanyController::class, 'destroy'])->middleware('permission:companies.delete');

    // ── Branches ───────────────────────────────────────────────────────────
    Route::get('/branches',         [BranchController::class, 'index'])->middleware('permission:branches.view');
    Route::post('/branches',        [BranchController::class, 'store'])->middleware('permission:branches.create');
    Route::get('/branches/{id}',    [BranchController::class, 'show'])->middleware('permission:branches.view');
    Route::put('/branches/{id}',    [BranchController::class, 'update'])->middleware('permission:branches.edit');
    Route::delete('/branches/{id}', [BranchController::class, 'destroy'])->middleware('permission:branches.delete');

    // ── Employees ──────────────────────────────────────────────────────────
    Route::apiResource('employees', EmployeeController::class);

    // ── Orders / POS ───────────────────────────────────────────────────────
    Route::prefix('orders')->group(function () {
        Route::get('/',              [OrderController::class, 'index'])->middleware('permission:orders.view');
        Route::get('/open',          [OrderController::class, 'openOrders'])->middleware('permission:orders.view');
        Route::post('/',             [OrderController::class, 'store'])->middleware('permission:orders.create');
        Route::get('/{id}',          [OrderController::class, 'show'])->middleware('permission:orders.view');
        Route::patch('/{id}/cancel', [OrderController::class, 'cancel'])->middleware('permission:orders.edit');

        Route::post('/{id}/items',              [OrderController::class, 'addItem'])->middleware('permission:orders.edit');
        Route::put('/{id}/items/{detailId}',    [OrderController::class, 'updateItem'])->middleware('permission:orders.edit');
        Route::delete('/{id}/items/{detailId}', [OrderController::class, 'removeItem'])->middleware('permission:orders.edit');
        Route::post('/{id}/payment',            [OrderController::class, 'processPayment'])->middleware('permission:orders.payment');
    });
});