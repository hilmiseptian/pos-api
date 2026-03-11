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

    $company = $this->companyService->create([
      'name' => $data['company_name'],
      'type' => $data['company_type'],
    ]);

    $user = User::create([
      'name'       => $data['name'],
      'email'      => $data['email'],
      'password'   => Hash::make($data['password']),
      'company_id' => $company->id,
      'role'       => 'owner',  // owner bypasses all permissions
      'role_id'    => null,
    ]);

    $this->branchService->create([
      'company_id' => $company->id,
      'name'       => $data['branch_name'],
      'city'       => $data['branch_city'],
    ]);

    event(new Registered($user));

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
      'message' => 'Registration successful. Please verify your email.',
      'token'   => $token,
      'user'    => $this->userPayload($user),
    ], 201);
  }

  public function login(Request $request)
  {
    $data = $request->validate([
      'login'    => 'required|string',  // accepts email or username
      'password' => 'required|string',
    ]);

    // Detect whether input is email or username
    $field = filter_var($data['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

    $user = User::where($field, $data['login'])
      ->with('dynamicRole.permissions')
      ->first();

    if (!$user || !Hash::check($data['password'], $user->password)) {
      return response()->json(['message' => 'Invalid credentials.'], 401);
    }

    if (!$user->is_active) {
      return response()->json(['message' => 'Your account has been deactivated.'], 403);
    }

    // Non-superadmin/owner users must have at least one branch assigned
    if (!$user->isSuperAdmin() && !$user->isOwner() && $user->branches()->count() === 0) {
      return response()->json([
        'message' => 'Your account has not been assigned to a branch yet.',
      ], 403);
    }

    // Non-superadmin/owner users must have a dynamic role assigned
    if (!$user->isSuperAdmin() && !$user->isOwner() && !$user->role_id) {
      return response()->json([
        'message' => 'Your account has no role assigned. Contact your administrator.',
      ], 403);
    }

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
      'token'          => $token,
      'email_verified' => $user->hasVerifiedEmail(),
      'user'           => $this->userPayload($user),
    ]);
  }

  public function logout(Request $request)
  {
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'Logged out successfully.']);
  }

  // ── Helper ─────────────────────────────────────────────────────────────────

  private function userPayload(User $user): array
  {
    $user->load('company', 'dynamicRole.permissions');

    return [
      'id'             => $user->id,
      'name'           => $user->name,
      'email'          => $user->email,
      'role'           => $user->role,           // structural: superadmin/owner/admin/cashier
      'role_id'        => $user->role_id,
      'role_name'      => $user->dynamicRole?->name, // display name e.g. "Manager"
      'email_verified' => $user->hasVerifiedEmail(),
      'company_id'     => $user->company_id,
      'company'        => $user->company?->only(['id', 'name', 'type', 'code']),
      'permissions'    => $user->getPermissions(), // ['*'] or ['users.view', ...]
    ];
  }
}