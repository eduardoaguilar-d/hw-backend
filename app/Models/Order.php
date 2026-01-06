<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'status',
        'payment_method',
        'phone',
        'customer_name',
        'address',
        'delivery_type',
        'reward_applied',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'status' => 'string',
        'reward_applied' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Calcular el tiempo transcurrido desde que se recibió el pedido
     */
    public function getTimeSinceReceivedAttribute(): ?int
    {
        if (!$this->created_at) {
            return null;
        }
        return now()->diffInSeconds($this->created_at);
    }

    /**
     * Calcular el tiempo que duró en preparación
     */
    public function getPreparationTimeAttribute(): ?int
    {
        if (!$this->started_at) {
            return null;
        }
        
        $endTime = $this->completed_at ?? now();
        return $endTime->diffInSeconds($this->started_at);
    }

    /**
     * Calcular el tiempo total desde recepción hasta completado
     */
    public function getTotalTimeAttribute(): ?int
    {
        if (!$this->created_at) {
            return null;
        }
        
        $endTime = $this->completed_at ?? now();
        return $endTime->diffInSeconds($this->created_at);
    }

    /**
     * Obtener el número de pedidos completados por teléfono desde la última recompensa aplicada
     */
    public static function getCompletedOrdersCount(string $phone): int
    {
        // Obtener el último pedido donde se aplicó recompensa
        $lastRewardOrder = self::where('phone', $phone)
            ->where('reward_applied', true)
            ->orderBy('completed_at', 'desc')
            ->first();
        
        // Contar pedidos completados desde el último reward_applied (o todos si no hay ninguno)
        $query = self::where('phone', $phone)
            ->where('status', 'completed');
        
        if ($lastRewardOrder) {
            $query->where('completed_at', '>', $lastRewardOrder->completed_at);
        }
        
        return $query->count();
    }

    /**
     * Verificar si el cliente tiene derecho a descuento basado en recompensas dinámicas
     */
    public static function getDiscountForPhone(string $phone): ?array
    {
        $completedCount = self::getCompletedOrdersCount($phone);
        
        // Buscar recompensa activa para este número de pedidos
        $reward = \App\Models\RewardConfig::getRewardForOrderCount($completedCount);
        
        if ($reward) {
            return [
                'discount_percentage' => (float) $reward->discount_percentage,
                'order_count' => $reward->order_count,
                'is_last_reward' => self::isLastReward($reward),
            ];
        }
        
        return null;
    }

    /**
     * Verificar si una recompensa es la última
     */
    public static function isLastReward(\App\Models\RewardConfig $reward): bool
    {
        $lastReward = \App\Models\RewardConfig::getLastReward();
        return $lastReward && $lastReward->id === $reward->id;
    }

    /**
     * Obtener información de progreso hacia la siguiente recompensa
     */
    public static function getRewardProgress(string $phone): ?array
    {
        $completedCount = self::getCompletedOrdersCount($phone);
        $rewards = \App\Models\RewardConfig::getActiveRewards();
        
        // Encontrar la siguiente recompensa
        foreach ($rewards as $reward) {
            if ($completedCount < $reward->order_count) {
                $remaining = $reward->order_count - $completedCount;
                return [
                    'current' => $completedCount,
                    'target' => $reward->order_count,
                    'remaining' => $remaining,
                    'next_discount' => (float) $reward->discount_percentage,
                    'message' => "Completa {$remaining} pedido(s) más para obtener un {$reward->discount_percentage}% de descuento",
                ];
            }
        }
        
        return null;
    }
}
