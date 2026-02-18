<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Services\CompanyService;
use Illuminate\Http\Request;

class CompanyController extends Controller
{

  public function __construct(
    protected CompanyService $companyService
  ) {}

  public function index()
  {
    return response()->json(
      $this->companyService->list()
    );
  }

  public function store(Request $request)
  {
    $data = $request->validate([
      'name' => 'required|string|max:255',
      'code' => 'required|string|max:100|unique:companies,code',
      'email' => 'nullable|email',
      'phone' => 'nullable|string|max:50',
      'address' => 'nullable|string',
      'logo' => 'nullable|string',
      'is_active' => 'boolean',
    ]);

    $company = $this->companyService->create($data);

    return response()->json([
      'message' => 'Company created successfully',
      'data' => $company,
    ], 201);
  }

  public function show(Company $company)
  {
    return response()->json(['data' => $company]);
  }

  public function update(Request $request, Company $company)
  {
    $data = $request->validate([
      'name' => 'sometimes|required|string|max:255',
      'code' => 'sometimes|required|string|max:100|unique:companies,code,' . $company->id,
      'email' => 'nullable|email',
      'phone' => 'nullable|string|max:50',
      'address' => 'nullable|string',
      'logo' => 'nullable|string',
      'is_active' => 'boolean',
    ]);

    $this->companyService->update($company, $data);

    return response()->json([
      'message' => 'Company updated successfully',
      'data' => $company,
    ]);
  }

  public function destroy(Company $company)
  {
    $this->companyService->delete($company);

    return response()->json([
      'message' => 'Company deleted successfully'
    ]);
  }
}
