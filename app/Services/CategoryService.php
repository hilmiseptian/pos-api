<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\CategoryRepository;

class CategoryService
{
  public function __construct(
    protected CategoryRepository $categoryRepository
  ) {}

  public function list()
  {
    return $this->categoryRepository->paginate();
  }

  public function listAll()
  {
    return $this->categoryRepository->getAll();
  }

  public function find(int $id): Category
  {
    return $this->categoryRepository->findById($id);
  }

  public function create(array $data): Category
  {
    $branchIds    = $data['branch_ids'] ?? [];
    $data['code'] = $this->generateCode($data['name']);
    unset($data['branch_ids']);

    return $this->categoryRepository->create($data, $branchIds);
  }

  public function update(Category $category, array $data): Category
  {
    $branchIds = $data['branch_ids'] ?? [];
    unset($data['branch_ids'], $data['code']); // code immutable after creation

    return $this->categoryRepository->update($category, $data, $branchIds);
  }

  public function delete(Category $category): bool
  {
    return $this->categoryRepository->delete($category);
  }

    // ── Code Generator ─────────────────────────────────────────────────────────

  /**
   * Generates a unique category code from the category name.
   *
   * Format: CAT-{SLUG}-{3-digit counter}
   * Example: "Food & Beverage" → CAT-FB-001
   *          "Electronics"     → CAT-ELE-001
   *          "Food & Beverage" → CAT-FB-002 (if first exists)
   */
  private function generateCode(string $name): string
  {
    $slug   = $this->makeSlug($name);
    $prefix = 'CAT-' . $slug . '-';

    $count = Category::withoutGlobalScopes()
      ->where('code', 'like', $prefix . '%')
      ->count();

    $next = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

    // Handle gaps from deletions
    while (Category::withoutGlobalScopes()->where('code', $prefix . $next)->exists()) {
      $next = str_pad((int)$next + 1, 3, '0', STR_PAD_LEFT);
    }

    return $prefix . $next;
  }

  /**
   * Multi-word: initials of each word, max 3 chars.
   * Single word: first 3 chars.
   *
   * "Food & Beverage" → FB  (skips &)
   * "Electronics"     → ELE
   * "Hot Drinks"      → HD
   */
  private function makeSlug(string $name): string
  {
    // Strip special chars, keep only letters/numbers/spaces
    $cleaned = preg_replace('/[^a-zA-Z0-9\s]/', '', $name);
    $words   = array_values(array_filter(preg_split('/\s+/', trim($cleaned))));

    if (count($words) >= 2) {
      $slug = implode('', array_map(
        fn($w) => strtoupper(substr($w, 0, 1)),
        array_slice($words, 0, 3)
      ));
    } else {
      $slug = strtoupper(substr($words[0] ?? 'CAT', 0, 3));
    }

    return $slug;
  }
}