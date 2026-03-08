<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\BranchService;
use App\Services\CompanyService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
  public function __construct(
    protected CompanyService $companyService,
    protected BranchService  $branchService
  ) {}

  // ── Register ───────────────────────────────────────────────────────────────

  public function register(Request $request)
  {
    $data = $request->validate([
      'name'         => 'required|string|max:255',
      'email'        => 'required|email|unique:users,email',
      'password'     => 'required|string|min:8|confirmed',
      'company_name' => 'required|string|max:255',
      'company_type' => 'required|string|max:100',
      'branch_name'  => 'required|string|max:255',
      'branch_city'  => 'required|string|max:255',
    ]);

    // 1. Create company via service — generates code automatically
    $company = $this->companyService->create([
      'name' => $data['company_name'],
      'type' => $data['company_type'],
    ]);

    // 2. Create owner user
    $user = User::create([
      'name'       => $data['name'],
      'email'      => $data['email'],
      'password'   => Hash::make($data['password']),
      'company_id' => $company->id,
      'branch_id'  => null,
      'role'       => 'owner',
    ]);

    // 3. Create first branch via service — generates code automatically
    $this->branchService->create([
      'company_id' => $company->id,
      'name'       => $data['branch_name'],
      'city'       => $data['branch_city'],
    ]);

    // 4. Fire verification email
    event(new Registered($user));

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
      'message' => 'Registration successful. Please verify your email.',
      'token'   => $token,
      'user'    => $this->userPayload($user),
    ], 201);
  }

  // ── Login ──────────────────────────────────────────────────────────────────

  public function login(Request $request)
  {
    $data = $request->validate([
      'email'    => 'required|email',
      'password' => 'required|string',
    ]);

    $user = User::where('email', $data['email'])->first();

    if (!$user || !Hash::check($data['password'], $user->password)) {
      return response()->json(['message' => 'Invalid credentials.'], 401);
    }

    if ($user->role === 'cashier' && !$user->branch_id) {
      return response()->json([
        'message' => 'Your account has not been assigned to a branch yet.',
      ], 403);
    }

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
      'token'          => $token,
      'email_verified' => $user->hasVerifiedEmail(),
      'user'           => $this->userPayload($user),
    ]);
  }

  // ── Logout ─────────────────────────────────────────────────────────────────

  public function logout(Request $request)
  {
    $request->user()->currentAccessToken()->delete();

    return response()->json(['message' => 'Logged out successfully.']);
  }

  // ── Helper ─────────────────────────────────────────────────────────────────

  private function userPayload(User $user): array
  {
    return [
      'id'             => $user->id,
      'name'           => $user->name,
      'email'          => $user->email,
      'role'           => $user->role,
      'email_verified' => $user->hasVerifiedEmail(),
      'company_id'     => $user->company_id,
      'branch_id'      => $user->branch_id,
      'company'        => $user->company?->only(['id', 'name', 'type', 'code']),
      'branch'         => $user->branch?->only(['id', 'name', 'city', 'code']),
    ];
  }
}