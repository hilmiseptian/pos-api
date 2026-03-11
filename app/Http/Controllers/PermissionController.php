<?php

namespace App\Http\Controllers;

use App\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Return all permissions grouped by module.
     * Used by the role create/edit form to render checkboxes.
     */
    public function index()
    {
        $permissions = Permission::orderBy('module')->orderBy('name')->get();

        $grouped = $permissions->groupBy('module')->map(function ($items, $module) {
            return [
                'module'      => $module,
                'permissions' => $items->values(),
            ];
        })->values();

        return response()->json(['data' => $grouped]);
    }
}