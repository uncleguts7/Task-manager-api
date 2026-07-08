<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function addItem(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $cart = Cart::firstOrCreate(['user_id'=> $request->user()->id]);
        $cartItem = $cart->cartItems()->firstOrCreate(
            ['product_id'=> $request->product_id],
            ['quantity'=> 0]
        );
        $cartItem->increment('quantity');

        return response()->json($cartItem);
    }

    public function show(Request $request)
    {
        $cart = $request->user()->cart()->with('cartItems.product')->first();
        return response()->json($cart);
    }

    public function updateQuantity(Request $request, CartItem $cartItem)
    {
        $validated = $request->validate(['quantity'=> 'required|integer|min:1']);
        $userId = $request->user()->id;
        
        if($cartItem->cart->user_id === $userId)
        {
            $cartItem->update($validated);
            return response()->json($cartItem, 200);
        }else{
            return response()->json(['message'=> 'unauthorized!'], 403);
        }
    }

    public function removeItem(Request $request, CartItem $cartItem)
    {
        $userId = $request->user()->id;
        
        if($cartItem->cart->user_id === $userId)
        {
            $cartItem->delete();
            return response()->json(null, 204);
        }else{
            return response()->json(['message'=> 'unauthorized!'], 403);
        }
    }
}
