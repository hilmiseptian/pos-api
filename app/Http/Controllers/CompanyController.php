<?php

namespace App\Http\Controllers;

use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\Services\CompanyService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
  use ApiResponse;

  public function __construct(
    protected CompanyService $companyService
  ) {}

  public function index()
  {
    return $this->respondWithList(
      CompanyResource::collection($this->companyService->list())
    );
  }

  public function show($id)
  {
    $company = $this->companyService->find($id);

    return $this->respondWithItem(
      new CompanyResource($company)
    );
  }

  public function store(Request $request)
  {
    $data = $request->validate([
      'name'      => 'required|string|max:255',
      'code'      => 'required|string|max:100|unique:companies,code',
      'email'     => 'nullable|email',
      'phone'     => 'nullable|string|max:50',
      'address'   => 'nullable|string',
      'logo'      => 'nullable|string',
      'is_active' => 'boolean',
    ]);

    $company = $this->companyService->create($data);

    return $this->respondWithItem(
      new CompanyResource($company),
      'Company created successfully',
      201
    );
  }

  public function update(Request $request, Company $company)
  {
    $data = $request->validate([
      'name'      => 'sometimes|required|string|max:255',
      'code'      => 'sometimes|required|string|max:100|unique:companies,code,' . $company->id,
      'email'     => 'nullable|email',
      'phone'     => 'nullable|string|max:50',
      'address'   => 'nullable|string',
      'logo'      => 'nullable|string',
      'is_active' => 'boolean',
    ]);

    $company = $this->companyService->update($company, $data);

    return $this->respondWithItem(
      new CompanyResource($company),
      'Company updated successfully'
    );
  }

  public function destroy(Company $company)
  {
    $this->companyService->delete($company);

    return $this->respondWithMessage('Company deleted successfully');
  }
}