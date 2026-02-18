<?php

namespace App\Services;

use App\Models\Company;
use App\Repositories\CompanyRepository;

class CompanyService
{

  public function __construct(
    protected CompanyRepository $companyRepository
  ) {}

  public function list()
  {
    return $this->companyRepository->paginate();
  }

  public function detail(int $id)
  {
    return $this->companyRepository->findById($id);
  }

  public function create(array $data): Company
  {
    return $this->companyRepository->create($data);
  }

  public function update(Company $company, array $data): Company
  {
    return $this->companyRepository->update($company, $data);
  }

  public function delete(Company $company): bool
  {
    return $this->companyRepository->delete($company);
  }
}
