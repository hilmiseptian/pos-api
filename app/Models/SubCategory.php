<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use CompanyScope;

    protected $fillable = [
        'company_id',  // ✅ add this
        'category_id',
        'name',
        'is_active'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
