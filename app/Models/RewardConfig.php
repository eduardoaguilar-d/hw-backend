<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RewardConfig extends Model
{
    protected $fillable = [
        'order_count',
        'discount_percentage',
        'is_active',
        'order',
    ];

    protected $casts = [
        'order_count' => 'integer',
        'discount_percentage' => 'decimal:2',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Obtener todas las recompensas activas ordenadas
     */
    public static function getActiveRewards()
    {
        return self::where('is_active', true)
            ->orderBy('order', 'asc')
            ->get();
    }

    /**
     * Obtener la última recompensa (mayor order)
     */
    public static function getLastReward()
    {
        return self::where('is_active', true)
            ->orderBy('order', 'desc')
            ->first();
    }

    /**
     * Obtener recompensa para un número específico de pedidos
     */
    public static function getRewardForOrderCount(int $orderCount)
    {
        return self::where('is_active', true)
            ->where('order_count', $orderCount)
            ->first();
    }
}
