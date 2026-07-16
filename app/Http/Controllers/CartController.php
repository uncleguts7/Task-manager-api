<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function checkout(Request $request)
    {
        $userId = $request->user()->id;
        $cart = Cart::where('user_id',$userId)->with('cartItems.product')->first();

        if($cart === null)
        {
            return response()->json(['message'=> 'Null: there is no cart yet!'], 404);
            
        }elseif($cart->cartItems->isEmpty()){
            return response()->json(['message'=> 'Cannot checkout empty cart!'], 422);
        }

        $order = null;

        DB::transaction(function() use ($cart, $userId, &$order){
            $totalPrice = 0;

            foreach($cart->cartItems as $item)
            {
                $totalPrice += $item->quantity * $item->product->price;
            }

            $order = Order::create([
            'user_id'=> $userId,
            'status'=> 'processing',
            'total_price'=> $totalPrice,
            ]);

            foreach($cart->cartItems as $item)
            {
                OrderItem::create([
                    'order_id'=> $order->id,
                    'product_id'=> $item->product->id,
                    'price'=> $item->product->price,
                    'quantity'=> $item->quantity,
                ]);
            }

            $cart->cartItems->each(function($item) {
                $item->delete();
            });
        });

        $order->load('orderItems.product');
        return response()->json($order, 201);
    }
}
    