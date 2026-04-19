<?php

namespace App\Http\Controllers;

use App\Http\Resources\PermissionResource;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $roles = Role::with('permissions')
            ->where('company_id', auth()->user()->company_id)
            ->orderBy('name')
            ->paginate(10);

        return $this->respondWithList(RoleResource::collection($roles));
    }

    public function all()
    {
        $roles = Role::where('company_id', auth()->user()->company_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return $this->respondWithList(RoleResource::collection($roles));
    }

    public function show(int $id)
    {
        $role = Role::with('permissions')
            ->where('company_id', auth()->user()->company_id)
            ->findOrFail($id);

        return $this->respondWithItem(new RoleResource($role));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:100',
            'description'      => 'nullable|string|max:255',
            'is_active'        => 'boolean',
            'permission_ids'   => 'array',
            'permission_ids.*' => 'integer|exists:permissions,id',
        ]);

        $role = Role::create([
            'company_id'  => auth()->user()->company_id,
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active'   => $data['is_active'] ?? true,
        ]);

        if (!empty($data['permission_ids'])) {
            $role->permissions()->sync($data['permission_ids']);
        }

        return $this->respondWithItem(
            new RoleResource($role->load('permissions')),
            'Role created successfully',
            201
        );
    }

    public function update(Request $request, int $id)
    {
        $role = Role::where('company_id', auth()->user()->company_id)
            ->findOrFail($id);

        $data = $request->validate([
            'name'             => 'required|string|max:100',
            'description'      => 'nullable|string|max:255',
            'is_active'        => 'boolean',
            'permission_ids'   => 'array',
            'permission_ids.*' => 'integer|exists:permissions,id',
        ]);

        $role->update([
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active'   => $data['is_active'] ?? $role->is_active,
        ]);

        $role->permissions()->sync($data['permission_ids'] ?? []);

        return $this->respondWithItem(
            new RoleResource($role->load('permissions')),
            'Role updated successfully'
        );
    }

    public function destroy(int $id)
    {
        $role = Role::where('company_id', auth()->user()->company_id)
            ->findOrFail($id);

        if ($role->users()->exists()) {
            return response()->json([
                'message' => 'Cannot delete role with assigned users.',
            ], 422);
        }

        $role->permissions()->detach();
        $role->delete();

        return $this->respondWithMessage('Role deleted successfully');
    }
}