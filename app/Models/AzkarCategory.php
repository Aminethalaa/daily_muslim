<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AzkarCategory extends Model
{
    protected $guarded = [];

    public function items(): HasMany
    {
        return $this->hasMany(AzkarItem::class, 'category_id')->orderBy('sort');
    }
}
