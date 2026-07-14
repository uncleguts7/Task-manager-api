<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $product = Product::all();
        return response()->json($product);
    }

    public function show(Product $product)
    {
        $product->load('categories');
        return response()->json($product);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_ids' => 'required|array',
        ]);

        $categoryIds = $validated['category_ids'];
        unset($validated['category_ids']);
        $product = Product::create($validated);
        $product->categories()->attach($categoryIds);
        
        return response()->json($product, 201);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'product_name' => 'sometimes|string',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
            'category_ids' => 'sometimes|array',
        ]);

        if($request->has('category_ids'))
        {
            $categoryIds = $validated['category_ids'];
            unset($validated['category_ids']);
            $product->update($validated);
            $product->categories()->sync($categoryIds);

            return response()->json($product, 200);    
        }else{
            $product->update($validated);
            return response()->json($product, 200);
        }
        
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(null, 204);
    }
}
