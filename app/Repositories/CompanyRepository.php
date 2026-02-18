<?php

namespace App\Repositories;

use App\Models\Company;

class CompanyRepository
{
  public function paginate(int $perPage = 10)
  {
    return Company::paginate($perPage);
  }

  public function find(int $id): Company
  {
    return Company::findOrFail($id);
  }

  public function create(array $data): Company
  {
    return Company::create($data);
  }

  public function update(Company $company, array $data): Company
  {
    $company->update($data);
    return $company;
  }

  public function delete(Company $company): bool
  {
    return $company->delete();
  }
}
