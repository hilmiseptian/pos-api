<?php

namespace App\Http\Controllers;

use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use App\Traits\ApiResponse;

class PermissionController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $permissions = Permission::orderBy('module')->orderBy('name')->get();

        $grouped = $permissions->groupBy('module')->map(function ($items, $module) {
            return [
                'module'      => $module,
                'permissions' => PermissionResource::collection($items)->resolve(),
            ];
        })->values();

        return response()->json(['data' => $grouped]);
    }
}