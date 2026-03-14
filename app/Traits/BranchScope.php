<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Scopes order queries by branch for cashiers, or by company for owners.
 * Apply this trait to: Order (SalesHead).
 */
trait BranchScope
{
  protected static function bootBranchOrderScope(): void
  {
    static::addGlobalScope('branch_order', function (Builder $query) {
      if (!auth()->check()) return;

      $user = auth()->user();

      if ($user->role === 'admin') {
        // Cashier: fixed to their branch
        $query->where('branch_id', $user->branch_id);
      } else {
        // Owner: all branches under their company
        $query->whereHas(
          'branch',
          fn($q) =>
          $q->where('company_id', $user->company_id)
        );
      }
    });

    // Auto-assign branch_id on create (cashier only)
    static::creating(function ($model) {
      if (auth()->check() && !$model->branch_id) {
        $user = auth()->user();
        if ($user->role === 'cashier') {
          $model->branch_id = $user->branch_id;
        }
      }
    });
  }
}
