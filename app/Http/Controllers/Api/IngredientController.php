<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    /**
     * Listar todos los ingredientes
     */
    public function index()
    {
        $ingredients = Ingredient::all();
        
        return response()->json([
            'ingredients' => $ingredients,
        ]);
    }

    /**
     * Crear un nuevo ingrediente
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:ingredients,name',
        ]);

        $ingredient = Ingredient::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'message' => 'Ingrediente creado exitosamente',
            'ingredient' => $ingredient,
        ], 201);
    }

    /**
     * Mostrar un ingrediente específico
     */
    public function show(string $id)
    {
        $ingredient = Ingredient::findOrFail($id);
        
        return response()->json([
            'ingredient' => $ingredient,
        ]);
    }

    /**
     * Actualizar un ingrediente
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:ingredients,name,' . $id,
        ]);

        $ingredient = Ingredient::findOrFail($id);
        $ingredient->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'message' => 'Ingrediente actualizado exitosamente',
            'ingredient' => $ingredient,
        ]);
    }

    /**
     * Eliminar un ingrediente
     */
    public function destroy(string $id)
    {
        $ingredient = Ingredient::findOrFail($id);
        $ingredient->delete();

        return response()->json([
            'message' => 'Ingrediente eliminado exitosamente',
        ]);
    }
}
