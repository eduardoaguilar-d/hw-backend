<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Listar todos los productos
     */
    public function index()
    {
        $products = Product::with('defaultIngredients')->get();
        
        return response()->json([
            'products' => $products,
        ]);
    }

    /**
     * Crear un nuevo producto
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:hamburguesa,hotdog',
            'price' => 'required|numeric|min:0',
            'default_ingredients' => 'nullable|array',
            'default_ingredients.*' => 'exists:ingredients,id',
        ]);

        $product = Product::create([
            'name' => $request->name,
            'type' => $request->type,
            'price' => $request->price,
        ]);

        if ($request->has('default_ingredients')) {
            $product->defaultIngredients()->attach($request->default_ingredients);
        }

        $product->load('defaultIngredients');

        return response()->json([
            'message' => 'Producto creado exitosamente',
            'product' => $product,
        ], 201);
    }

    /**
     * Mostrar un producto específico
     */
    public function show(string $id)
    {
        $product = Product::with('defaultIngredients')->findOrFail($id);
        
        return response()->json([
            'product' => $product,
        ]);
    }

    /**
     * Actualizar un producto
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'string|max:255',
            'type' => 'in:hamburguesa,hotdog',
            'price' => 'numeric|min:0',
            'default_ingredients' => 'nullable|array',
            'default_ingredients.*' => 'exists:ingredients,id',
        ]);

        $product = Product::findOrFail($id);
        $product->update($request->only(['name', 'type', 'price']));

        if ($request->has('default_ingredients')) {
            $product->defaultIngredients()->sync($request->default_ingredients);
        }

        $product->load('defaultIngredients');

        return response()->json([
            'message' => 'Producto actualizado exitosamente',
            'product' => $product,
        ]);
    }

    /**
     * Eliminar un producto
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $product->defaultIngredients()->detach();
        $product->delete();

        return response()->json([
            'message' => 'Producto eliminado exitosamente',
        ]);
    }

    /**
     * Actualizar ingredientes por defecto de un producto
     */
    public function updateIngredients(Request $request, string $id)
    {
        $request->validate([
            'ingredients' => 'required|array',
            'ingredients.*' => 'exists:ingredients,id',
        ]);

        $product = Product::findOrFail($id);
        $product->defaultIngredients()->sync($request->ingredients);
        $product->load('defaultIngredients');

        return response()->json([
            'message' => 'Ingredientes por defecto actualizados exitosamente',
            'product' => $product,
        ]);
    }
}
