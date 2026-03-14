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
    $data['code'] = $this->generateCode($data['name']);

    return $this->companyRepository->create($data);
  }

  public function update(Company $company, array $data): Company
  {
    // code is immutable after creation
    return $this->companyRepository->update($company, $data);
  }

  public function delete(Company $company): bool
  {
    return $this->companyRepository->delete($company);
  }

    // ── Code Generator ─────────────────────────────────────────────────────────

  /**
   * Generates a unique company code from the company name.
   *
   * Format: CMP-{SLUG}-{3-digit counter}
   * Example: "Toko Maju Jaya" → CMP-TMJ-001
   *          "PT Sinar Abadi" → CMP-PSA-001
   */
  private function generateCode(string $name): string
  {
    $slug   = $this->makeSlug($name);
    $prefix = 'CMP-' . $slug . '-';

    $count = Company::where('code', 'like', $prefix . '%')->count();
    $next  = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

    // Ensure uniqueness in case of gaps from deletions
    while (Company::where('code', $prefix . $next)->exists()) {
      $next = str_pad((int)$next + 1, 3, '0', STR_PAD_LEFT);
    }

    return $prefix . $next;
  }

  /**
   * Converts a company name to a short uppercase abbreviation.
   *
   * Multi-word: first letter of each word, max 3 chars.
   * Single word: first 3 letters.
   *
   * "Toko Maju Jaya"  → TMJ
   * "PT Sinar Abadi"  → PSA
   * "Apple"           → APP
   */
  private function makeSlug(string $name): string
  {
    $words = preg_split('/\s+/', trim($name));

    if (count($words) >= 2) {
      $slug = implode('', array_map(
        fn($w) => strtoupper(substr($w, 0, 1)),
        array_slice($words, 0, 3)
      ));
    } else {
      $slug = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $words[0]), 0, 3));
    }

    return $slug;
  }
}