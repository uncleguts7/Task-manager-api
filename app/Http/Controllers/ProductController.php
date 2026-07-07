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
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'category_ids' => 'required|array',
        ]);

        $categoryIds = $validated['category_ids'];
        unset($validated['category_ids']);
        $product = Product::create($validated);
        $product->categories()->attach($categoryIds);
        
        return response()->json($product);
    }
}
