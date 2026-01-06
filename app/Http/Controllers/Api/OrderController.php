<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Crear un nuevo pedido
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.excluded_ingredients' => 'nullable|array',
            'items.*.excluded_ingredients.*' => 'exists:ingredients,id',
            'payment_method' => 'nullable|in:efectivo,transferencia',
            'phone' => 'nullable|string',
            'customer_name' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'delivery_type' => 'nullable|in:domicilio,recoger',
        ]);

        DB::beginTransaction();
        try {
            $phone = $request->phone;
            $discountInfo = null;
            
            // Verificar descuento si hay teléfono
            if ($phone) {
                $discountInfo = Order::getDiscountForPhone($phone);
            }

            $order = Order::create([
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'phone' => $phone,
                'customer_name' => $request->customer_name,
                'address' => $request->address,
                'delivery_type' => $request->delivery_type ?? 'recoger',
                'reward_applied' => false,
            ]);

            foreach ($request->items as $item) {
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);

                // Guardar ingredientes excluidos
                if (isset($item['excluded_ingredients']) && is_array($item['excluded_ingredients'])) {
                    $orderItem->excludedIngredients()->attach($item['excluded_ingredients']);
                }
            }

            DB::commit();

            $order->load(['items.product.defaultIngredients', 'items.excludedIngredients']);

            $response = $this->formatOrderResponse($order);
            if ($discountInfo !== null) {
                $response['discount'] = $discountInfo['discount_percentage'];
                $response['discount_message'] = "¡Felicidades! Tienes un {$discountInfo['discount_percentage']}% de descuento por tus pedidos completados.";
            }

            return response()->json([
                'message' => 'Pedido creado exitosamente',
                'order' => $response,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al crear el pedido',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Listar todos los pedidos
     */
    public function index()
    {
        $orders = Order::with(['items.product.defaultIngredients', 'items.excludedIngredients'])
            ->orderBy('created_at', 'asc')
            ->get();

        $formattedOrders = $orders->map(function ($order) {
            return $this->formatOrderResponse($order);
        });

        return response()->json([
            'orders' => $formattedOrders,
        ]);
    }

    /**
     * Obtener el siguiente pedido pendiente
     */
    public function next()
    {
        $order = Order::with(['items.product.defaultIngredients', 'items.excludedIngredients'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->first();

        if (!$order) {
            return response()->json([
                'message' => 'No hay pedidos pendientes',
            ], 404);
        }

        return response()->json([
            'order' => $this->formatOrderResponse($order),
        ]);
    }

    /**
     * Actualizar el estado de un pedido
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,preparing,completed',
        ]);

        $order = Order::findOrFail($id);
        
        $updateData = ['status' => $request->status];
        
        // Actualizar started_at cuando cambia a "preparing"
        if ($request->status === 'preparing' && !$order->started_at) {
            $updateData['started_at'] = now();
        }
        
        // Actualizar completed_at cuando cambia a "completed"
        if ($request->status === 'completed' && !$order->completed_at) {
            $updateData['completed_at'] = now();
        }
        
        $order->update($updateData);
        $order->load(['items.product.defaultIngredients', 'items.excludedIngredients']);

        $response = $this->formatOrderResponse($order);
        
        // Si se completó el pedido y hay teléfono, verificar recompensas
        if ($request->status === 'completed' && $order->phone) {
            $completedCount = Order::getCompletedOrdersCount($order->phone);
            $response['completed_orders_count'] = $completedCount;
            
            // Verificar si alcanzó una recompensa
            $discountInfo = Order::getDiscountForPhone($order->phone);
            
            if ($discountInfo) {
                // Marcar que se aplicó recompensa en este pedido
                $order->update(['reward_applied' => true]);
                
                $response['reward_earned'] = [
                    'discount_percentage' => $discountInfo['discount_percentage'],
                    'order_count' => $discountInfo['order_count'],
                    'message' => "¡Felicidades! Has completado {$discountInfo['order_count']} pedidos. Tu próximo pedido tendrá un {$discountInfo['discount_percentage']}% de descuento.",
                ];
                
                // Si es la última recompensa, el contador se reinicia automáticamente
                if ($discountInfo['is_last_reward']) {
                    $response['reward_earned']['is_last_reward'] = true;
                    $response['reward_earned']['counter_reset_message'] = "Has alcanzado la última recompensa. El contador se ha reiniciado para comenzar un nuevo ciclo.";
                }
            }
        }

        return response()->json([
            'message' => 'Estado del pedido actualizado',
            'order' => $response,
        ]);
    }

    /**
     * Formatear la respuesta del pedido con información de tiempos
     */
    private function formatOrderResponse(Order $order): array
    {
        $orderArray = $order->toArray();
        
        // Formatear items con ingredientes por defecto y excluidos
        $orderArray['items'] = $order->items->map(function ($item) {
            $itemArray = $item->toArray();
            
            // Agregar ingredientes por defecto del producto
            if ($item->product && $item->product->defaultIngredients) {
                $itemArray['product']['default_ingredients'] = $item->product->defaultIngredients->map(function ($ingredient) {
                    return [
                        'id' => $ingredient->id,
                        'name' => $ingredient->name,
                    ];
                });
            }
            
            // Cambiar excluded_ingredients en lugar de ingredients
            $itemArray['excluded_ingredients'] = $item->excludedIngredients->map(function ($ingredient) {
                return [
                    'id' => $ingredient->id,
                    'name' => $ingredient->name,
                ];
            });
            
            // Mantener ingredients por compatibilidad (pero ahora representa excluidos)
            $itemArray['ingredients'] = $itemArray['excluded_ingredients'];
            
            return $itemArray;
        })->toArray();
        
        // Agregar tiempos calculados
        $orderArray['time_since_received'] = $order->time_since_received; // en segundos
        $orderArray['time_since_received_formatted'] = $this->formatTime($order->time_since_received);
        
        if ($order->started_at) {
            $orderArray['preparation_time'] = $order->preparation_time; // en segundos
            $orderArray['preparation_time_formatted'] = $this->formatTime($order->preparation_time);
        } else {
            $orderArray['preparation_time'] = null;
            $orderArray['preparation_time_formatted'] = null;
        }
        
        if ($order->completed_at) {
            $orderArray['total_time'] = $order->total_time; // en segundos
            $orderArray['total_time_formatted'] = $this->formatTime($order->total_time);
        } else {
            $orderArray['total_time'] = $order->total_time; // tiempo hasta ahora
            $orderArray['total_time_formatted'] = $this->formatTime($order->total_time);
        }
        
        // Agregar información de recompensas si hay teléfono
        if ($order->phone) {
            $completedCount = Order::getCompletedOrdersCount($order->phone);
            $orderArray['completed_orders_count'] = $completedCount;
            
            // Verificar si tiene descuento activo
            $discountInfo = Order::getDiscountForPhone($order->phone);
            
            if ($discountInfo) {
                $orderArray['rewards_status'] = [
                    'discount_active' => true,
                    'discount_percentage' => $discountInfo['discount_percentage'],
                    'order_count' => $discountInfo['order_count'],
                    'message' => "¡Tienes un {$discountInfo['discount_percentage']}% de descuento activo!",
                ];
            } else {
                // Mostrar progreso hacia la siguiente recompensa
                $progress = Order::getRewardProgress($order->phone);
                if ($progress) {
                    $orderArray['rewards_progress'] = $progress;
                } else {
                    // No hay más recompensas disponibles
                    $orderArray['rewards_status'] = [
                        'discount_active' => false,
                        'message' => "Has completado todas las recompensas disponibles.",
                    ];
                }
            }
        }
        
        return $orderArray;
    }

    /**
     * Formatear tiempo en segundos a formato legible
     */
    private function formatTime(?int $seconds): ?string
    {
        if ($seconds === null) {
            return null;
        }
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;
        
        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $secs);
        }
        
        return sprintf('%d:%02d', $minutes, $secs);
    }
}
