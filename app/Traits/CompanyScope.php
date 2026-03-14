<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Automatically scopes all queries to the authenticated user's company.
 * Apply this trait to: Category, SubCategory, Item.
 */
trait CompanyScope
{
  protected static function bootCompanyScope(): void
  {
    static::addGlobalScope('company', function (Builder $query) {
      if (auth()->check() && auth()->user()->company_id) {
        $query->where(
          (new static)->getTable() . '.company_id',
          auth()->user()->company_id
        );
      }
    });

    // Auto-assign company_id on create
    static::creating(function ($model) {
      if (auth()->check() && !$model->company_id) {
        $model->company_id = auth()->user()->company_id;
      }
    });
  }
}
