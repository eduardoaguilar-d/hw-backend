<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Ingredientes excluidos del producto
     */
    public function excludedIngredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'ingredient_order_item');
    }

    /**
     * Alias para mantener compatibilidad (deprecated)
     */
    public function ingredients(): BelongsToMany
    {
        return $this->excludedIngredients();
    }
}
