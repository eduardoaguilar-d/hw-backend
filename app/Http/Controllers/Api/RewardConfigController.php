<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RewardConfig;
use Illuminate\Http\Request;

class RewardConfigController extends Controller
{
    /**
     * Listar todas las recompensas
     */
    public function index()
    {
        $rewards = RewardConfig::orderBy('order', 'asc')->get();
        
        return response()->json([
            'rewards' => $rewards,
        ]);
    }

    /**
     * Crear una nueva recompensa
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_count' => 'required|integer|min:1',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
            'order' => 'required|integer|min:0',
        ]);

        $reward = RewardConfig::create($request->all());

        return response()->json([
            'message' => 'Recompensa creada exitosamente',
            'reward' => $reward,
        ], 201);
    }

    /**
     * Mostrar una recompensa específica
     */
    public function show(string $id)
    {
        $reward = RewardConfig::findOrFail($id);
        
        return response()->json([
            'reward' => $reward,
        ]);
    }

    /**
     * Actualizar una recompensa
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'order_count' => 'integer|min:1',
            'discount_percentage' => 'numeric|min:0|max:100',
            'is_active' => 'boolean',
            'order' => 'integer|min:0',
        ]);

        $reward = RewardConfig::findOrFail($id);
        $reward->update($request->all());

        return response()->json([
            'message' => 'Recompensa actualizada exitosamente',
            'reward' => $reward,
        ]);
    }

    /**
     * Eliminar una recompensa
     */
    public function destroy(string $id)
    {
        $reward = RewardConfig::findOrFail($id);
        $reward->delete();

        return response()->json([
            'message' => 'Recompensa eliminada exitosamente',
        ]);
    }
}
