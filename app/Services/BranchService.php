<?php

namespace App\Services;

use App\Repositories\BranchRepository;

class BranchService
{
  public function __construct(
    protected BranchRepository $branchRepository
  ) {}

  public function list()
  {
    return $this->branchRepository->getAll();
  }

  public function find(int $id)
  {
    return $this->branchRepository->findById($id);
  }

  public function create(array $data)
  {
    $data['code'] = $this->generateCode($data['name']);
    $data['company_id'] = $data['company_id'] ?? auth()->user()->company_id;

    return $this->branchRepository->create($data);
  }

  public function update(int $id, array $data)
  {
    // code is intentionally excluded — immutable after creation
    return $this->branchRepository->update($id, $data);
  }

  public function delete(int $id)
  {
    return $this->branchRepository->delete($id);
  }

    // ── Code Generator ─────────────────────────────────────────────────────────

  /**
   * Generates a unique branch code from the branch name.
   *
   * Format: BRN-{SLUG}-{3-digit counter}
   * Example: "Jakarta Pusat" → BRN-JKP-001
   *          "Main Store"    → BRN-MST-001
   *          "Main Store"    → BRN-MST-002 (if first exists)
   */
  private function generateCode(string $name): string
  {
    $slug   = $this->makeSlug($name);
    $prefix = 'BRN-' . $slug . '-';

    $count = \App\Models\Branch::where('code', 'like', $prefix . '%')->count();
    $next  = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

    // Ensure uniqueness (edge case: deleted codes leave gaps)
    while (\App\Models\Branch::where('code', $prefix . $next)->exists()) {
      $next = str_pad((int)$next + 1, 3, '0', STR_PAD_LEFT);
    }

    return $prefix . $next;
  }

  /**
   * Converts a branch name to a short uppercase abbreviation.
   *
   * Strategy: take the first letter of each word, uppercase, max 3 chars.
   * "Jakarta Pusat"  → JKP  (J + first 2 consonants of next word)
   * "Main Store"     → MST
   * "HQ"             → HQ
   *
   * Fallback: first 3 chars uppercased if single word.
   */
  private function makeSlug(string $name): string
  {
    $words = preg_split('/\s+/', trim($name));

    if (count($words) >= 2) {
      // Multi-word: first letter of each word, max 3
      $slug = implode('', array_map(
        fn($w) => strtoupper(substr($w, 0, 1)),
        array_slice($words, 0, 3)
      ));
    } else {
      // Single word: first 3 letters
      $slug = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $words[0]), 0, 3));
    }

    return $slug;
  }
}