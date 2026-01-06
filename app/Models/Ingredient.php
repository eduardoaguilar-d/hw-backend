<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ingredient extends Model
{
    protected $fillable = [
        'name',
    ];

    public function orderItems(): BelongsToMany
    {
        return $this->belongsToMany(OrderItem::class, 'ingredient_order_item');
    }
}
