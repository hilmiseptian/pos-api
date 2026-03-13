<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function index()
    {
        return response()->json([
            'data' => $this->userService->list()
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'data' => $this->userService->find($id)
        ]);
    }

    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'username'   => ['required', 'string', 'max:50', Rule::unique('users', 'username')],
            'email'      => ['required', 'email', Rule::unique('users', 'email')],
            'phone'      => 'nullable|string|max:20',
            'password'   => 'required|string|min:8|confirmed',
            'role_id'    => [
                'required',
                'integer',
                Rule::exists('roles', 'id')->where('company_id', $companyId)->where('is_active', true),
            ],
            'is_active'  => 'boolean',
            'branch_ids' => 'required|array|min:1',
            'branch_ids.*' => [
                'integer',
                Rule::exists('branches', 'id')->where('company_id', $companyId),
            ],
        ]);

        $user = $this->userService->create($data);

        return response()->json([
            'message' => 'User created successfully',
            'data'    => $user,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $companyId = auth()->user()->company_id;
        $user      = $this->userService->find($id);

        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'username'   => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($user->id)],
            'email'      => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'phone'      => 'nullable|string|max:20',
            'password'   => 'nullable|string|min:8|confirmed',
            'role_id'    => [
                'required',
                'integer',
                Rule::exists('roles', 'id')->where('company_id', $companyId)->where('is_active', true),
            ],
            'is_active'  => 'boolean',
            'branch_ids' => 'required|array|min:1',
            'branch_ids.*' => [
                'integer',
                Rule::exists('branches', 'id')->where('company_id', $companyId),
            ],
        ]);

        $user = $this->userService->update($user, $data);

        return response()->json([
            'message' => 'User updated successfully',
            'data'    => $user,
        ]);
    }

    public function destroy($id)
    {
        $user = $this->userService->find($id);
        $this->userService->delete($user);

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }
}