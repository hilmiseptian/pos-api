<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')
            ->where('company_id', auth()->user()->company_id)
            ->orderBy('name')
            ->paginate(10);

        return response()->json(['data' => $roles]);
    }

    public function all()
    {
        $roles = Role::where('company_id', auth()->user()->company_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json(['data' => $roles]);
    }

    public function show(int $id)
    {
        $role = Role::with('permissions')
            ->where('company_id', auth()->user()->company_id)
            ->findOrFail($id);

        return response()->json(['data' => $role]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:100',
            'description'    => 'nullable|string|max:255',
            'is_active'      => 'boolean',
            'permission_ids' => 'array',
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

        return response()->json([
            'message' => 'Role created successfully',
            'data'    => $role->load('permissions'),
        ], 201);
    }

    public function update(Request $request, int $id)
    {
        $role = Role::where('company_id', auth()->user()->company_id)
            ->findOrFail($id);

        $data = $request->validate([
            'name'           => 'required|string|max:100',
            'description'    => 'nullable|string|max:255',
            'is_active'      => 'boolean',
            'permission_ids' => 'array',
            'permission_ids.*' => 'integer|exists:permissions,id',
        ]);

        $role->update([
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active'   => $data['is_active'] ?? $role->is_active,
        ]);

        $role->permissions()->sync($data['permission_ids'] ?? []);

        return response()->json([
            'message' => 'Role updated successfully',
            'data'    => $role->load('permissions'),
        ]);
    }

    public function destroy(int $id)
    {
        $role = Role::where('company_id', auth()->user()->company_id)
            ->findOrFail($id);

        // Prevent deletion if users are still assigned
        if ($role->users()->exists()) {
            return response()->json([
                'message' => 'Cannot delete role with assigned users. Reassign users first.',
            ], 422);
        }

        $role->permissions()->detach();
        $role->delete();

        return response()->json(['message' => 'Role deleted successfully']);
    }
}