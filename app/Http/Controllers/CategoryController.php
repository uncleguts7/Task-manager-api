<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $category = Category::all();
        return response()->json($category);
    }

    public function show(Category $category)
    {   
        $category->load('products');
        return response()->json($category);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_name'=> 'required|string',
            'description'=> 'nullable|string',
        ]);
        $category = Category::create($validated);

        return response()->json($category, 201);
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'category_name'=> 'sometimes|string',
            'description'=> 'sometimes|string',
        ]);
        $category->update($validated);

        return response()->json($category, 200);
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(null, 204);
    }
    
}

