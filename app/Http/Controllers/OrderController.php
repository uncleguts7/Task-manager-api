<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $orders = Order::where('user_id',$userId)->with('orderItems.product')->get();

        return response()->json($orders, 200);
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);
        $order->load('orderItems.product');
    
        return response()->json($order);
    }
    
}
